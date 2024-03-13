<?php
include 'db_connect.php';

$user_role_id = $_SESSION['user_role_id'];
if ($user_role_id != 1 && $user_role_id != 2) {
    header('Location: list-users.php');
}
// $loggedInuserID = $_SESSION['id'];
// $getUserEmail = "SELECT * from users WHERE id='$loggedInuser'";
// $res = mysqli_query($conn, $getUserEmail);
// $getUserRow = mysqli_fetch_array($res);
// $user_email = $getUserRow["email"];

$sql = "SELECT * FROM email_templates WHERE slug = 'user_added'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$subject = $row["subject"];
$content = $row["content"];


$flag = 0; //to show modal

$name = $number = $password = $email = "";
$name_error = $email_error = $number_error = $password_error = $password_same_error = false; //setting default as false

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Get email from AJAX request
    $email = $_POST['email'];

    // Query the database to check if the email exists
    $checkQuery = "SELECT id FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($result) > 0) {
        $response = array('success' => false, 'message' => 'Email is already registered');
    } else {
        $response = array('success' => true);
    }


    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Terminate script after sending JSON response
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $number = $_POST["number"];
    $password = $_POST["password"];
    $password_2 = $_POST["password_2"];
    $gender = $_POST["gender"];
    $role = intval($_POST["userRole"]);


    if (!$name_error && !$email_error && !$number_error && !$password_error && !$password_same_error) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sqlInsert = "INSERT INTO users (name, email, mobile, password, gender,user_role_id) VALUES ('$name', '$email', '$number', '$hashed_password','$gender','$role')";

        $result = mysqli_query($conn, $sqlInsert);
        if ($result) {
            require('script.php');
            require('template.php');
            // Replace placeholders with actual values
            $content = str_replace("[USER_EMAIL]", $email, $content);
            $content = str_replace("[USER_PASSWORD]", $password, $content);
            sendMail("$email", "$subject", "$content");
            $_SESSION["user-added"] = true;
            header("Location: list-users.php");
            $flag = 1;//to show model

        } else {
            die("Insertion failed: " . mysqli_error($conn));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <link href="css/dashboard.css" rel="stylesheet">
    <style>
        .error {
            margin-top: 41px;
            position: absolute;
            font-size: 13px;
            color: red;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .buttons-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 15px;
        }

        #back-btn {
            padding: 10px 24px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
            font-weight: 400;
        }

        #back-btn:hover,
        .submit-button:hover {
            background-color: #0056b3;
        }

        .confirm-model {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            /* Increased padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            width: 380px;
            height: 180px;
        }

        .confirm-model.show {
            display: block;
        }

        .confirm-model>div {
            margin-bottom: 20px;
        }

        .ok {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #ok-text {
            font-size: 18;
        }

        .confirm-model button {
            padding: 15px 30px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input:focus {
            outline: none !important;
            border: 2px solid #007bff;
            box-shadow: 0 0 1px #007bff;
        }

        .registration-form {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: -20px;
        }

        .registration-form>p {
            position: absolute;
            font-size: 14px;
            color: red;
            margin-top: -29px;
        }

        .confirm-model button:hover,
        .confirm-model button:focus {
            background-color: #0056b3;
        }

        .required-fields {
            font-size: 13px;
            margin-bottom: 20px;
            margin-top: -4px;
        }

        .star {
            color: red;
        }

        .data-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h3 {
            font-size: 1.8em;
        }

        #userRole {
            padding: 7px;
            font-size: 15px;
            background-color: #f2f2f2;
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
                    <li class="active"><a href=" list-users.php ">Users</a></li>
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
                    <li><a href="#">List Users</a></li>
                    <li>Edit Users</li>
                </ul>
            </div> -->
            <div class="left_sidebr">
                <ul>
                    <li><a href="home.php" class="dashboard">Dashboard</a></li>
                    <li><a class="user active" href="list-users.php" class="user">Users</a>

                        <!-- <ul class="submenu">
                            <li><a href="">Manage Users</a></li>

                        </ul> -->

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

                            <li><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>

                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Add User</h1>
                <div class="list-contet">
                    <div id="errorMessageContainer" style="display: none;">
                        <!-- <div class="error-message-div error-msg">
                            <img src="images/unsucess-msg.png">
                            <strong>UnSuccess!</strong> Your Message hasn't been Sent
                        </div> -->
                    </div>

                    <form id="registrationForm" class="form-edit" method="post"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <!-- <div class="form-row">
            </div> -->
                        <div class="form-row">
                            <div class="form-label">
                                <label for="name">Name : <span>*</span></label>
                            </div>
                            <div class="input-field">
                                <input name="name" id="name" type="text" class="search-box"
                                    value="<?php echo $name; ?>" />
                                <p id="nameError" class="error"></p>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-label">
                                <label for="email">Email: <span>*</span></label>
                            </div>
                            <div class="input-field">
                                <input type="text" name="email" id="email" class="search-box"
                                    value="<?php echo $email; ?>" />
                                <p id="emailError" class="error"></p>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-label">
                                <label for="number">Mobile Number: <span>*</span> </label>
                            </div>
                            <div class="input-field">
                                <input type="number" name="number" id="number" class="search-box"
                                    placeholder='Enter without country code (+91).' value="<?php echo $number; ?>"
                                    maxlength="10"
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">

                                <p id="mobileError" class="error"></p>
                            </div>
                        </div>
                        <!-- here -->
                        <div class="form-row">
                            <div class="form-label">
                                <label> Gender: <span>*</span></label>
                                <!-- <p id="genderError" class="error"></p> -->
                            </div>
                            <input type="radio" id="maleRadio" name="gender" value="male" checked>
                            <label style="width: 30px;" for="maleRadio">Male</label>
                            <input type="radio" id="femaleRadio" name="gender" value="female">
                            <label style="width: 40px;" for="femaleRadio">Female</label>

                        </div>
                        <div class="form-row">
                            <div class="form-label">
                                <label for="userRole">Select Role:<span>*</span></label>
                            </div>

                            <select id="userRole" name="userRole">
                                <option value="">Select Role</option>
                                <?php if ($user_role_id == 1): ?>
                                    <option value="2">Admin</option>
                                <?php endif; ?>
                                <option value="3">Manager</option>
                                <option value="4">Team Lead</option>
                                <option value="5">Employee</option>
                            </select>
                            <p id="userRoleError" class="error" style="display:none;">Please select a role.</p>

                        </div>


                        <div class="form-row">
                            <div class="form-label">
                                <label for="password">Password: <span>*</span> </label>
                            </div>
                            <div class="input-field">
                                <input type="password" name="password" id="password" class="search-box"
                                    value="<?php echo $password; ?>" />
                                <p id="passwordError" class="error"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label">
                                <label for="password">Confirm Password: <span>*</span> </label>
                            </div>
                            <div class="input-field">
                                <input type="password" name="password_2" id="password_2" class="search-box"
                                    value="<?php echo $password_2; ?>" />
                                <p id="passwordError2" class="error"></p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-label">
                                <label><span></span> </label>
                            </div>
                            <div class="input-field">
                                <input id="ok-text" type="submit" class="submit-button" value="Add">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var form = document.getElementById('registrationForm');

        $(document).ready(function () {
            var timeoutIdEmail;
            var timeoutIdNumber, timeoutIdPassword, timeoutIdPassword2;
            var emailErrorPersisted = false;

            $('#name').on('input', function () {
                validateField();
            });

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

            $('#number').on('input', function () {
                clearTimeout(timeoutIdNumber);
                timeoutIdNumber = setTimeout(() => {
                    validateNumber();
                }, 1000)
                $('#mobileError').text('');

            });

            $('#password').on('input', function () {
                clearTimeout(timeoutIdPassword);
                timeoutIdPassword = setTimeout(() => {
                    validatePassword();
                }, 1000)
                $('#passwordError').text('');

            });

            $('#password').on('input', function () {
                validatePassword2($(this), '#password_error_2');
            });

            $('#password_2,#password').on('input', function () {
                clearTimeout(timeoutIdPassword2);
                timeoutIdPassword2 = setTimeout(() => {
                    validatePassword_match($(this), '#password_error_match');
                }, 1000)
                $('#passwordError2').text('');

            });
            $('input[name="gender"]').change(function () {
                $('#genderError').text(''); // Clear gender error message when any radio button is selected
            });

            $('#registrationForm').submit(function (e) {
                e.preventDefault(); // Prevent form submission
                var hasErrors = false;
                // Reset error messages
                $('#emailError').text('');
                $('#nameError').text('');
                $('#mobileError').text('');
                $('#passwordError').text('');
                $('#passwordError2').text('');
                $('#genderError').text('');


                if (!validateField()) {
                    hasErrors = true;
                }
                if (!validateNumber()) {
                    hasErrors = true;
                }
                if (!validateEmail()) {
                    hasErrors = true;
                }
                if (!validatePassword()) {
                    hasErrors = true;
                }

                // if(!validatePassword2($(this), '#password_error_2')){
                //     hasErrors = true;
                // }

                // Get input values
                var email = $('#email').val().trim();
                var name = $('#name').val().trim();
                var number = $('#number').val().trim();
                var password = $('#password').val().trim();
                var password2 = $('#password_2').val().trim();
                var gender = $('input[name="gender"]:checked').val();

                if (gender !== undefined) {
                    gender = gender.trim();
                }

                // Check for empty fields

                if (email === '') {
                    $('#emailError').text('Please enter your email.');
                    hasErrors = true;
                }
                if (name === '') {
                    $('#nameError').text('Please enter your name.');
                    hasErrors = true;
                }
                if (number === '') {
                    $('#mobileError').text('Please enter your mobile number.');
                    hasErrors = true;
                }
                if (password === '') {
                    $('#passwordError').text('Please enter your password.');
                    hasErrors = true;
                }
                if (password2 === '') {
                    $('#passwordError2').text('Please confirm your password.');
                    hasErrors = true;
                }
                var passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
                if (!passwordRegex.test(password)) {
                    $('#passwordError').text('8+ characters with at least 1 uppercase letter and 1 special character.');
                    hasErrors = true;
                }
                if (gender === undefined) {
                    $('#genderError').text('Please select your gender.');
                    hasErrors = true;
                }

                // Check if passwords match
                if (password !== password2) {
                    $('#passwordError2').text('Both Passwords should match.');
                    hasErrors = true;
                }

                // If there are errors, stop form submission

                if (emailErrorPersisted) {
                    $('#emailError').text('Email is already registered.');
                    return;
                }
                if (hasErrors) {
                    return;
                }
                // $.ajax({
                //     type: form.method,
                //     url: form.action,
                //     data: $(form).serialize(),
                //     success: function (data) {
                //         console.log("Form submission successful.");
                //         // Redirect to the desired page
                //         window.location.href = 'list-users.php';
                //     }
                // });
                if (emailErrorPersisted) {
                    $('#emailError').text('Email is already registered.');
                    return; // Exit early if email is not registered
                }

                // If all validations pass, submit the form
                this.submit();
            });


            // Function to validate name input
            function validateField() {
                var value = $('#name').val().trim();
                var containsSpecialChars = /[^a-zA-Z\s]/.test(value); // Regular expression to check for non-alphabetic characters

                if (value === '') {
                    $("#nameError").text('Please enter your name.').show();
                    return false; // Validation failed
                } else if (containsSpecialChars) {
                    $("#nameError").text('Name should not contain numbers or special characters.').show();
                    return false; // Validation failed
                } else {
                    $("#nameError").hide();
                    return true; // Validation passed
                }
            }


            function validateEmail() {
                var inputField = $('#email');
                var errorElement = $('#emailError');
                var value = inputField.val().trim();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var dotPosition = value.lastIndexOf(".");
                var afterDot = value.substring(dotPosition + 1);

                if (value === '') {
                    errorElement.text('Please Enter your email.');
                    return false;
                }
                else if (!emailRegex.test(value)) {
                    errorElement.text('Please enter a valid email.');
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


            function validateNumber() {
                var number = $('#number').val().trim();
                if (number === '' || isNaN(number) || number.length !== 10 || number < 0) {
                    $('#mobileError').text('Please enter a valid 10-digit number.').show();
                    return false; // Validation failed
                } else if (/^0+$/.test(number)) {
                    $('#mobileError').text('Number cannot be all zeros.').show();
                    return false; // Validation failed
                } else {
                    $('#mobileError').hide();
                    return true; // Validation passed
                }
            }


            function validatePassword() {
                var password = $('#password').val().trim();
                var passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
                if (password === '') {
                    $('#passwordError').text('Please Enter Password.').show();
                    return false;
                }
                else if (!passwordRegex.test(password)) {
                    $('#passwordError').text('8+ characters with at least 1 uppercase letter and 1 special character.').show();
                    return false;
                    // hasErrors=true;
                } else {
                    $('#passwordError').hide();
                    return true;
                    // hasErrors=false;
                }
            }

            function validatePassword2(inputField, errorElement) {
                var value = inputField.val().trim();
                if (value === "") {
                    $(errorElement).text('Please enter password.').show();
                    return false;
                }
                else {
                    $(errorElement).hide();
                    return true;
                }
            }

            function validatePassword_match() {
                var password1 = $('#password').val().trim();
                var password2 = $('#password_2').val().trim();
                if (password1 !== password2) {
                    $('#passwordError2').text('Both Passwords should match.').show();
                    return true; // Return true when passwords don't match
                } else {
                    $('#passwordError2').hide();
                    return false; // Return false when passwords match
                }
            }

            function validateEmptyEmail(inputField, errorElement) {
                console.log("Validating empty email field...");
                var value = inputField.val().trim();
                if (value === '') {
                    $(errorElement).text('Please enter your email.').show();
                } else {
                    $(errorElement).hide();
                }
            }
            function checkEmailExists(email) {
                $.ajax({
                    url: 'user.php',
                    method: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function (response) {
                        if (!response.success) {
                            $('#emailError').text(response.message);
                            emailErrorPersisted = true;

                        } else {
                            emailErrorPersisted = false;
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