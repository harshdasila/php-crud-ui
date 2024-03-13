<?php
include "db_connect_login&registration.php";
session_start();
$incorrectPassword = false;


// Check if the request is made via AJAX
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Get email from AJAX request
    $email = $_POST['email'];

    // Query the database to check if the email exists
    $checkQuery = "SELECT id FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkQuery);

    if (!$result || mysqli_num_rows($result) == 0) {
        // Email is not registered
        $response = array('success' => false, 'message' => 'Email is not registered');
    } else {
        // Email is registered
        $response = array('success' => true);
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Terminate script after sending JSON response
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $checkQuery = "SELECT id,name,user_role_id, password FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) == 0) {
        $incorrectPassword = true;
        $unregisteredEmail = true;
    } else {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];
        // Verify the provided password with the hashed password from the database
        if (password_verify($password, $hashed_password)) {
            $user_id = $row['id'];
            $name = $row['name'];
            $user_role_id = $row['user_role_id'];
            $_SESSION["id"] = $user_id;
            $_SESSION["name"] = $name;
            $_SESSION["user_role_id"] = $user_role_id;
            header('Location: list-users.php');
        } else {
            // Password is incorrect only if the password is not empty
            if ($password !== '') {
                $incorrectPassword = true;
            }
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
    <style>
        .star {
            color: red;
        }

        .error {
            margin-top: 66px;
            position: absolute;
            font-size: 11px;
            color: red;
        }

        .form-group {
            margin-bottom: 27px;
        }
    </style>
</head>

<body>
    <div class="login_section">
        <div class="wrapper relative">
            <div style="display:none" class="meassage_successful_login">You have Successfull Edit </div>
            <div class="heading-top">
                <div class="logo-cebter"><a href="#"><img src="images/at your service_banner.png"></a></div>
            </div>
            <div class="box">
                <div class="outer_div">

                    <h2>Admin <span>Login</span></h2>
                    <?php if ($unregisteredEmail || $incorrectPassword): ?>
                        <div class="error-message-div error-msg"><strong>Incorrect!</strong> Password
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        class="margin_bottom" role="form">
                        <div class="form-group">
                            <label for="email">User Name<span class="star">*</span></label>
                            <input type="text" id="email" name="email"
                                value="<?php if (isset($_POST['email']))
                                    echo $_POST['email']; ?>"
                                class="form-control" />
                            <div id="emailError" class="error"></div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password<span class="star">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" />
                            <div id="passwordError" class="error"></div>

                        </div>
                        <button type="submit" class="btn_login">Login</button>
                        <!-- <a href="forgot-password.php">Forgot Password</a> -->
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                var timeoutIdEmail, timeoutIdPassword;
                var emailErrorPersisted = false;

                // Function to validate email input with debounce
                $('#email').on('input', function () {
                    clearTimeout(timeoutIdEmail);
                    var emailInput = $(this);
                    var emailError = $('#emailError');
                    timeoutIdEmail = setTimeout(function () {
                        validateEmail(emailInput, emailError);
                        if (emailInput.val().trim() === '') {
                            emailErrorPersisted = false;
                        } else if (emailError.text() === '') {
                            checkEmailExists(emailInput.val().trim());
                        }
                    }, 500);
                    // Hide error message immediately when user starts typing again
                    emailError.text('');
                });


                // Function to validate password input with debounce
                $('#password').on('input', function () {
                    clearTimeout(timeoutIdPassword); // Clear previous timeout
                    var passwordInput = $(this);
                    var passwordError = $('#passwordError');
                    timeoutIdPassword = setTimeout(function () {
                        validatePassword(passwordInput, passwordError);
                    }, 1000);
                    passwordError.text('');
                });

                // Function to handle form submission
                $('#loginForm').submit(function (e) {
                    e.preventDefault();
                    $('#incorrectPasswordContainer').hide();
                    // $('#emailError').text('');
                    // $('#passwordError').text('');

                    // Get email and password
                    var email = $('#email').val().trim();
                    var password = $('#password').val().trim();

                    if (email === '') {
                        $('#emailError').text('Please enter your email.');
                        return;
                    }
                    if (password === '') {
                        $('#passwordError').text('Please enter your password.');
                        return;
                    }

                    // Validate email
                    var emailInput = $('#email');
                    var validEmail = validateEmail(emailInput, $('#emailError'));
                    if (!validEmail) {
                        $('#emailError').text('Please enter a valid email.');
                        return; // Exit early if email is invalid
                    }

                    // Validate password
                    var passwordInput = $('#password');
                    var validPassword = validatePassword(passwordInput, $('#passwordError'));

                    if (!validPassword) {
                        return; // Exit early if password is invalid
                    }

                    // If email error was persisted, show it again
                    if (emailErrorPersisted) {
                        $('#emailError').text('Email is not registered.');
                        return; // Exit early if email is not registered
                    }

                    // If all validations pass, submit the form
                    this.submit();
                });

                // Function to validate email format
                function validateEmail(inputField, errorElement) {
                    var value = inputField.val().trim();
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    var dotPosition = value.lastIndexOf(".");
                    var afterDot = value.substring(dotPosition + 1);

                    if (value === '' || !emailRegex.test(value)) {
                        errorElement.text(value === '' ? '' : 'Please enter a valid email.');
                        return false;
                    } else if (/\d/.test(afterDot)) {
                        errorElement.text('Please enter a valid email.');
                        return false;
                    } else if (afterDot.length < 2) {
                        errorElement.text('Please enter a valid email.');
                        return false;
                    } else {
                        errorElement.text('');
                        return true;
                    }
                }


                // Function to validate password presence
                function validatePassword(inputField, errorElement) {
                    var value = inputField.val().trim();
                    if (value === '') {
                        errorElement.text('Please enter your password.');
                        return false;
                    } else {
                        errorElement.text('');
                        return true;
                    }
                }

                // Function to check if the email exists
                function checkEmailExists(email) {
                    $.ajax({
                        url: 'login.php',
                        method: 'POST',
                        data: { email: email },
                        dataType: 'json',
                        success: function (response) {
                            if (!response.success) {
                                $('#emailError').text(response.message);
                                emailErrorPersisted = true; // Set flag to true
                            } else {
                                emailErrorPersisted = false; // Reset flag if email exists
                            }
                        },
                        error: function (xhr, status, error) {
                            // Handle error
                            console.error(xhr.responseText);
                        }
                    });
                }

            });

        </script>
</body>

</html>