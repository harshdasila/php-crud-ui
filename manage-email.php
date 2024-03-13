<?php
include 'db_connect.php';

$user_role_id = $_SESSION['user_role_id'];
if ($user_role_id != 1 && $user_role_id != 2) {
    header('Location: list-users.php');
}


$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

$sql = "SELECT * from email_templates";
$result = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($data);

if (isset($_SESSION["template-deleted"])) {
    $flag_deleted_template = true;
    unset($_SESSION["template-deleted"]); // Clear the session variable
}

if (isset($_SESSION["template-edited"])) {
    $flag_edited_template = true;
    unset($_SESSION["template-edited"]); // Clear the session variable
}

$getRowsQuery = "SELECT * FROM settings WHERE id = 1";
$rowsResult = mysqli_query($conn, $getRowsQuery);
if (mysqli_num_rows($rowsResult) > 0) {
	$settingsRow = mysqli_fetch_array($rowsResult);
	$recordsPerPage = $settingsRow['rows_per_page'];// Pagination variables
	$dateFormat = $settingsRow['date_format'];
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

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .swal2-close:hover {
            color: #ff651b;
        }

        .delete-button {
            margin-left: 10px;
            /* Adjust as needed */
            vertical-align: middle;
            border: none;
            cursor: pointer;
        }

        .button-container a,
        .button-container button {
            margin-right: 10px;
            /* Add spacing between buttons */
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

        .manage-email-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
            font-weight: 600;
            font-size: 27px;
        }

        .list-contet {
            height: 440px;
        }

        .confirm-model h3 {
            margin-top: 0;
        }

        .confirm-model {
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

        #noButton {
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
            background-color: #FF651B;
        }

        .buttons-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container {
            display: flex;
            align-items: center;
            /* Vertically center the buttons */
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
                            <?php echo $firstName ?>
                        </strong></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="nav_top">
                <ul>
                    <li class=""><a href=" home.php ">Dashboard</a></li>
                    <li><a href="list-users.php">Users</a></li>
                    <li><a href="manage-contact.php">Queries</a></li>
                    <?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
                    <!-- <li><a href=" geoloclist.php ">Configuration</a></li> -->
                </ul>

            </div>
        </div>
    </div>

    <div class="confirm-model" style="display:<?php echo $flag ? 'block' : 'none'; ?>">
        <h3>Are you sure you want to Delete the Template? </h3>
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
                            <li class="change-password "><a href="change-password.php">Change Password</a></li>
                            <li class="manage-email active"><a href="#">Manage Email Content</a></li>
                            <!-- <li><a href="#">Manage Login Page</a></li> -->

                        </ul>
                    </li>
                    <li><a href="" class="social"></a>
                        <ul class="submenu">
                        <?php if ($user_role_id == 1): ?>
                                <li><a href="settings.php">Settings</a></li>
                            <?php endif ?>
                           
                            <li class=""><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Manage Email Content</h1>
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
                                            "<th width='120px'>Template Name</th>" .
                                            "<th width='100px'>Created on</th>" .
                                            "<th width='100px'>Updated on</th>" .
                                            "<th width='80px'><a>Options </a></th>";
                                        ?>

                                    </tr>
                                    <?php

                                    while ($row = mysqli_fetch_array($result)) {
                                        echo '<tr>' .
                                            '<td>' . $row['slug'] . '</td>' .
                                            '<td>' . date($dateFormat, strtotime(substr($row['created_at'],0,10))) . '</td>' .
                                            '<td>' .  date($dateFormat, strtotime(substr($row['updated_at'],0,10))) . '</td>' .

                                            '<td>';
                                           

                                        // Wrap both buttons in a div for styling
                                        echo '<div class="button-container">';
                                        echo '<a href="edit-template.php?id=' . $row["id"] . '"><img src="images/edit-icon.png"></a>' .
                                            "<button class='delete-button' onclick='showConfirmationModel({$row['id']})'><img src='images/cross.png'></button>";
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
        const flag_deleted_template = <?php echo json_encode($flag_deleted_template); ?>;
        if (flag_deleted_template) {
            Swal.fire({
                title: "Great!",
                text: "Template deleted successfully!",
                icon: "success"
            });
        }

        const flag_edited_template = <?php echo json_encode($flag_edited_template); ?>;
        if (flag_edited_template) {
            Swal.fire({
                title: "Great!",
                text: "Template edited successfully!",
                icon: "success"
            });
        }
        //delete
        var flag = <?php echo $flag ? 'true' : 'false'; ?>;


        function showConfirmationModel(id) {
            var confirmationModel = document.querySelector('.confirm-model');
            if (confirmationModel) {
                confirmationModel.style.display = 'block';
                document.getElementById('confirm_delete').value = 'yes';
                // Construct the action URL with sortOrder and sortColumn parameters
                var url = 'delete_template.php?id=' + id;
                document.getElementById('confirmForm').action = url;

            }
        }

        document.getElementById('noButton').addEventListener('click', function () {
            flag = false; // Set flag to false
            document.querySelector('.confirm-model').style.display = 'none'; // Hide confirmation model
        });
    </script>

</body>

</html>