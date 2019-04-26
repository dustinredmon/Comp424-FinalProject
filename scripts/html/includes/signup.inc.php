<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

// Recaptcha verification
//if(isset($_POST['signup-submit'])){
//   
//    $privateKey = "6Ld9xZwUAAAAAEh09IBRViZ2W7LyALBfMA3fiDoK";
//	$responseKey = $_POST['g-recaptcha-response'];
//	$userIP = $_SERVER['REMOTE_ADDR'];
//	
//	$url = "https://www.google.com/recaptcha/api/siteverify?secret=$privateKey&response=$responseKey&remoteip=$userIP";
//
//    $response= file_get_contents($url);
//    $data = json_decode($response);
//	
//	echo $response;
//}


if (isset($_POST['signup-submit'])) {
	$privateKey = "6Ld9xZwUAAAAAEh09IBRViZ2W7LyALBfMA3fiDoK";
	$responseKey = $_POST['g-recaptcha-response'];
	$userIP = $_SERVER['REMOTE_ADDR'];

	$url = "https://www.google.com/recaptcha/api/siteverify?secret=".$privateKey."&response=".$responseKey."&remoteip=".$userIP;

    	$response= file_get_contents($url);
	$data = json_decode($response);

  if($data->success == true){

  // Connect to database using the database handler script
  require 'dbh.inc.php';

  //We grab all the data which we passed from the signup form so we can use it later.
  $username = $_POST['uid'];
  $email = $_POST['mail'];
  $first = $_POST['first'];
  $last = $_POST['last'];
  $sq1 = $_POST['sq1'];
  $sa1 = $_POST['sa1'];
  $sq2 = $_POST['sq2'];
  $sa2 = $_POST['sa2'];
  $bday = $_POST['bday'];
  $password = $_POST['pwd'];
  $passwordRepeat = $_POST['pwd-repeat'];

  // These variables will be used to check for errors submitted in the form by the user.
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number    = preg_match('@[0-9]@', $password);
  $specialChars = preg_match('@[^\w]@', $password);

  /* Then we perform a bit of error handling to make sure we catch any errors
  made by the user. Here you can add ANY error checks you might think of! I'm
  just checking for a few common errors feel free to add more.
  If we do run into an error we need to stop the rest of the script from running,
  and take the user back to the signup page with an error message. As an additional
  feature we will also send all the data back to the signup page, to make sure all the
  fields aren't empty and the user won't need to type it all in again. */

  // We check for any empty inputs.
  if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
    header("Location: ../signup.php?error=emptyfields&uid=".$username."&mail=".$email);
    exit();
  }
  // We check for an invalid username AND invalid e-mail.
  else if (!preg_match("/^[a-zA-Z0-9]*$/", $username) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../signup.php?error=invaliduidmail");
    exit();
  }
  // We check for an invalid username. In this case ONLY letters and numbers.
  else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
    header("Location: ../signup.php?error=invaliduid&mail=".$email);
    exit();
  }
  // We check for an invalid e-mail.
  else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../signup.php?error=invalidmail&uid=".$username);
    exit();
  }
  // We check if the repeated password is NOT the same.
  else if ($password !== $passwordRepeat) {
    header("Location: ../signup.php?error=passwordcheck&uid=".$username."&mail=".$email);
    exit();
  }
  //check if password contains an uppercase, lowercase, special  character, and proper length
  else if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
    header("Location: ../signup.php?error=invalidpassword&uid=".$username."&mail=".$email);
    exit();
  }
  else {

    /* We also need to include another error handler here that checks whether
    or the username is already taken. We HAVE to do this using prepared statements
    because it is safer!

    First we create the statement that searches our database table to check for
    any identical usernames. */
    $sql = "SELECT uidUsers FROM users WHERE uidUsers=?;";
    // We create a prepared statement.
    $stmt = mysqli_stmt_init($conn);
    // Then we prepare our SQL statement AND check if there are any errors with it.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      // If there is an error we send the user back to the signup page.
      header("Location: ../signup.php?error=sqlerror");
      exit();
    }
    else {
      /* Next we need to bind the type of parameters we expect to pass into the
      statement, and bind the data from the user.

      In case you need to know, "s" means "string", "i" means "integer", "b" means
      "blob", "d" means "double". */
      mysqli_stmt_bind_param($stmt, "s", $username);
      // Then we execute the prepared statement and send it to the database!
      mysqli_stmt_execute($stmt);
      // Then we store the result from the statement.
      mysqli_stmt_store_result($stmt);
      /* Then we get the number of result we received from our statement.
      This tells us whether the username already exists or not! */
      $resultCount = mysqli_stmt_num_rows($stmt);
      // Then we close the prepared statement!
      mysqli_stmt_close($stmt);
      // Here we check if the username exists.
      if ($resultCount > 0) {
        header("Location: ../signup.php?error=usertaken&mail=".$email);
        exit();
      }
      //Next we need to check if the email is already registered
      /* Next we need to bind the type of parameters we expect to pass into the
      statement, and bind the data from the user. */
      $sql = "SELECT emailUsers FROM users WHERE emailUsers=?;";
      // We create a prepared statement.
      $stmt = mysqli_stmt_init($conn);
      // Then we prepare our SQL statement AND check if there are any errors with it.
      if (!mysqli_stmt_prepare($stmt, $sql)) {
        // If there is an error we send the user back to the signup page.
        header("Location: ../signup.php?error=sqlerror");
        exit();
      }
      else {
        /* Next we need to bind the type of parameters we expect to pass into
        the statement, and bind the data from the user. In case you need to know,
        "s" means "string", "i" means "integer", "b" means "blob", "d" means "double". */
        mysqli_stmt_bind_param($stmt, "s", $email);
        // Then we execute the prepared statement and send it to the database!
        mysqli_stmt_execute($stmt);
        // Then we store the result from the statement.
        mysqli_stmt_store_result($stmt);
        /* Then we get the number of result we received from our statement.
        This tells us whether the username already exists or not! */
        $resultCount = mysqli_stmt_num_rows($stmt);
        // Then we close the prepared statement!
        mysqli_stmt_close($stmt);
        // Here we check if the username exists.
        if ($resultCount > 0) {
          // An account already exist with this email
          header("Location: ../signup.php?error=mailused&uid=".$username);
          exit();
        }
        else {
          // If we got to this point, it means the user didn't make an error! :)

          /* Next thing we do is to prepare the SQL statement that will insert
          the users info into the database. We HAVE to do this using prepared
          statements to make this process more secure. DON'T JUST SEND THE RAW
          DATA FROM THE USER DIRECTLY INTO THE DATABASE! */

          /* Prepared statements works by us sending SQL to the database first,
          and then later we fill in the placeholders (this is a placeholder -> ?)
          by sending the users data. */
          $sql = "INSERT INTO users (uidUsers, emailUsers, pwdUsers, firstUsers, lastUsers, sq1Users, sa1Users, sq2Users, sa2Users, bdayUsers) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
          // Here we initialize a new statement using the connection from the dbh.inc.php file.
          $stmt = mysqli_stmt_init($conn);
          // Then we prepare our SQL statement AND check if there are any errors with it.
          if (!mysqli_stmt_prepare($stmt, $sql)) {
            // If there is an error we send the user back to the signup page.
            header("Location: ../signup.php?error=sqlerror");
            exit();
          }
          else {

            // If there is no error then we continue the script!

            /* Before we send ANYTHING to the database we HAVE to hash the users
            password to make it un-readable in case anyone gets access to our
            database without permission! The hashing method I am using
            here, is the LATEST version and will always will be since it updates
            automatically. DON'T use md5 or sha256 to hash, these are old and
            outdated! */
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $hashedSa1 = password_hash($sa1, PASSWORD_DEFAULT);
            $hashedSa2 = password_hash($sa2, PASSWORD_DEFAULT);

            /* Next we need to bind the type of parameters we expect to pass
            into the statement, and bind the data from the user. */
            mysqli_stmt_bind_param($stmt, "ssssssssss", $username, $email, $hashedPwd, $first, $last, $sq1, $hashedSa1, $sq2, $hashedSa2, $bday);
            // Then we execute the prepared statement and send it to the database!
            // This means the user is now registered! :)
            mysqli_stmt_execute($stmt);


            //TODO: Send email here for account activation

            //Generate tokens using bin2hex and random_bytes
            $selector = bin2hex(random_bytes(8));
            $token = random_bytes(32);
            //Then we create the URL link which we will send the user by mail so they can reset their password.
            $url = "http://173.255.215.244/activate-account.php?selector=" . $selector . "&validator=" . bin2hex($token);
            /* Then we need to define when the tokens should expire. We do this for
            security reasons to make sure the same token can't be used for more than an hour.

            Then we set the timestamp and add another hour to the current time,
            and then pass it into the format we defined.*/
            $expires = date("U") + 1800;

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
              mysqli_stmt_bind_param($stmt, "s", $email);
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
              mysqli_stmt_bind_param($stmt, "ssss", $email, $selector, $hashedToken, $expires);
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
            $mail->addAddress($email);     // Add a recipient
            $mail->addReplyTo('no-reply@blackmesa.com', 'No-Reply');



            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Account Activation';

            $mail->Body    = '<p>Thank you for creating an account.
                              Please click the link below to activate your account.
                              If you did not make this request, you can ignore this email</p>
                              <p>Here is your activation link: </br> <a href="' . $url . '">' . $url . '</a></p>
                              ';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();




            // Lastly we send the user back to the signup page with a success message!
            header("Location: ../index.php?signup=needactivation");
            exit();
          }
        }
      }
    }
  }
  // Then we close the prepared statement and the database connection!
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
 }
 else {
	header("Location: ../signup.php");
	exit();
    } 
}
else {
  // If the user tries to access this page an inproper way, we send them back to the signup page.
  header("Location: ../signup.php");
  exit();
}
?>
