<?php
include "db_connect.php";
$user_role_id = $_SESSION['user_role_id'];
if($user_role_id!=1 && $user_role_id!=2){
	header('Location: list-users.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
        $deleteQuery = "DELETE FROM users WHERE id=$id";
        $deletestatement = mysqli_query($conn, $deleteQuery);
        
        if ($deletestatement) {
            // Construct the redirect URL with sortOrder and sortColumn parameters
            $redirectURL = 'list-users.php';
            if (!empty($_GET['query'])) {
                $redirectURL .= '?query=' . urlencode($_GET['query']);
            }
            else{
                $redirectURL .= '?query=' . urlencode('');
            }
            if (!empty($_GET['page'])) {
                $redirectURL .= '&page=' . urlencode($_GET['page']);
            }
            if (!empty($_GET['sortOrder'])) {
                $redirectURL .= '&order=' . urlencode($_GET['sortOrder']);
            }
            if (!empty($_GET['sortColumn'])) {
                $redirectURL .= '&sort=' . urlencode($_GET['sortColumn']);
            }
            $_SESSION['user-deleted'] = true;
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
