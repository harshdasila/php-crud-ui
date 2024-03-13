<?php
session_start();

// Check if the user is already logged in
if(isset($_SESSION['id'])) {
    // Redirect the user to the dashboard page since they are already logged in
    header("Location: list-users.php"); // Change 'dashboard.php' to the appropriate page
    exit();
} else {
    // Redirect the user to the login page
    header("Location: login.php");
    exit();
}
