<?php
include "db_connect.php";
$user_role_id = $_SESSION['user_role_id'];
if($user_role_id!=1 ){ //onlu super admin is allowed to delete
	header('Location: list-users.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        $deleteQuery = "DELETE FROM email_templates WHERE id=$id";
        $deletestatement = mysqli_query($conn, $deleteQuery);
        
        if ($deletestatement) {
            // Construct the redirect URL with sortOrder and sortColumn parameters
            $redirectURL = 'manage-email.php';
            
            $_SESSION['template-deleted'] = true; //krna h ye pura
            // Redirect back to list-users.php with parameters
            header('Location: ' . $redirectURL);
            exit;
        } else {
            echo "Deletion failed: " . mysqli_error($conn);
        }
    }
} else {
    echo "Did not get ID parameter.";
}
