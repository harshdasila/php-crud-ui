<?php
include("db_connect.php");
$user_role_id = $_SESSION["user_role_id"];


$id = $_GET['id'];
$replyerror = false;


$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];


$sql = "select * from  `php_crud_messages` where id = '$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$name = $row["name"];
$email = $row["email"];
$number = $row["number"];
$title = $row["title"];
$message = $row["message"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editorContent'])) {
        $editorContent = $_POST['editorContent'];
        if ($editorContent == "") {
            $replyerror = true;
        }
    } else {

        // echo "No content received from the editor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    <link rel="stylesheet" href="css/styles.css">
    <link href="css/dashboard.css" rel="stylesheet">
    <style>
        .error {
            margin-top: -15px;
            position: absolute;
            font-size: 13px;
            color: red;
        }

        #replyBtn {
            padding: 8px 17px;
            background-color: #008CBA;
            /* Blue */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        #replyBtn:hover {
            background-color: #005f75;
            /* Darker Blue */
        }

        .editor-container {
            margin-top: 20px;
        }

        .message-container {
            margin-top: 20px;
        }

        .message-heading {
            display: inline-block;
            margin-right: 10px;
            color: #214139;
            font-family: sans-serif;
        }

        .message-content {
            display: inline-block;
            font-weight: 550;
            font-size: 17px;
        }

        .buttons-container {
            margin-top: 20px;
            padding: 2em;
            display: flex;
            justify-content: space-evenly;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #4CAF50;
            /* Green */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #45a049;
            /* Darker Green */
        }

        .delete-button {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #FF0000;
            /* Red */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .delete-button:hover {
            background-color: #CC0000;
            /* Darker Red */
        }

        .reply-btn-container {
            position: absolute;
            right: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="wrapper">
            <div class="logo"><a href="#"><img src="images/logo.png"></a></div>
            <div class="right_side">
                <ul>
                    <li>Welcome
                        <?php echo $firstName ?>
                    </li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="nav_top">
                <ul>
                    <li class=""><a href=" home.php ">Dashboard</a></li>
                    <li><a href="list-users.php ">Users</a></li>
                    <li class="active"><a href="manage-contact.php">Queries</a></li>
                    <?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
                    <!-- <li><a href=" geoloclist.php ">Configuration</a></li> -->
                </ul>

            </div>
        </div>
    </div>

    <div class="clear"></div>
    <div class="clear"></div>
    <div class="content">
        <div class="wrapper">
            <!-- <div class="bedcram">
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="list-users.php">List Users</a></li>
                    <li>Edit Users</li>
                </ul>
            </div> -->
            <div class="left_sidebr">
                <ul>
                    <li><a href="home.php" class="dashboard">Dashboard</a></li>
                    <li><a href="list-users.php" class="user">Users</a>
                        <!-- <ul clhome -->
                    </li>
                    <li><a href="" class="Setting"></a>
                        <ul class="submenu">
                            <li><a href="change-password.php">Change Password</a></li>
                            <?php if ($user_role_id == 1 || $user_role_id == 2): ?>
                                <li><a href="manage-email.php">Manage Email Content</a></li>
                            <?php endif ?>
                            <!-- <li><a href="#">Manage Login Page</a></li> -->
                        </ul>

                    </li>
                    <li><a href="" class="social"></a>
                        <ul class="submenu">
                            <?php if ($user_role_id == 1): ?>
                                <li><a href="settings.php">Settings</a></li>
                            <?php endif ?>
                            <li><a class="manage-contact active" href="manage-contact.php">Manage Contact Request</a>
                            </li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Message Details</h1>
                <div class="list-contet">
                    <div class="message-container">
                        <div>
                            <h3 class="message-heading">Name :</h3>
                            <div class="message-content">
                                <?php echo $name ?>
                            </div>
                        </div>
                        <br>
                        <div>
                            <h3 class="message-heading">Mobile Number :</h3>
                            <div class="message-content">
                                <?php echo $number ?>
                            </div>
                        </div>
                        <br>
                        <div>
                            <h3 class="message-heading">Email :</h3>
                            <div class="message-content">
                                <?php echo $email ?>
                            </div>
                        </div>
                        <br>
                        <div>
                            <h3 class="message-heading">Message Title :</h3>
                            <div class="message-content">
                                <?php echo $title ?>
                            </div>
                        </div>
                        <br>
                        <div>
                            <h3 class="message-heading">Message :</h3>
                            <div class="message-content">
                                <?php echo $message ?>
                            </div>
                        </div>
                        <br>
                        <div>
                            <h3 class="message-heading">Reply :</h3>
                            <div class="message-content">
                                <?php echo $editorContent ?>
                            </div>
                        </div>
                    </div>
                    <div class="editor-container">

                        <form id="replyForm" method="POST">
                            <textarea name="editorContent" id="editor"></textarea>
                            <br>
                            <div class="reply-btn-container">
                                <button id="replyBtn" type="submit" value="submit">Reply</button>
                            </div>
                            <div id="replyError" class="error"></div>
                        </form>


                    </div>
                    <!-- <button id="replyButton">Reply</button> -->

                    <div class="buttons-container">
                        <a class="back-button" href="manage-contact.php"> Back </a>
                        <a class="delete-button" href="delete_message.php?id=<?php echo $id; ?>"> Delete </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <div class="footer">
        <div class="wrapper">
            <p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
        var hasError = false;
        $('#replyForm').submit(function (e) {
            e.preventDefault();
            $('#replyError').text('');
            var reply = $('#editor').val().trim();
            if (reply == "") {
                $('#replyError').text("Reply can not be empty.")
                return false;
            }
            this.submit();
        });
    </script>

</body>

</html>