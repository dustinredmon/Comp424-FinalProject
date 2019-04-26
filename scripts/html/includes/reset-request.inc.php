<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);


// First we check if the user arrived at this page using the proper form button
if (isset($_POST["reset-request-submit"])) {

  /* The first thing you should know about reset password scripts, is that we
  need to make it as secure as possible. To help do this we will be creating
  "tokens" to ensure that it is the correct user who tries to reset their password.

  Tokens are used to make sure it is the correct user that is trying to reset
  their password. I will explain more on this later.

  When we create the two tokens, we use random_bytes() and bin2hex(), which
  are build-in functions in PHP. random_bytes() generates cryptographically
  secure pseudo-random bytes, which we then convert to hexadecimal values so
  we can actually use it. Right now we are only going to use the bin2hex() on
  the "selector" because later we need to insert the "token" into the database
  in binary.

  Later we will also include these tokens into a link which we then send the
  user by mail so they can reset their password. */


  //Generate tokens using bin2hex and random_bytes
  $selector = bin2hex(random_bytes(8));
  $token = random_bytes(32);


  /* The reason we need to have a "selector" and a "token" is to prevent
  timing attacks, which is when we limit the speed at which a hacker can
  attempt to hack our script. I will get more into this later in the next script.

  Then we create the URL link which we will send the user by mail so they can
  reset their password.

  Notice that we convert the "token" to hexadecimals here as well,
  to make the URL usable. */

  $url = "http://173.255.215.244/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);

  /* Then we need to define when the tokens should expire. We do this for
  security reasons to make sure the same token can't be used for more than an hour.

  Then we set the timestamp and add another hour to the current time,
  and then pass it into the format we defined.*/
  $expires = date("U") + 1800;

  /* Next we delete any existing tokens that might be in the database.
  We don't want to fill up our database with unnecessary data we don't need anymore.

  First we need to get our database connection.*/
  require 'dbh.inc.php';


  // Then we grab the e-mail and security answers the user submitted from the form.
  $userEmail = $_POST["email"];
  $sa1 = $_POST["sa1"];
  $sa2 = $_POST["sa2"];


  // Retrive user security answers from the database, using prepared statements
  $sql = "SELECT * FROM users WHERE emailUsers=?;";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    // If there was an error connecting to the database
    header("Location: ../index.php?error=sqlerror");
    exit();
  }
  else {
    // Connection was successful, continue query with prepared statements
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      // Compare answer submitted by user with answer stored in the database
      $sa1Check = password_verify($sa1, $row['sa1Users']);
      $sa2Check = password_verify($sa2, $row['sa2Users']);
      if ($sa1Check == false) {
        // Answers submitted by the user does not match our records
        header("Location: ../reset-password.php?error=wrongans");
        exit();
      }
      else if ($sa1Check == true) {
        // User submitted the correct answers to thier security questions

        // We can now start the process to generate tokens to create a secure link to send in the email
        // Delete any user tokens from the database using prepared statements
        $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
          // If there was an error connecting to the database
          header("Location: ../reset-password.php?error=sqlerror");
          exit();
        }
        else {
          // Connection was successful. Delete tokens using prepared statments.
          mysqli_stmt_bind_param($stmt, "s", $userEmail);
          mysqli_stmt_execute($stmt);
        }

        /* Here we then insert the info we have regarding the token into the
        database. This means that we have something we can use to check if it is
        the correct user that tries to change their password. */

        // Insert generated tokens into the database using prepared statements.
        $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
          // If there was an error connecting to the database
          header("Location: ../reset-password.php?error=sqlerror");
          exit();
        }
        else {
          /*Connection was successful, contine to insert token using prepared statements

          Here we also hash the token to make it unreadable, in case a hacker
          accessess our database. */
          $hashedToken = password_hash($token, PASSWORD_DEFAULT);
          mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
          mysqli_stmt_execute($stmt);
        }

        // Here we close the statement and connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // Send e-mail to user with password reset link using PHPmailer

        $mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'novaprospekt.blackmesa@gmail.com';     // SMTP username
        $mail->Password   = 'HEROofTIME123!@#';                     // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('support@blackmesa.com', 'Black Mesa Support');
        $mail->addAddress($userEmail);     // Add a recipient
        $mail->addReplyTo('no-reply@blackmesa.com', 'No-Reply');



        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Password reset request.';

        $mail->Body    = '<p>We recieved a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email</p> <p>Here is your password reset link: </br> <a href="' . $url . '">' . $url . '</a></p>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();

        // Finally we send them back to a page telling them to check their e-mail.
        header("Location: ../reset-password.php?reset=success");
      }
    }
  }
}
else {
  // If the user tries to access this page an inproper way, we send them back to the index page.
  header("Location: ../index.php");
  exit();
}
