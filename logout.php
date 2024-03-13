<?php
session_start();
unset($_SESSION['id']); 
unset($_SESSION['name']);
unset($_SESSION['user_role_id']);
session_destroy();
header("Location: login.php");