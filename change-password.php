<?php
include 'db_connect.php';
$id = 0;
$passerr = "";
$cpasserr = "";
$id = $_SESSION['id'];
$user_role_id = $_SESSION["user_role_id"];

$sql = "SELECT * FROM email_templates WHERE slug = 'password_changed'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$subject = $row["subject"];
$content = $row["content"];

$sql = "SELECT * FROM users WHERE id='$id';";
$result = mysqli_query($conn, $sql);
$password = "";
$flag = false;
$forRow = mysqli_fetch_array($result);
$email = $forRow["email"];

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

if (isset($_POST['submit'])) {
    $iscorrect = "true";
    $password = $_POST['opassword'];

    if (!password_verify($password, $forRow['password'])) {
        $iscorrect = false;
        $flag = true;
        $opasserr = "Incorrect password.";
    }


    if (strlen($password) < 8) {
        $passerr = "Password should be at atleast 8 characters";
        $iscorrect = "false";
        $flag = true;

    } else {
        $uppercase = preg_match('@[A-Z]@', $password);
        // $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('/[^A-Za-z0-9]/', $password); // Match any character that is not a letter or number

        if (!$uppercase || !$number || !$specialChars) {
            $iscorrect = false;
            $flag = true;
            $passerr = 'Password should include at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }

    }
    // echo $iscorrect;

    $npassword = $_POST['npassword'];
    $cpassword = $_POST['cpassword'];

    // if ($cpassword != $password || $_POST['cpassword'] == NULL) {
    //     $iscorrect = "false";
    //     echo $iscorrect;
    //     // $cpasserr = "Both passwords are not matched ";
    // }
    $hash = password_hash($npassword, PASSWORD_DEFAULT);

    // echo $hash;
    // echo $iscorrect;
    $iscorrect = trim($iscorrect);

    if ($iscorrect == "true") {
        $timee = time();
        date_default_timezone_set('Asia/Kolkata');
        $ttime = date('Y-m-d H:i:s', $timee);
        $sql = "UPDATE users SET `password`='$hash' WHERE id=$id";

        require('script.php');
        require('template.php');
        sendMail("$email", "$subject", "$content");
        // echo "<br>" . $sql;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $_SESSION['status'] = "success";
        }
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


    <style>
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

        .error-ms {
            position: absolute;
            margin-top: 36px;
            color: #ff0000;
            float: left;
            font-size: 12px;
            width: 100%;
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
                            <li class="change-password active"><a href="change-password.php">Change Password</a></li>
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
                            <li><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Change Password</h1>
                <div class="list-contet">



                    <form class="form-edit" method="POST" onsubmit="return validateForm()">
                        <div class="form-row">

                            <div class="form-label">
                                <label for="opassword">Old Password : </label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="search-box" placeholder="Old password" id="opassword"
                                    value="<?php echo $password ?>" class="opassword" name="opassword" />
                                <p class="error-ms" id="opassmsg">
                                    <?php echo $opasserr ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="form-label">
                                <label for="npassword">New Password : </label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="search-box" placeholder="New password" id="npassword"
                                    value="<?php echo $npassword ?>" class="npassword" name="npassword" />
                                <p class="error-ms" id="npassmsg">
                                    <?php echo $npasserr ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-row">

                            <div class="form-label">
                                <label for="cpassword">Confirm Password : </label>
                            </div>
                            <div class="input-field">
                                <input type="password" class="search-box" placeholder="Confirm password"
                                    onblur="validateForm()" id="cpassword" value="<?php echo $cpassword ?>"
                                    class="cpassword" name="cpassword" />
                                <p class="error-ms" id="cpassmsg">
                                    <?php echo $cpasserr ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label">
                                <label><span></span> </label>
                            </div>
                            <div class="input-field">
                                <!-- <button type="submit" value="submit" name="submit" id='btn'>Submit</button>     -->
                                <button type="submit" class="submit-btn" name="submit">Change</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="footer">
        <div class="wrapper">
            <p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
        </div>

    </div>
    <script src="js/validatechangePass.js"></script>
    <script>
        $(document).ready(function () {
            $('#opassword').on('blur', function () {
                // var email = $(this).val();
                var opass = $(this).val();
                console.log(opass)
                var passRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
                if (!passRegex.test(opass)) {
                    // document.getElementById("opassmsg").innerHTML = "Please enter a valid password";
                }
                else document.getElementById("opassmsg").innerHTML = "";
            });
        });


        <?php if (isset($_SESSION['status'])) { ?>
            Swal.fire({
                title: "Password changed.",
                icon: "success",
                iconColor: "#ff651b",
                showCloseButton: true,
                confirmButtonText: "Okay", // Enclose in quotes
                confirmButtonColor: "#ff651b",
            }).then((result) => {
                <?php unset($_SESSION['status']); ?> // Unset the session after displaying the alert
                window.location.href = "list-users.php";
            });
        <?php } ?>
    </script>
</body>

</html>