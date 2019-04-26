<?php

//This page is the database handler.
//The database handler is used to login and connect to the database

$servername = "localhost";
$dBUsername = "link";
$dBPassword = "hero12#$";
$dBName = "nova_prospekt";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

if (!$conn) {
  die("Connection failed: ".mysqli_connect_error());
}
