<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "Pass@123";

// Create connection
$conn = new mysqli($servername, $username, $password,'root');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
if (isset($_SESSION["id"])) {
    header("location: list-users.php");
  }
  