<?php

// check if the user got to this page by clicking the proper form button.
if (isset($_POST["security-check-submit"])) {

  // Store email sumbited through the form by the user
  $userEmail = $_POST["email"];
  // Connect to database using the database handler script
  require 'dbh.inc.php';

  // Retrive user security questions from the database, using prepared statements
  $sql = "SELECT * FROM users WHERE emailUsers=?;";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    // If there was an error connecting to the database
    header("Location: ../reset-password.php?error=sqlerror");
    exit();
  }
  else {
    // Connection was successful, continue query with prepared statements
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
      // Save security questions retrived from database in variables
      $questionOne = $row['sq1Users'];
      $questionTwo = $row['sq2Users'];
      // Start session to store security questions and email for security-check.php
      session_start();
      $_SESSION['sq1'] = $row['sq1Users'];
      $_SESSION['sq2'] = $row['sq2Users'];
      $_SESSION['resetEmail'] = $userEmail;
      mysqli_stmt_close($stmt);
      mysqli_close($conn);
      // Security questions and email stored successfully. Proceed tp secutiy check.
      header("Location: ../security-check.php?sq=success");
      exit();
    }
    else {
      // If there was an error connecting to the database
      header("Location: ../reset-password.php?error=sqlerror2");
      exit();
    }
  }
}
else {
  // If the user tries to access this page an inproper way, we send them back to the index page.
  header("Location: ../index.php");
  exit();
}
