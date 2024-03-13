<?php
include 'db_connect.php';
$user_role_id = $_SESSION['user_role_id'];

$recordsPerPage = 8; // Number of records to display per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1; // Current page number
$startFrom = ($page - 1) * $recordsPerPage;

$totalRecordsQuery = "SELECT COUNT(*) as total FROM `php_crud_messages`;";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
$totalRecords = $totalRecordsRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

$sql = "SELECT * FROM `php_crud_messages` LIMIT $startFrom, $recordsPerPage;";

$result = mysqli_query($conn, $sql);

if ($page > $totalPages && $totalPages > 0) {
    header("Location: {$_SERVER['PHP_SELF']}?page=1");
    exit;
}

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

if (isset($_SESSION["message-deleted"])) {
	$flag_deleted_message = true;
	unset($_SESSION["message-deleted"]); // Clear the session variable
}

?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin</title>

    <!-- Bootstrap -->
    <link href="css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .list-contet {
			height: 440px;
		}
        .paginaton-div {
			bottom: 0;
			position: absolute;
			left: 0;
		}
        .paginaton-div .disabled {
            color: #aaa;
            /* Change color to grey */
            pointer-events: none;
            border: 1px solid grey;
            /* Disable pointer events */
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .swal2-close:hover {
            color: #ff651b;
        }
        .delete-button {
            margin-left: 10px; /* Adjust as needed */
            vertical-align: middle;
            border: none;
            cursor: pointer;
        }

        
		.confirm-model {
            text-align: center;
            padding: 30px;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 35px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-width: 450px;
        }

        .confirm-model h3 {
            margin-top: 0;
        }
		
        #yesButton {
            padding: 10px 20px;
            margin: 0 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            background-color: #FF651B;
            color: white;
        }
        #noButton{
            padding: 10px 20px;
            margin: 0 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            background-color: #214139;
            color: white;
        }

        
        #yesButton:hover {
            background-color:#FF651B;
        }
		.buttons-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container {
            display: flex;
            align-items: center; /* Vertically center the buttons */
        }

.button-container a,
.button-container button {
    margin-right: 10px; /* Add spacing between buttons */
}
.btn.btn-info{
    width: 71px;
}

    </style>
</head>

<body>

    <div class="header">
        <div class="wrapper">
            <div class="logo"><a href="#"><img src="images/logo.png"></a></div>


            <div class="right_side">
                <ul>
                    <li>Welcome <strong>
                    <?php echo $firstName?>
                        </strong></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="nav_top">
                <ul>
                    <li class=""><a href=" home.php ">Dashboard</a></li>
                    <li><a href="list-users.php">Users</a></li>
                    <li class="active"><a href="#">Queries</a></li>
                    <?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
                    <!-- <li><a href=" geoloclist.php ">Configuration</a></li> -->
                </ul>
            </div>
        </div>
    </div>

    <div class="confirm-model" style="display:<?php echo $flag ? 'block' : 'none'; ?>">
        <h3>Are you sure you want to Delete the Message? </h3>
        <div class="buttons-container">
            <form method="post" id="confirmForm">
                <input type="hidden" name="confirm_delete" id="confirm_delete">
                <button type="button" id="noButton">No</button>
                <button type="submit" id="yesButton">Yes</button>
            </form>
        </div>
    </div>

    <div class="clear"></div>
    <div class="clear"></div>
    <div class="content">
        <div class="wrapper">
            <!-- <div class="bedcram">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="list-users.php">List Users</a></li>
                    <li>Add User</li>
                </ul>
            </div> -->
            <div class="left_sidebr">
                <ul>
                    <li><a href="home.php" class="dashboard">Dashboard</a></li>
                    <li><a href="list-users.php" class="user">Users</a>
                   
                        <!-- <ul class="submenu">
                            <li><a href="list-users.php">Manage Users</a></li>

                        </ul> -->

                    </li>
                    <li><a href="" class="Setting"></a>
                        <ul class="submenu">
                            <li><a href="change-password.php">Change Password</a></li>
                            <?php if($user_role_id==1 || $user_role_id==2):?> <li><a href="manage-email.php">Manage Email Content</a></li>
                                <?php endif?>
                            <!-- <li><a href="#">Manage Login Page</a></li> -->
                        </ul>

                    </li>
                    <li><a href="" class="social"></a>
                        <ul class="submenu">
                        <?php if ($user_role_id == 1): ?>
                                <li><a href="settings.php">Settings</a></li>
                            <?php endif ?>
                            <li class="manage-contact active"><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>User Queries</h1>
                <div class="list-contet">
                    <!-- <div class="form-left">
                        
                        <input type="button" id="add-btn" class="submit-btn add-user" value="Add More Users">
                    </div> -->
                    <?php if ($result): ?>
                        <?php if (mysqli_num_rows($result) > 0): ?>

                            <table width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <?php
                                        echo
                                            "<th width='120px'> Name</th>" .
                                            "<th width='80px'>Email</th>" .
                                            "<th width='80px'>Number</th>" .
                                            "<th width='120px'>Title</th>".
                                            "<th width='150px'>Message</th>".
                                            "<th width='100px'><a>Options </a></th>";
                                        ?>

                                    </tr>
                                    <?php

                                    while ($row = mysqli_fetch_array($result)) {
                                        echo '<tr>' .
                                            '<td>' . (strlen($row['name']) > 13 ? substr($row['name'], 0, 13) . '...' : $row['name']) . '</td>' .
                                            '<td>' . $row['email'] . '</td>' .
                                            '<td>' . $row['number'] . '</td>' .
                                            '<td>' . (strlen($row['title']) > 20 ? substr($row['title'], 0, 20) . '...' : $row['title']) . '</td>' .
                                            '<td>' . (strlen($row['message']) > 20 ? substr($row['message'], 0, 20) . '...' : $row['message']) . '</td>' .
                                            '<td>';

                                        // Wrap both buttons in a div for styling
                                        echo '<div class="button-container">';
                                        echo '<a href="show-message.php?id=' . $row['id'] . '" class="btn btn-info">Read More</a>';
                                        echo '<button class="delete-button btn btn-danger" onclick="showConfirmationModel(' . $row['id'] . ')"><img src="images/cross.png"></button>';
                                        echo '</div>'; // Close button-container div

                                        echo '</td>' .
                                            '</tr>';
                                    }

                                    // End of PHP block
                                    ?>

                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No Messages found.</p>

                        <?php endif; ?>
                    <?php endif; ?>

                    <?php
                   echo "<div class='paginaton-div'>";
                   $prevPage = max($page - 1, 1); // Ensure previous page doesn't go below 1
                   $nextPage = min($page + 1, $totalPages); // Ensure next page doesn't exceed total pages
                   
                   echo "<ul>";
                   echo "<li><a href='?page={$prevPage}'" . ($prevPage == $page ? " class='disabled'" : "") . ">Previous</a></li>";
                   
                   // Calculate the range of pages to display
                   $startPage = max(1, min($page - 1, $totalPages - 2));
                   $endPage = min($startPage + 2, $totalPages);
                   
                   // Display pagination links
                   for ($i = $startPage; $i <= $endPage; $i++) {
                       echo "<li><a href='?page={$i}'" . ($i == $page ? " class='active'" : "") . ">$i</a></li>";
                   }
                   
                   echo "<li><a href='?page={$nextPage}'" . ($nextPage == $page ? " class='disabled'" : "") . ">Next</a></li>";
                   echo "</ul>";
                   echo "<p style='margin-top: 10px'>Total Queries: $totalRecords</p>";
                   echo "</div>";
                   
                    ?>
                </div>
            </div>

        </div>
    </div>
    <div class="footer">
<div class="wrapper">
<p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   
    const flag_deleted_message =  <?php echo json_encode($flag_deleted_message);?>;
    if (flag_deleted_message) {
			Swal.fire({
				title: "Great!",
				text: "Message deleted successfully!",
				icon: "success"
			});
		}

    document.addEventListener('DOMContentLoaded', function () {
        var readMoreButtons = document.querySelectorAll('.read-more-button');

        readMoreButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var message = this.dataset.message;
                var truncatedMessage = this.parentNode.previousElementSibling.textContent;
                this.parentNode.previousElementSibling.textContent = message;
                this.dataset.message = truncatedMessage;
                this.textContent = (this.textContent === 'Read More') ? 'Read Less' : 'Read More';
            });
        });
    });
    //delete
	var flag = <?php echo $flag ? 'true' : 'false'; ?>;

    function showConfirmationModel(id) {
        var confirmationModel = document.querySelector('.confirm-model');
        if (confirmationModel) {
            confirmationModel.style.display = 'block';
            document.getElementById('confirm_delete').value = 'yes';
            document.getElementById('confirmForm').action = 'delete_message.php?id=' + id + '&query=<?php echo urlencode($searchQuery); ?>&page=<?php echo $page; ?>';
        }
    }

    document.getElementById('noButton').addEventListener('click', function () {
        flag = false; // Set flag to false
        document.querySelector('.confirm-model').style.display = 'none'; // Hide confirmation model
    });

</script>
</body>

</html>