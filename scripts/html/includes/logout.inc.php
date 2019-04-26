<?php

// destroy session to 
session_start();
session_unset();
session_destroy();
header("Location: ../index.php");
