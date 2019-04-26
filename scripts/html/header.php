<?php
  session_start();
  require "includes/dbh.inc.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="This is an example of a meta description. This will often show up in search results.">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>COMP424 Final Project</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>  
  
</head>
<body>
  <header>
    <nav class="nav-header-main">
      <a class="header-logo" href="index.php">
        <img src="img/logo.png" alt="Black Mesa">
      </a>
      <ul>
        <li><a href="index.php">Home</a></li>
        <?php
          if (!isset($_SESSION['userId'])) {
            echo '
              <li><a href="signup.php">Register</a></li>
              <li><a href="reset-password.php">Reset PW</a></li>
            ';
          }
          else if (isset($_SESSION['userId'])) {
            echo '
              <li><a href="#">Secret</a></li>
            ';
          }
        ?>
      </ul>
    </nav>
      <div class="header-login">
        <?php
        if (!isset($_SESSION['userId'])) {
        echo '<form action="includes/login.inc.php" method="post">
          <input type="text" name="mailuid" placeholder="E-mail/Username">
          <input type="password" name="pwd" placeholder="Password">
          <button type="submit" name="login-submit">Login</button>
        </form>';
      }
      else if (isset($_SESSION['userId'])) {
        echo '<form action="includes/logout.inc.php" method="post">
          <button type="submit" name="logout-submit">Logout</button>
        </form>';
      }
        ?>
      </div>

  </header>
