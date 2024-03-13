<?php
include 'db_connect.php';

$user_role_id = $_SESSION["user_role_id"];

if ($user_role_id != 1) {
    header("Location: list-users.php");
}

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];
$token_time_updated = false;
$id = $_SESSION['id'];


// Initialize variables
$name = $email = $number = "";
$token_time_updated = false;

// Fetch token expiry time
$tokenTime = isset($_POST['tokenTime']) ? $_POST['tokenTime'] : 5;
$rows = isset($_POST['rows']) ? $_POST['rows'] : 8;
$name = isset($_POST['name']) ? $_POST['name'] : "";
$email = isset($_POST['email']) ? $_POST['email'] : "";
$number = isset($_POST['number']) ? $_POST['number'] : "";
$dateFormat = isset($_POST["dateFormat"]) ? $_POST["dateFormat"] :""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Fetch existing settings for the user
    $Data = "SELECT * FROM settings WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $Data);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result2 = mysqli_stmt_get_result($stmt);
    $row2 = mysqli_fetch_array($result2);

    $updateUserDetails = "UPDATE users SET name = ?, email = ?, mobile = ? WHERE user_role_id = 1";
    $stmt = mysqli_prepare($conn, $updateUserDetails);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $number);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // echo $dateFormat;
    // die();
    // Update or insert token expiry time
    if ($row2) {
        $updateSettings = "UPDATE settings SET token_expiry_time = ?, rows_per_page = ?, `date_format` = ? WHERE id = 1";
        $stmt = mysqli_prepare($conn, $updateSettings);
        mysqli_stmt_bind_param($stmt, "iis", $tokenTime, $rows, $dateFormat);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
     else { //hta de
        $insertSettings = "INSERT INTO settings (setting_name, setting_slug, user_id, token_expiry_time, rows_per_page, `date_format`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertSettings);
        $settingName = "token expiry time";
        $settingSlug = "token_expiry";
        mysqli_stmt_bind_param($stmt, "ssiiii", $settingName, $settingSlug, $id, $tokenTime,$dateFormat, $rows);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $token_time_updated = true;
}

// Fetch user details after possible updates
$sql = "SELECT * FROM users WHERE user_role_id = 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$name = $row['name'];
$email = $row['email'];
$num = $row['mobile'];

$sqlTime = "SELECT * FROM settings WHERE id=1";
$result = mysqli_query($conn, $sqlTime);
$row = mysqli_fetch_array($result);
$tokenTime = $row["token_expiry_time"];
$rows = $row['rows_per_page'];
$dateFormat = $row['date_format'];

?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>

    <!-- Bootstrap -->
    <link href="css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .error {
            margin-top: 0px;
            position: absolute;
            font-size: 13px;
            color: red;
        }

        #saveButton {
            padding: 10px;
            background-color: #ff651b;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .list-content {
            margin-top: 10px;
        }

        .super-admin-details {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .details-table {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
        }

        table td {
            padding: 18px 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: white;
        }

        /* Style for the expiry time label */
        .expiry-time label {
            font-weight: bold;
            margin-bottom: 10px;
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
                    <li class=""><a href="home.php">Dashboard</a></li>
                    <li><a href="list-users.php">Users</a></li>
                    <li><a href="manage-contact.php">Queries</a></li>
                    <?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
                    <!-- <li><a href="geoloclist.php">Configuration</a></li> -->
                </ul>
            </div>
        </div>
    </div>

    <div class="clear"></div>
    <div class="clear"></div>
    <div class="content">
        <div class="wrapper">
            <div class="left_sidebr">
                <ul>
                    <li><a href="home.php" class="dashboard">Dashboard</a></li>
                    <li><a href="list-users.php" class="user">Users</a></li>
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
                                <li class="settings active"><a class="settings-text" href="#">Settings</a></li>
                            <?php endif ?>
                           
                            <li><a href="manage-contact.php">Manage Contact Request</a></li>
                            <!-- <li><a href="#">Manage Limits</a></li> -->
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="right_side_content">
                <h1>Settings</h1>
                <div class="list-content">
                    <div class="super-admin-details">
                        <h2>Super Admin Details</h2>
                        <form id="updateSuperAdmin" method="post"
                            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="details-table">
                                <table>
                                    <tr>
                                        <td>Name:</td>
                                        <td>
                                            <input type="text" id="nameInput" name="name" value="<?php echo $name; ?>">
                                            <p id="nameError" class="error"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Email:</td>
                                        <td>
                                            <input type="email" id="emailInput" name="email"
                                                value="<?php echo $email; ?>">
                                            <p id="emailError" class="error"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mobile Number:</td>
                                        <td>
                                            <input type="text" id="numberInput" name="number"
                                                value="<?php echo $num; ?> " maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            <p id="mobileError" class="error"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Token Expiry Time:</td>
                                        <td>
                                            <select id="tokenExpirySelect" name="tokenTime">
                                                <option value="5" <?php if ($tokenTime == 5)
                                                    echo "selected"; ?>>5 minutes
                                                </option>
                                                <option value="10" <?php if ($tokenTime == 10)
                                                    echo "selected"; ?>>10
                                                    minutes</option>
                                                <option value="15" <?php if ($tokenTime == 15)
                                                    echo "selected"; ?>>15
                                                    minutes</option>
                                                <option value="20" <?php if ($tokenTime == 20)
                                                    echo "selected"; ?>>20
                                                    minutes</option>
                                                <option value="30" <?php if ($tokenTime == 30)
                                                    echo "selected"; ?>>30
                                                    minutes</option>
                                                <option value="50" <?php if ($tokenTime == 50)
                                                    echo "selected"; ?>>50
                                                    minutes</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Rows Per Page: </td>
                                        <td>
                                            <select id="rowsPerTable" name="rows">
                                                <option value="5" <?php if ($rows == 5)
                                                    echo "selected"; ?>>5
                                                </option>
                                                <option value="8" <?php if ($rows == 8)
                                                    echo "selected"; ?>>8
                                                </option>
                                                <option value="10" <?php if ($rows == 10)
                                                    echo "selected"; ?>>10
                                                </option>
                                                <option value="15" <?php if ($rows == 15)
                                                    echo "selected"; ?>>15
                                                </option>
                                                <option value="25" <?php if ($rows == 25)
                                                    echo "selected"; ?>>25
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Date Format:</td>
                                        <td>
                                            <select id="dateFormat" name="dateFormat">
                                                <option value="Y-m-d" <?php if ($dateFormat == 'Y-m-d')
                                                    echo "selected"; ?>>
                                                    YYYY-MM-DD</option>
                                                <option value="m/d/Y" <?php if ($dateFormat == 'm/d/Y')
                                                    echo "selected"; ?>>
                                                    MM/DD/YYYY</option>
                                                <option value="d/m/Y" <?php if ($dateFormat == 'd/m/Y')
                                                    echo "selected"; ?>>
                                                    DD/MM/YYYY</option>
                                                <!-- Add more options as needed -->
                                            </select>
                                        </td>
                                    </tr>


                                </table>
                                <button type="submit" id="saveButton" style="margin-top: 10px;">Save</button>
                            </div>
                        </form>
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
    <script>
        var tokentimeUpdated = <?php echo json_encode($token_time_updated); ?>;

        if (tokentimeUpdated) {
            Swal.fire({
                title: "Great!",
                text: "Details Updated successfully!",
                icon: "success",
                confirmButtonColor: "#FF651B",
                iconColor: "#FF651B"
            });
        }
        // Function to update token expiry time
        function updateTokenExpiry() { //hta dena isko
            var tokenExpiry = document.getElementById('tokenExpirySelect').value;
            var rows = document.getElementById('rowsPerTable').value;

            // $.ajax({
            //     type: "POST",
            //     url: "settings.php",
            //     data: {
            //         tokenTime: tokenExpiry
            //         // rows: rows
            //     },
            //     success: function (response) {
            //         // console.log(response); // Log success message
            //     },
            //     error: function (xhr, status, error) {
            //         console.error(xhr.responseText); // Log error message
            //     }
            // });
        }

        function validateField() {
            var value = $('#nameInput').val().trim();
            var containsSpecialChars = /[^a-zA-Z\s]/.test(value); // Regular expression to check for non-alphabetic characters

            if (value === '') {
                $("#nameError").text('Please enter your name.').show();
                return false;
            } else if (containsSpecialChars) {
                $("#nameError").text('Please enter a valid name.').show();
                return false;
            } else {
                $("#nameError").hide();
                return true;
            }
        }


        function validateEmail() {
            var inputField = $('#emailInput');
            var errorElement = $('#emailError');
            var value = inputField.val().trim(); // Trim leading and trailing spaces
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var dotPosition = value.lastIndexOf(".");
            var afterDot = value.substring(dotPosition + 1);

            if (value === '') {
                errorElement.text('Please enter your email.');
                return false;
            } else if (!emailRegex.test(value)) {
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
            var number = $('#numberInput').val().trim();
            if (number === '' || isNaN(number) || number.length !== 10 || number < 0) {
                $('#mobileError').text('Please enter a valid 10-digit number.').show();
                return false; // Validation failed
            } else if (/^0+$/.test(number)) {
                $('#mobileError').text('Please enter a valid number.').show();
                return false; // Validation failed
            } else {
                $('#mobileError').hide();
                return true; // Validation passed
            }
        }
        $(document).ready(function () {
			var timeoutIdEmail;
			var timeoutIdNumber, timeoutIdPassword, timeoutIdPassword2;
			var emailErrorPersisted = false;

			$('#nameInput').on('input', function () {
				validateField();
			});

			$('#emailInput').on('input', function () {
				clearTimeout(timeoutIdEmail); // Clear previous timeout
				var emailInput = $(this).val().trim();
				var emailError = $('#emailError');
				timeoutIdEmail = setTimeout(function () {
					validateEmail(emailInput, emailError);
					if (emailError.text() === '') {
						// Email is valid, check if it exists
						// checkEmailExists(emailInput);
					}
				}, 1000); // Set new timeout
				// Hide error message immediately when user starts typing again
				emailError.text('');
			});

			$('#numberInput').on('input', function () {
				clearTimeout(timeoutIdNumber);
				timeoutIdNumber = setTimeout(() => {
					validateNumber();
				}, 1000)
				$('#mobileError').text('');

			});
        });


        // Handle form submission
        $('#updateSuperAdmin').submit(function (event) {
            event.preventDefault(); // Prevent default form submission
            $('#emailError').text('');
            $('#nameError').text('');
            $('#mobileError').text('');

            var hasErrors = false;
            var email = $('#emailInput').val().trim();
            var name = $('#nameInput').val().trim();
            var number = $('#numberInput').val().trim();

            if (email === '') {
                $('#emailError').text('Please enter your email.');
                hasErrors = true;
            } else if (!validateEmail()) {
                $('#emailError').text('Please enter a valid email.');
                hasErrors = true;
            }
            if (name === '') {
                $('#nameError').text('Please enter your name.');
                hasErrors = true;
            }
            else if (!validateField()) {
                $('#nameError').text('Please enter a valid name.');
                hasErrors = true;
            }
            if (number === '') {
                $('#mobileError').text('Please enter your number.');
                hasErrors = true;
            } else if (!validateNumber()) {
                $('#mobileError').text('Please enter a valid number.');
                hasErrors = true;
            }
            if (hasErrors) {
                return;
            }
            updateTokenExpiry(); // Call the function to update token expiry
            this.submit();
        });
    </script>
</body>

</html>