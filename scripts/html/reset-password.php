<?php
  require "header.php";
?>

    <main>
      <div class="wrapper-main">
        <section class="section-default">
          <h1>Reset your password</h1>
          <p>An e-mail will be send to you with instructions on how to reset your password.</p>
          <form class="form-resetpwd" action="includes/security-check.inc.php" method="post">
            <input type="text" name="email" placeholder="Enter you Email address...">
            <button type="submit" name="security-check-submit">Reset Password</button>
          </form>

          <?php
            if (isset($_GET["reset"])) {
              if ($_GET["reset"] == "success") {
                echo '<p class="signupsuccess">Check your e-mail!</p>';
              }
            }
            else if (isset($_GET["newpwd"])) {
              if ($_GET["newpwd"] == "passwordupdated") {
                echo '<p class="signupsuccess">Password updated!</p>';
              }
            }
            else if (isset($_GET["error"])) {
              if ($_GET["error"] == "wrongans") {
                echo '<p class="signuperror">The answer you provided does not match our records.</p>';
              }
            }
            else if (isset($_GET["error"])) {
              if ($_GET["error"] == "expired") {
                echo '<p class="signuperror">You need to re-submit your reset request.</p>';
              }
            }
            else if (isset($_GET["error"])) {
              if ($_GET["error"] == "invalidtoken") {
                echo '<p class="signuperror">Invalid token! You need to re-submit your reset request.</p>';
              }
            }
            else if (isset($_GET["error"])) {
              if ($_GET["error"] == "sqlerror") {
                echo '<p class="signuperror">Invalid token! You need to re-submit your reset request.</p>';
              }
            }
          ?>
        </section>
      </div>
    </main>

<?php
  require "footer.php";
?>
