<?php
include 'db_connect.php';

$id = $_GET['id'];
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$searchQuery = isset($_GET['query']) ? $_GET['query'] : ''; // Retrieve the search query

// echo $searchQuery;

$user_role_id = $_SESSION['user_role_id'];
if ($user_role_id != 1 && $user_role_id != 2) {
	header('Location: list-users.php');
}

$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

if (isset($_GET['id']) && $_SERVER["REQUEST_METHOD"] != "POST") {
	$qry = "select * from users where id = $id";
	$data = mysqli_query($conn, $qry);
	$row = mysqli_fetch_array($data);

	$name = $row['name'];
	$email = $row['email'];
	$number = $row['mobile'];
	$role = $row['user_role_id'];
	// $password = $row['password'];
}

$flag = 0;

$name_error = $email_error = $number_error = $password_error = $password_same_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Get other form data
	$name = $_POST["name"];
	$email = $_POST["email"];
	$number = $_POST["number"];
	$role = $_POST["userRole"];
	$query = $_POST["query"];

	// Get the value of the 'page' parameter
	$page = isset($_POST['page']) ? $_POST['page'] : 1;
	

	// Update the database with the provided data
	$sqlUpdate = "UPDATE users SET name='$name', email='$email', mobile='$number', `updated_at`=CURDATE(), `user_role_id`='$role' WHERE id='$id'";
	$result = mysqli_query($conn, $sqlUpdate);

	if ($result) {
		
		$_SESSION['user-edited'] = true;
		$flag = 1;
		// var_dump($query);
		// die();
		// Redirect the user to list-users.php with the page number and search query
		header('Location: list-users.php?page=' . $page . '&query=' . urlencode($query));
		exit; // Make sure to exit after redirection to prevent further execution
	} else {
		die("Update failed: " . mysqli_error($conn));
	}
}

?>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Bootstrap -->
	<link href="css/dashboard.css" rel="stylesheet">
	<style>
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}

		#userRole {
			padding: 5px;
			font-size: 14px;
		}

		.error {
			margin-top: 34px;
			position: absolute;
			font-size: 13px;
			color: red;
		}

		.confirm-model {
			display: none;
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			background-color: #f9f9f9;
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 20px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			z-index: 9999;
			max-width: 400px;
		}

		.confirm-model.show {
			display: block;
		}

		.confirm-model>div {
			margin-bottom: 20px;
		}

		.confirm-model button {
			padding: 10px 20px;
			background-color: #007bff;
			color: #fff;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.confirm-model h3 {
			margin-bottom: 20px;
		}

		.confirm-model button:hover,
		.confirm-model button:focus {
			background-color: #0056b3;
		}

		.ok {
			display: flex;
			justify-content: center;
			align-items: center;
		}

		#ok-text {
			font-size: 16;
		}
	</style>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
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
					<li><a href=" home.php ">Dashboard</a></li>
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
					<li><a href="list-users.php" class="user active">Users</a>
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
				<h1>Edit User</h1>
				<div class="list-contet">
					<div id="errorMessageContainer" style="display: none;">
						<!-- <div class="error-message-div error-msg">
							<img src="images/unsucess-msg.png">
							<strong>UnSuccess!</strong> Your Message hasn't been Sent
						</div> -->
					</div>


					<form id="registrationForm" class="form-edit" method="post" action="edit-user.php?id=<?php echo $id; ?>&page=<?php echo $page; ?>">
						<!-- <div class="form-row">
			</div> -->
						<input type="hidden" name="page" value="<?php echo $page; ?>">
						<input type="hidden" name="query" value="<?php echo urlencode($searchQuery); ?>">
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
								<label for="email">Role: <span>*</span></label>

							</div>
							<?php if ($user_role_id == 1): ?>
								<select id="userRole" name="userRole">
									<option value="1" <?php if ($role == 1)
										echo 'selected'; ?>>Super Admin</option>
								<?php endif; ?>
								<?php if ($user_role_id == 1): ?>
									<option value="2" <?php if ($role == 2)
										echo 'selected'; ?>>Admin</option>
								<?php endif; ?>
								<option value="3" <?php if ($role == 3)
									echo 'selected'; ?>>Manager</option>
								<option value="4" <?php if ($role == 4)
									echo 'selected'; ?>>Team Lead</option>
								<option value="5" <?php if ($role == 5)
									echo 'selected'; ?>>Employee</option>
							</select>
							<p id="userRoleError" class="error" style="display:none;">Please select a role.</p>

						</div>

						<div class="form-row">
							<div class="form-label">
								<label for="number">Mobile Number: <span>*</span> </label>
							</div>
							<div class="input-field">
								<input type="number" name="number" id="number" class="search-box"
									value="<?php echo $number; ?>" maxlength="10"
									oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
								<p id="mobileError" class="error"></p>
							</div>
						</div>
						<div class="form-row">
							<div class="form-label">
								<label><span></span> </label>
							</div>
							<div class="input-field">
								<input id="ok-text" type="submit" class="submit-btn" value="Save">
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

			$('#number').on('input', function () {
				clearTimeout(timeoutIdNumber);
				timeoutIdNumber = setTimeout(() => {
					validateNumber();
				}, 1000)
				$('#mobileError').text('');

			});

			$('#registrationForm').submit(function (e) {
				e.preventDefault(); // Prevent form submission

				// Reset error messages
				$('#emailError').text('');
				$('#nameError').text('');
				$('#mobileError').text('');

				// Get input values
				var email = $('#email').val().trim();
				var name = $('#name').val().trim();
				var number = $('#number').val().trim();

				// Check for empty fields
				var hasErrors = false;

				if (email === '') {
					$('#emailError').text('Please enter your email.');
					hasErrors = true;
				} else if (!validateEmail(email)) {
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
				} else if (!validateNumber(number)) {
					$('#mobileError').text('Please enter a valid number.');
					hasErrors = true;
				}
				// If there are errors, stop form submission
				if (hasErrors) {
					return;
				}
				// if (!validateEmail()) {
				//     hasErrors = true;
				// }
				// $.ajax({
				// 	type: form.method,
				// 	url: form.action,
				// 	data: $(form).serialize(),
				// 	success: function (data) {
				// 		console.log("Form submission successful.");
				// 		// Redirect to the desired page


				// 	}
				// });

				this.submit();
			});

			function isValidEmail(email) {
				var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				return emailRegex.test(email);
			}

			function isValidNumber(number) {
				return !isNaN(number) && number.length === 10;
			}



			// Function to validate name input
			function validateField() {
				var value = $('#name').val().trim();
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
				var inputField = $('#email');
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
				var number = $('#number').val().trim();
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

		});

	</script>
</body>

</html>