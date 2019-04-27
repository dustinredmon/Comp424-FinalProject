<?php
  require "header.php";
?>

    <main>
      <div class="wrapper-main">
        <section class="section-default">
          <!--
          Content that is displayed depends if the user is logged in or not.
          -->
          <?php
          if (!isset($_SESSION['userId'])) {
            ?>

              <!--CONTENT FOR LOGGED OUT USERS GO HERE-->
              <!--CONTENT FOR LOGGED OUT USERS GO HERE-->

              <p class="login-status">You are logged out!</p>

              <!--CONTENT FOR LOGGED OUT USERS GO HERE-->
              <!--CONTENT FOR LOGGED OUT USERS GO HERE-->


            <?php
            if (isset($_GET["error"])) {
              if ($_GET["error"] == "emptyfields") {
                echo '<p class="signuperror">You left a login field empty!</p>';
              }
              if ($_GET["error"] == "wrongpwd") {
                echo '<p class="signuperror">You entered an incorrect password!</p>';
              }
              if ($_GET["error"] == "nouser") {
                echo '<p class="signuperror">You entered a Username or E-mail that does not exist!</p>';
              }
              if ($_GET["error"] == "sqlerror") {
                echo '<p class="signuperror">There was an error with the sql query</p>';
              }
              if ($_GET["error"] == "invalidtoken") {
                echo '<p class="signuperror">Unauthorized access!!</p>';
              }
              if ($_GET["error"] == "expired") {
                echo '<p class="signuperror">You entered a Username or E-mail that does not exist!</p>';
              }
            }
          }
          if (isset($_GET["signup"])) {
            if ($_GET["signup"] == "needactivation") {
              echo '<p class="signupsuccess">An activation link has been sent to your e-mail. Please click the link to activate your account.</p>';
            }
            if ($_GET["signup"] == "success") {
              echo '<p class="signupsuccess">Signup successful!</p>';
            }
          }else if (isset($_SESSION['userId']) and ($_SESSION['userStatus'] == "Active")) {
            ?>
	   <?php
		$userLOG = $_SESSION['userId'];
        	$sql = "SELECT login_countUsers FROM users WHERE idUsers='$userLOG'";
		$output= mysqli_query($conn, $sql);
        	$row = mysqli_fetch_assoc($output);
        	$login_count = $row['login_countUsers'];

		$sql = "SELECT firstUsers, lastUsers FROM users WHERE idUsers='$userLOG'";
		$qResult = mysqli_query($conn, $sql);
                
		if(mysqli_num_rows($qResult) > 0){
			while($row = mysqli_fetch_assoc($qResult)){
				$userName = $row["firstUsers"] . " " . $row["lastUsers"];
			}
		} else {
			echo "Error";
                }


                // Close connection
                mysqli_close($conn);

	   ?>
              <!--CONTENT FOR LOGGED IN USERS GO HERE-->
              <!--CONTENT FOR LOGGED IN USERS GO HERE-->

              <p class="login-status"><?php echo $userName ?> is logged in and activated!</p>

	      <p>You have logged in <?php echo $login_count ?> times.<p>
	      <p>Your last login was on <?php echo $last_login ?>.<p>
	      <p><a href="/secret/secret.txt" download>Download secret here</a></p>


              <!--CONTENT FOR LOGGED IN USERS GO HERE-->
              <!--CONTENT FOR LOGGED IN USERS GO HERE-->

            <?php
          }
          ?>
        </section>
      </div>
    </main>

<?php
  require "footer.php";
?>
