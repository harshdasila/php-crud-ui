<?php
include 'db_connect.php';

$id = $_GET['id'];

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];
$user_role_id = $_SESSION["user_role_id"];

$sql = "SELECT * from email_templates where id='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$email_body = $row["content"];
$email_subject = $row["subject"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['emailBody'])) {
        $editorBody = $_POST['emailBody'];
    }
    if (isset($_POST['emailSubject'])) {
        $editorSubject = $_POST['emailSubject'];
    }
    
    // Ensure to use $editorSubject and $editorBody variables instead of $email_subject and $content
    $sql = "UPDATE email_templates SET subject = '$editorSubject', content='$editorBody' WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $_SESSION['template-edited'] = true;
        header('Location: manage-email.php');
        exit; // Add exit to prevent further execution
    } else {
        // Handle error if the query fails
        echo "Error updating template: " . mysqli_error($conn);
    }
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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    <style>
        .error {
            margin-top: -15px;
            position: absolute;
            font-size: 13px;
            color: red;
        }

        #replyBtn:hover {
            background-color: #005f75;
            /* Darker Blue */
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

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            margin-top: 10px;
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-family: sans-serif;
        }

        .input-field,
        .textarea-field {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .textarea-field {
            resize: vertical;
            /* Allow vertical resizing of the textarea */
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
                    <li><a href="list-users.php">Users</a></li>
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
                    <li><a href="#">Home</a></li>
                    <li><a href="list-users.php">List Users</a></li>
                    <li>Add User</li>
                </ul>
            </div> -->
            <div class="left_sidebr">
                <ul>
                    <li><a href="home.php" class="dashboard">Dashboard</a></li>
                    <li><a href="list-users.php" class="user">Users</a>
                        <ul class="submenu">
                            <!-- <li><a href="list-users.php">Manage Users</a></li> -->
                        </ul>
                    </li>
                    <li><a href="" class="Setting"></a>
                        <ul class="submenu">
                            <li><a href="change-password.php">Change Password</a></li>
                            <?php if($user_role_id==1 || $user_role_id==2):?> <li class="manage-email active"><a href="manage-email.php">Manage Email Content</a></li>
                                <?php endif?>
                        </ul>

                    </li>
                    <li><a href="" class="social"></a>
                        <ul class="submenu">
                        <?php if ($user_role_id == 1): ?>
                                <li><a href="settings.php">Settings</a></li>
                            <?php endif ?>
                            <li><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Update Template</h1>
                <div class="list-content">
                    <div id="email_body_container" class="input-group">
                        <form id="replyForm" method="POST">
                            <div class="input-group">
                                <label for="emailSubject" class="input-label">Email Subject:</label>
                                <input type="text" id="emailSubject" name="emailSubject" class="input-field"
                                    value="<?php echo $email_subject ?>" />
                            </div>
                            <br>
                            <label for="emailBody" class="input-label">Email Body:</label>
                            <textarea id="emailBody" name="emailBody" rows="8"
                                class="textarea-field"><?php echo $email_body ?></textarea>
                            <br>
                            <div class="reply-btn-container">
                                <button id="replyBtn" type="submit" value="submit">Save</button>
                            </div>
                            <!-- <div id="replyError" class="error"></div> -->
                        </form>
                    </div>

                    <!-- <div class="buttons-container">
                        <a class="back-button" href="manage-contact.php"> Back </a>
                    </div> -->

                </div>


            </div>

        </div>
    </div>
    <div class="footer">
        <div class="wrapper">
            <p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
        </div>

    </div>

    <script>
        ClassicEditor
            .create(document.querySelector('#emailBody'))
            .then(editor => {
                // Set the retrieved HTML content in the CKEditor instance
                editor.setData(`<?php echo htmlspecialchars_decode($email_body); ?>`);
            })
            .catch(error => {
                console.error(error);
            });
    </script>


</body>

</html>