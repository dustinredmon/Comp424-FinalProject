<?php
  require "header.php";

// Recaptcha verification
// if(isset($_POST["signup-submit"])){
//	//$url = "https://www.google.com/recaptcha/api/siteverify";
//	//$privatekey = "6Ld9xZwUAAAAAEh09IBRViZ2W7LyALBfMA3fiDoK";
//
//	//$response = file_get_contents($url."?secret=".$privatekey."&response=".$_POST["g-recaptcha-response"]."&remoteip=".$_SERVER["REMOTE_ADDR"]);
//	//$data = json_decode($response);
//
//	//if(isset($data->success AND $data->success==true)){
//		
//	//} else {
//
//	}
//}
?>

    <main>
      <div class="wrapper-main">
        <section class="section-default">
          <h1>Create an account</h1>
          <?php
          // Here we create an error message if the user made an error trying to sign up.
          if (isset($_GET["error"])) {
            if ($_GET["error"] == "emptyfields") {
              echo '<p class="signuperror">Fill in all fields!</p>';
            }
            else if ($_GET["error"] == "invaliduidmail") {
              echo '<p class="signuperror">Invalid username and e-mail!</p>';
            }
            else if ($_GET["error"] == "invaliduid") {
              echo '<p class="signuperror">Invalid username!</p>';
            }
            else if ($_GET["error"] == "invalidmail") {
              echo '<p class="signuperror">Invalid e-mail!</p>';
            }
            else if ($_GET["error"] == "passwordcheck") {
              echo '<p class="signuperror">Your passwords do not match!</p>';
            }
            else if ($_GET["error"] == "invalidpassword") {
              echo '<p class="signuperror">Invalid Password! Password must
              be at least 8 characters in length, and contain at least an uppercase,
              lowercase, number , and symbol.</p>';
            }
            else if ($_GET["error"] == "usertaken") {
              echo '<p class="signuperror">Username is already taken!</p>';
            }
            else if ($_GET["error"] == "mailused") {
              echo '<p class="signuperror">This E-mail is already registered to another account!</p>';
            }
          }
          // Here we create a success message if the new user was created.
          else if (isset($_GET["signup"])) {
            if ($_GET["signup"] == "success") {
              echo '<p class="signupsuccess">Signup successful!</p>';
            }
          }
          ?>
          <form class="form-signup"action="includes/signup.inc.php" method="post">
            <?php
            // Here we check if the user already tried submitting data.

            // We check username.
            if (!empty($_GET["uid"])) {
              echo '<input type="text" name="uid" placeholder="Username" value="'.$_GET["uid"].'">';
            }
            else {
              echo '<input type="text" name="uid" placeholder="Username">';
            }

            // We check e-mail.
            if (!empty($_GET["mail"])) {
              echo '<input type="text" name="mail" placeholder="E-mail" value="'.$_GET["mail"].'">';
            }
            else {
              echo '<input type="text" name="mail" placeholder="E-mail">';
            }
            ?>


            <input type="text" name="first" placeholder="First Name">
            <input type="text" name="last" placeholder="Last Name">

            Security Question 1:
            <select name="sq1">
              <option value="What was your first car?">What was your first car?</option>
              <option value="What primary school did you attend?">What primary school did you attend?</option>
              <option value="In what town or city was your first full time job?">In what town or city was your first full time job?</option>
            </select>

            <input type="text" name="sa1" placeholder="Answer">

            Security Question 2:
            <select name="sq2">
              <option value="In what town or city did you meet your spouse/partner?">In what town or city did you meet your spouse/partner?</option>
              <option value="What is the middle name of your oldest child?">What is the middle name of your oldest child?</option>
              <option value="In what town or city did your mother and father meet?">In what town or city did your mother and father meet?</option>
            </select>

            <input type="text" name="sa2" placeholder="Answer">
            Birthday:
            <input type="date" name="bday" placeholder="Birthday">
            <input type="password" name="pwd" id="password" placeholder="Password">
	    <meter max="4" id="password-strength-meter"></meter>
	    <p id="password-strength-text"></p>
            <input type="password" name="pwd-repeat" placeholder="Repeat Password">
            <div class="g-recaptcha" data-sitekey="6Ld9xZwUAAAAAEaHhmwqVSS7O44Fw-HB8h52vBQV"></div>
           <br>
	   <br> 
	   <button type="submit" id="signup-submit" name="signup-submit">Register</button>
          </form>

          <!--Here we create the form which starts the password recovery process!-->
          <?php
          if (isset($_GET["newpwd"])) {
            if ($_GET["newpwd"] == "passwordupdated") {
              echo '<p class="signupsuccess">Your password has been reset!</p>';
            }
          }
          ?>

          <a class="p-forgetpwd" href="reset-password.php">Forgot your password?</a>

        </section>
      </div>
    </main>
   <script type="text/javascript" src="pwstr.js"></script>
   <script type="text/javascript" src="validate.js"></script>
<?php
  require "footer.php";
?>
