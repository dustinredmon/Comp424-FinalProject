<?php
  require "header.php";
?>

    <main>
      <div class="wrapper-main">
        <section class="section-default">
          <h1>Answer Security Questions</h1>

          <form class="form-signup" action="includes/reset-request.inc.php" method="post">
            <input type="hidden" name="email" value="<?php echo $_SESSION["resetEmail"]; ?>">
            <p><?php   echo $_SESSION["sq1"];  ?></p>
            <input type="text" name="sa1" placeholder="Answer">
            <p><?php   echo $_SESSION["sq2"];  ?></p>
            <input type="text" name="sa2" placeholder="Answer">
            <button type="submit" name="reset-request-submit">Submit</button>
          </form>

          <?php
            if (isset($_SESSION["sq1"]) and isset($_SESSION["sq2"])) {
              unset($_SESSION['sq1']);
              unset($_SESSION['sq2']);
            }
            else {
              header("Location: reset-password.php?error=accessdenied");
              exit();
            }
          ?>

        </section>
      </div>
    </main>

<?php
  require "footer.php";
?>
