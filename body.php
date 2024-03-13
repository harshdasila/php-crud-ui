<?php
include 'db_connect.php';

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
    <!-- <style>
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
    </style> -->
</head>

<body>

    <div class="header">
        <div class="wrapper">
            <div class="logo"><a href="#"><img src="images/logo.png"></a></div>


            <div class="right_side">
                <ul>
                <li>Welcome <strong> <?php echo $_SESSION['uname'];?> </strong></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="nav_top">
                <ul>
                    <li class=""><a href=" home.php ">Dashboard</a></li>
                    <li><a href="list-users.php">Users</a></li>
                    <!-- <li><a href=" agentloclist.php ">Setting</a></li> -->
                    <li><a href=" geoloclist.php ">Configuration</a></li>
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
                    <li><a href="" class="dashboard">Dashboard</a></li>
                    <li><a href="" class="user">Users</a>
                        <ul class="submenu">
                            <li><a href="list-users.php">Manage Users</a></li>

                        </ul>

                    </li>
                    <li><a href="" class="Setting">Setting</a>
                        <ul class="submenu">
                            <li class="active"><a href="change-password.php">Change Password</a></li>
                            <li><a href="">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Login Page</a></li> -->

                        </ul>

                    </li>
                    <li><a href="" class="social">Configuration</a>
                        <ul class="submenu">
                            <li><a href="">Payment Settings</a></li>
                            <li><a href="manage-email.php">Manage Email Content</a></li>
                            <li><a href="#">Manage Limits</a></li>
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Update User</h1>
                
            </div>

        </div>
    </div>
    <div class="footer">
<div class="wrapper">
<p>Copyright © 2014 yourwebsite.com. All rights reserved</p>
</div>

</div>
    <script src="js/validatechangePass.js"></script>
    <!-- <script>
        $(document).ready(function() {
            $('#opassword').on('blur', function() {
                // var email = $(this).val();
                var opass = $(this).val();
                console.log(opass)
                var passRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
                if (!passRegex.test(opass)) {
                    document.getElementById("opassmsg").innerHTML = "Please enter a valid password";
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
    </script> -->
</body>

</html>