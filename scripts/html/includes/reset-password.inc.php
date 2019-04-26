<?php

// First we check user arrived at this page using the correct form button.
if (isset($_POST['reset-password-submit'])) {

  // Here we grab the data from the form.
  $selector = $_POST['selector'];
  $validator = $_POST['validator'];
  $password = $_POST['pwd'];
  $passwordRepeat = $_POST['pwd-repeat'];

  if (empty($password) || empty($passwordRepeat)) {
    header("Location: ../create-new-password.php?selector=" . $selector . "&validator=" . $validator . "&error=emptyfields");
    exit();
  } else if ($password != $passwordRepeat) {
    header("Location: ../create-new-password.php?selector=" . $selector . "&validator=" . $validator . "&error=pwdnotsame");
    exit();
  }

  // We get the current date and time.
  $currentDate = date('U');

  // Connect to the database using the database handler script.
  require 'dbh.inc.php';

  /* Next we need to get the token from our database.

  When we look up our token we do it using the selector we created.

  Now let me explain why we have the "selector" in the first place before we continue, because this is an important security step that you need to understand.

  IF we had JUST created the "token" and NOT included the "selector", then we could have made this "retreat password" script simpler by just looking up the token in the bellow SQL query.

  However because we seperate the look-up (selector) and the validator (token), we prevent what is called timing attacks. Hense the reason we use the selector to grab the correct token from the database. */

  //Retrive token from database using prepared statements
  $sql = "SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >= $currentDate";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    // If there was an error connecting to the database
    header("Location: ../reset-password.php?error=sqlerror");
    exit();
  }
  else {
    // Connection was successful, continue query with prepared statements
    mysqli_stmt_bind_param($stmt, "s", $selector);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$row = mysqli_fetch_assoc($result)) {
      // The users token has expired
      header("Location: ../reset-password.php?error=expired");
      exit();
    }
    else {

      // Now we need to check if the token from the URL matches the token from the database.

      // First we convert the "token" from the URL back into binary.
      $tokenBin = hex2bin($validator);

      // Then we check if it matches the one from the database.
      $tokenCheck = password_verify($tokenBin, $row['pwdResetToken']);

      // Then if they match we grab the users e-mail from the database.
      if ($tokenCheck === false) {
        header("Location: ../reset-password.php?error=invalidtoken");
        exit();
      }
      elseif ($tokenCheck === true) {

        // Before we get the users info from the user table we need to store the token email for later.
        $tokenEmail = $row['pwdResetEmail'];

        // Here we query the user table to check if the email we have in our pwdReset table exists.
        // Retrive user email from the database, using prepared statements
        $sql = "SELECT * FROM users WHERE emailUsers=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
          // There was an error connecting to the database
          header("Location: ../reset-password.php?error=invalidtoken");
          exit();
        }
        else {
          // Connection was successful, continue query with prepared statements
          mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
          if (!$row = mysqli_fetch_assoc($result)) {
            // Token doesnt exist with that email
            header("Location: ../reset-password.php?error=invalidtoken");
            exit();
          }
          else {

            // Finally we update the users table with the newly created password.
            // Update user password from the database, using prepared statements
            $sql = "UPDATE users SET pwdUsers=? WHERE emailUsers=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
              // If there was an error connecting to the database
              header("Location: ../reset-password.php?error=sqlerror");
              exit();
            }
            else {
              // Connection was successful, continue query with prepared statements
              $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
              mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail);
              mysqli_stmt_execute($stmt);

              // Then we delete any leftover tokens from the pwdReset table.
              // Delete user token from the database, using prepared statements
              $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?";
              $stmt = mysqli_stmt_init($conn);
              if (!mysqli_stmt_prepare($stmt, $sql)) {
                // If there was an error connecting to the database
                header("Location: ../reset-password.php?error=sqlerror");
                exit();
              }
              else {
                // Connection was successful, continue query with prepared statements
                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                mysqli_stmt_execute($stmt);
                header("Location: ../reset-password.php?newpwd=passwordupdated");
              }
            }
          }
        }
      }
    }
  }
}
else {
  // If the user tries to access this page an inproper way, we send them back to the index page.
  header("Location: ../index.php");
  exit();
}
