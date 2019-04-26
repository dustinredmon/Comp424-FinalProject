<?php
  require 'header.php';
?>

<main>
  <div class="wrapper-main">
    <section class="section-default">

      <?php
      // First we grab the tokens from the URL.
      $selector = $_GET['selector'];
      $validator = $_GET['validator'];

      // Then we check if the tokens are here.
      if (empty($selector) || empty($validator)) {
        echo "Could not validate your request!";
      } else {
        // Here we check if all characters in our tokens are hexadecimal 'digits'.
        //This is a boolean. Again another error check to make sure the URL wasn't changed by the user.
        // If this check returns "true", we show the form that the user uses to reset their password.
        if (ctype_xdigit( $selector ) !== false && ctype_xdigit( $validator ) !== false) {
          ?>

          <p>Click the button to activate your account.</p>

          <form class="form-signup" action="includes/activate-account.inc.php" method="post">
            <input type="hidden" name="selector" value="<?php echo $selector; ?>">
            <input type="hidden" name="validator" value="<?php echo $validator; ?>">
            <button type="submit" name="activate-account-submit">Activate Account</button>
          </form>




<?php
        }
      }
?>

    </section>
  </div>
</main>

<?php
  require 'footer.php';
?>
