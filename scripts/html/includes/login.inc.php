<?php

// check if the user got to this page by clicking the proper login button.
if (isset($_POST['login-submit'])) {

  // Connect to database using the database handler script
  require 'dbh.inc.php';

  // Store data passed through from the signup form
  $mailuid = $_POST['mailuid'];
  $password = $_POST['pwd'];
  $uip = $_SERVER['REMOTE_ADDR'];
  // Check for Errors made by user
  // Check for empty inputs
  if (empty($mailuid) || empty($password)) {
    header("Location: ../index.php?error=emptyfields");
    exit();
  }
  else {

    //If the user did not make errors, then okay to verify password

    //Start of query using prepared statments
    // Retrive user password from the database, using prepared statements
    $sql = "SELECT * FROM users WHERE uidUsers=? OR emailUsers=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      // If there was an error connecting to the database
      header("Location: ../index.php?error=sqlerror");
      exit();
    }
    else {
      // Connection was successful, continue query with prepared statements
      mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      if ($row = mysqli_fetch_assoc($result)) {
        // User exists, use the result to verify password submitted by the user
        $pwdCheck = password_verify($password, $row['pwdUsers']);
        if ($pwdCheck == false) {
          // The user entered the wrong password!
	  // Add failed attempt to login_history
	  $sql = "INSERT INTO login_history (uidUsers, userIP, success) VALUES ('$mailuid', '$uip', 'no')";
          mysqli_query($conn, $sql);


          header("Location: ../index.php?error=wrongpwd");
          exit();
        }
        else if ($pwdCheck == true) {
          // The user entered the correct PASSWORD_DEFAULT
          // Start session to store userID and username in SESSION
          session_start();
          $_SESSION['userId'] = $row['idUsers'];
          $_SESSION['userUid'] = $row['uidUsers'];
          $_SESSION['userStatus'] = $row['statusUsers'];

	  // Store successful login atempt in login_history
	  $sql = "INSERT INTO login_history (uidUsers, userIP, success) VALUES ('$mailuid', '$uip', 'yes')";
	  mysqli_query($conn, $sql);

	  // query last_login and store in SESSION
          $sql = "SELECT last_loginUsers FROM users WHERE uidUsers='$mailuid' or emailUsers='$mailuid'";
          $output = mysqli_query($conn, $sql);
          $row = mysqli_fetch_assoc($output);
          $last_login = $row['last_login'];
          $_SESSION["last_login"] = "$last_login";

	  // Update login_count if login successful
          $sql = "UPDATE users SET login_countUsers=login_countUsers + 1 WHERE uidUsers='$mailuid' or emailUsers='$mailuid'";
          mysqli_query($conn, $sql);

          // Close connection
          mysqli_close($conn);
	
          header("Location: ../index.php?login=success");
          exit();
        }
      }
      else {
        //user does not exist
	//Add failed login attempt
	$sql = "INSERT INTO login_history (uidUsers, userIP, success) VALUES ('$mailuid', '$uip', 'no')";
        mysqli_query($conn, $sql);

        header("Location: ../index.php?error=nouser");
        exit();
      }
    }
    // End of database query using prepared statements


  }
  // Then we close the prepared statement and the database connection!
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
else {
  // If the user tries to access this page an inproper way, we send them back to the index page.
  header("Location: ../index.php");
  exit();
}
