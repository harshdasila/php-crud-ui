<?php
include "db_connect.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM php_crud_messages WHERE id=$id";
    $deletestatement = mysqli_query($conn, $deleteQuery);

    if ($deletestatement) {
        // Construct the redirect URL
        $redirectURL = 'manage-contact.php';
        $_SESSION['message-deleted'] = true;
        header('Location: ' . $redirectURL);
        exit;
    } else {
        echo "Deletion failed: " . mysqli_error($conn);
    }
}
else{
    echo "didn't got the id ";
    die();
}