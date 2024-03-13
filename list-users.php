<?php
include 'db_connect.php';
$user_role_id = $_SESSION["user_role_id"];

// Initialize variables with default values
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';

// Handle search query from POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query'])) {
	$searchQuery = trim($_POST['query']);
	$sortColumn = 'created_at';
	$page = 1;
}

// Sanitize search query to remove single and double quotes
$searchQuery = str_replace("'", "", $searchQuery);
$searchQuery = str_replace('"', "", $searchQuery);


$getRowsQuery = "SELECT * FROM settings WHERE id = 1";
$rowsResult = mysqli_query($conn, $getRowsQuery);
if (mysqli_num_rows($rowsResult) > 0) {
	$settingsRow = mysqli_fetch_array($rowsResult);
	$recordsPerPage = $settingsRow['rows_per_page'];// Pagination variables
	$dateFormat = $settingsRow['date_format'];
}

// $recordsPerPage = 8;


$startFrom = ($page - 1) * $recordsPerPage;

// Total records query with prepared statement
// $totalRecordsQuery = "SELECT COUNT(*) AS total FROM `users` WHERE name LIKE ? OR email LIKE ? OR mobile LIKE ?";
if ($user_role_id == 1) {
	$totalRecordsQuery = "SELECT COUNT(*) AS total 
                          FROM `users` u 
                          JOIN `roles` r ON u.user_role_id = r.role_id
                          WHERE u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ?";
} else if ($user_role_id == 2) {
	$totalRecordsQuery = "SELECT COUNT(*) AS total 
                          FROM `users` u 
                          JOIN `roles` r ON u.user_role_id = r.role_id
                          WHERE (u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ?) 
                          AND u.user_role_id NOT IN (1, 2)";
} else {
	$totalRecordsQuery = "SELECT COUNT(*) AS total 
                          FROM `users` u 
                          JOIN `roles` r ON u.user_role_id = r.role_id
                          WHERE (u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ?) 
                          AND u.user_role_id NOT IN (1, 2)";
}

$totalRecordsStmt = $conn->prepare($totalRecordsQuery);
$searchParam = "%$searchQuery%";
$totalRecordsStmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$totalRecordsStmt->execute();
$totalRecordsResult = $totalRecordsStmt->get_result();
$totalRecordsRow = $totalRecordsResult->fetch_assoc();
$totalRecords = $totalRecordsRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);


// Redirect if page number is invalid
if ($page > $totalPages && $totalPages > 0) {
	header("Location: {$_SERVER['PHP_SELF']}?page=1");
	exit;
}

// Handle session flags
$flag_edit_user = isset($_SESSION["user-edited"]);
if ($flag_edit_user) {
	unset($_SESSION["user-edited"]);
}

$flag_deleted_user = isset($_SESSION["user-deleted"]);
if ($flag_deleted_user) {
	unset($_SESSION["user-deleted"]);
}

$flag_added_user = isset($_SESSION["user-added"]);
if ($flag_added_user) {
	unset($_SESSION["user-added"]);
}

// Fetch user details from session
$id = $_SESSION['id'];
$fullName = $_SESSION["name"];
$nameParts = explode(' ', $fullName);
$firstName = $nameParts[0];

if ($user_role_id == 1) {
	$sql = "SELECT u.*, r.role_name, DATE_FORMAT(u.created_at, '%Y-%m-%d') AS created_at 
            FROM `users` u 
            JOIN `roles` r ON u.user_role_id = r.role_id
            WHERE u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ? 
            ORDER BY $sortColumn $sortOrder 
            LIMIT ?, ?";
} else if ($user_role_id == 2) {
	$sql = "SELECT u.*, r.role_name, DATE_FORMAT(u.created_at, '%Y-%m-%d') AS created_at 
            FROM `users` u 
            JOIN `roles` r ON u.user_role_id = r.role_id
            WHERE (u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ?) 
            AND u.user_role_id NOT IN (1, 2) 
            ORDER BY $sortColumn $sortOrder 
            LIMIT ?, ?";
} else {
	$sql = "SELECT u.*, r.role_name, DATE_FORMAT(u.created_at, '%Y-%m-%d') AS created_at 
            FROM `users` u 
            JOIN `roles` r ON u.user_role_id = r.role_id
            WHERE (u.name LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR r.role_name LIKE ?) 
            AND u.user_role_id NOT IN (1, 2) 
            ORDER BY $sortColumn $sortOrder 
            LIMIT ?, ?";
}


$stmt = $conn->prepare($sql);
$startIndex = max(0, ($page - 1) * $recordsPerPage);

$stmt->bind_param("ssssii", $searchParam, $searchParam, $searchParam, $searchParam, $startIndex, $recordsPerPage);
$stmt->execute();
$result = $stmt->get_result();


?>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin</title>
	<style>
		.search-btn-container {
			display: flex;

			align-items: center;
		}

		#searchBtn {
			cursor: pointer;
			margin-top: 4px;
			padding: 7px;
			background-color: #FF651B;
			border: none;
			border-radius: 5px;
			color: white;
		}

		.paginaton-div .disabled {
			color: #aaa;
			/* Change color to grey */
			pointer-events: none;
			border: 1px solid grey;
			/* Disable pointer events */
		}

		.paginaton-div {
			bottom: 0;
			/* position: absolute; */
			left: 0;
		}

		.delete-button {
			border: none;
			cursor: pointer;
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

		.list-contet {
			height: auto;
		}
	</style>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
		integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<!-- Bootstrap -->
	<link href="css/dashboard.css" rel="stylesheet">

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
					<li class=""><a href="home.php ">Dashboard</a></li>
					<li class="active"><a href=" list-users.php ">Users</a></li>
					<li><a href="manage-contact.php">Queries</a></li>
					<?php if ($user_role_id == 1): ?>
                        <li><a href=" settings.php ">Settings</a></li>
                    <?php endif ?>
					<!-- <li><a href=" geoloclist.php ">Configuration</a></li></ul> -->

			</div>
		</div>
	</div>
	<div class="confirm-model" style="display:<?php echo $flag ? 'block' : 'none'; ?>">
		<h3>Are you sure you want to Delete the User? </h3>
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
		<li>List Users</li>
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
				<h1>List Users</h1>
				<div class="list-contet">
					<div class="form-left">
						<div class="form">

							<form id="searchForm" method="POST">
								<input id="search-input" name="query" type="text" class="search-box search-upper"
									placeholder="Search.." value="<?php echo $searchQuery; ?>" />
								<div class="search-btn-container">
									<button id="searchBtn" type="submit" value="submit">Search</button>
								</div>
							</form>

						</div>
						<?php if ($user_role_id == 1 || $user_role_id == 2): ?>
							<input type="button" id="add-btn" class="submit-btn add-user" value="Add More Users">
						<?php endif; ?>


					</div>
					<?php if ($result): ?>
						<?php if (mysqli_num_rows($result) > 0): ?>

							<table width="100%" cellspacing="0">
								<tbody>
									<tr>
										<?php
										// Define arrow icons based on sort order and column
										$arrowUpIconId = ($sortColumn == 'id' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconId = ($sortColumn == 'id' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconName = ($sortColumn == 'name' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconName = ($sortColumn == 'name' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconEmail = ($sortColumn == 'email' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconEmail = ($sortColumn == 'email' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconMobile = ($sortColumn == 'mobile' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconMobile = ($sortColumn == 'mobile' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconCreatedOn = ($sortColumn == 'created_on' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconCreatedOn = ($sortColumn == 'created_at' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconCreatedDate = ($sortColumn == 'created_at' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconCreatedDate = ($sortColumn == 'created_at' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';
										$arrowUpIconRoles = ($sortColumn == 'role_name' && $sortOrder == 'ASC') ? '<i class="fa-solid fa-arrow-up"></i>' : '';
										$arrowDownIconRoles = ($sortColumn == 'role_name' && $sortOrder == 'DESC') ? '<i class="fa-solid fa-arrow-down"></i>' : '';

										// Output the table headers
										echo "<th width='100px'><a href=\"?sort=name&order=" . ($sortColumn == 'name' ? ($sortOrder == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . "&query=" . htmlspecialchars(urlencode($searchQuery)) . "&sortColumn=name\" style='color: #444;'>Name</a> $arrowUpIconName $arrowDownIconName</th>" .
											"<th width='120px'><a href=\"?sort=email&order=" . ($sortColumn == 'email' ? ($sortOrder == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . "&query=" . htmlspecialchars(urlencode($searchQuery)) . "&sortColumn=email\" style='color: #444;'>Email</a> $arrowUpIconEmail $arrowDownIconEmail</th>" .
											"<th width='120px'><a href=\"?sort=mobile&order=" . ($sortColumn == 'mobile' ? ($sortOrder == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . "&query=" . htmlspecialchars(urlencode($searchQuery)) . "&sortColumn=mobile\" style='color: #444;'>Number</a> $arrowUpIconMobile $arrowDownIconMobile</th>" .
											"<th width='100px'><a href=\"?sort=created_at&order=" . ($sortColumn == 'created_at' ? ($sortOrder == 'ASC' ? 'DESC' : 'ASC') : 'DESC') . "&query=" . htmlspecialchars(urlencode($searchQuery)) . "&sortColumn=created_at\" style='color: #444;'>Created on</a> " . $row['formatted_created_at'] . " $arrowUpIconCreatedDate $arrowDownIconCreatedDate</th>" .
											"<th width='100px'><a href=\"?sort=role_name&order=" . ($sortColumn == 'role_name' ? ($sortOrder == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . "&query=" . htmlspecialchars(urlencode($searchQuery)) . "&sortColumn=role_name\" style='color: #444;'>Roles</a> $arrowUpIconRoles $arrowDownIconRoles</th>";

										// Conditionally show the "Options" column based on your condition
										if ($user_role_id == 1 || $user_role_id == 2) {
											echo "<th width='40px'>Options</th>";
										}

										echo "</tr>";

										// Output the table rows
										while ($row = $result->fetch_assoc()) {
											echo '<tr>' .
												'<td>' . $row['name'] . '</td>' .
												'<td>' . $row['email'] . '</td>' .
												'<td>' . $row['mobile'] . '</td>' .
												'<td>' . date($dateFormat, strtotime($row['created_at'])) . '</td>' . // Format the date
												'<td>' . $row['role_name'] . '</td>';
											// Display the role name
								
											// Conditionally show the "Options" column based on your condition
											if ($user_role_id == 1 || $user_role_id == 2) {
												echo '<td>' .
													'<a href="edit-user.php?id=' . $row["id"] . '&page=' . $page . '&query=' . urlencode($searchQuery) . '"><img src="images/edit-icon.png"></a>' .
													'&nbsp;' .
													'&nbsp;' .
													"<button class='delete-button' onclick='showConfirmationModel({$row['id']})'><img src='images/cross.png'></button>" .
													'</td>';

											}

											echo '</tr>';
										}

										?>
								</tbody>
							</table>
						<?php else: ?>
							<p>No users found.</p>

						<?php endif; ?>
					<?php endif; ?>

					<?php
					echo "<div class='paginaton-div'>";
					$prevPage = max($page - 1, 1); // Ensure previous page doesn't go below 1
					$nextPage = min($page + 1, $totalPages); // Ensure next page doesn't exceed total pages
					
					echo "<ul>";
					echo "<li><a href='?page={$prevPage}&query={$searchQuery}&order={$sortOrder}&sort={$sortColumn}'" . ($prevPage == $page ? " class='disabled'" : "") . ">Previous</a></li>";


					// Calculate the range of pages to display
					$startPage = max(1, min($page - 1, $totalPages - 2));
					$endPage = min($startPage + 2, $totalPages);

					// Display pagination links
					// Display pagination links
					for ($i = $startPage; $i <= $endPage; $i++) {
						if ($i == $page) {
							echo "<li><a class='active'>$i</a></li>";
						} else {
							echo "<li><a href='?page={$i}&query={$searchQuery}&order={$sortOrder}&sort={$sortColumn}'" . ($i == $page ? " class='active'" : "") . ">$i</a></li>";
						}
					}


					echo "<li><a href='?page={$nextPage}&query={$searchQuery}&order={$sortOrder}&sort={$sortColumn}'" . ($nextPage == $page ? " class='disabled'" : "") . ">Next</a></li>";


					echo "</ul>";

					// Display total users
					echo "<p style='margin-top: 10px'>Total users: $totalRecords</p>";
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
	<script>
		const user_edited = <?php echo json_encode($flag_edit_user); ?>; // Echo the value of $flag_edit_user
		if (user_edited) {
			Swal.fire({
				title: "Great!",
				text: "User edited successfully!",
				icon: "success",
				confirmButtonColor: "#FF651B",
				iconColor: "#FF651B"
			});
		}
		const flag_deleted_user = <?php echo json_encode($flag_deleted_user); ?>;
		if (flag_deleted_user) {
			Swal.fire({
				title: "Great!",
				text: "User deleted successfully!",
				icon: "success",
				confirmButtonColor: "#FF651B",
				iconColor: "#FF651B"
			});
		}

		const flag_added_user = <?php echo json_encode($flag_added_user); ?>;
		if (flag_added_user) {
			Swal.fire({
				title: "Great!",
				text: "User added successfully!",
				icon: "success",
				confirmButtonColor: "#FF651B",
				iconColor: "#FF651B"
			});
		}


		// searchQuery = decodeURIComponent(searchQuery.replace(/\+/g, '%20'));

		// var input = document.getElementById('search-input');

		// Replace '+' with spaces in the input box
		// input.value = searchQuery.replace(/\+/g, ' ');

		// input.addEventListener('input', function () {
		// 	// Get the current value of the input
		// 	var value = this.value;

		// 	// Save the current cursor position
		// 	var cursorPosition = this.selectionStart;

		// 	// Prevent the default behavior to preserve cursor position
		// 	this.addEventListener('keydown', function (event) {
		// 		// If the event is not a backspace or delete key, prevent default behavior
		// 		if (event.key !== 'Backspace' && event.key !== 'Delete') {
		// 			event.preventDefault();
		// 		}
		// 	});

		// 	// Set the input value to update the cursor position
		// 	this.value = value;

		// 	// Set the cursor position to the saved position
		// 	this.setSelectionRange(cursorPosition, cursorPosition);
		// });

		// document.addEventListener('DOMContentLoaded', function () {
		// 	var searchInput = document.getElementById('search-input');
		// 	if (searchInput) {
		// 		searchInput.focus(); // Set focus on the search input field
		// 		// Set the cursor to the end of the input value
		// 		searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
		// 		searchInput.addEventListener('input', function () {
		// 			this.parentNode.submit(); // Submit the parent form when input changes
		// 		});
		// 	} else {
		// 		console.error('Input field with ID "search-input" not found.');
		// 	}
		// });

		//add user
		document.addEventListener('DOMContentLoaded', function () {
			var okButton = document.getElementById('add-btn');
			if (okButton) {
				okButton.addEventListener('click', function () {
					window.location.href = 'user.php';
				});
			} else {
				console.error('Button with ID "add-btn" not found.');
			}
		});
		//delete
		var flag = <?php echo $flag ? 'true' : 'false'; ?>;


		function showConfirmationModel(id) {
			var confirmationModel = document.querySelector('.confirm-model');
			if (confirmationModel) {
				confirmationModel.style.display = 'block';
				document.getElementById('confirm_delete').value = 'yes';
				// Construct the action URL with sortOrder and sortColumn parameters
				var url = 'delete.php?id=' + id + '&query=' + encodeURIComponent('<?php echo htmlspecialchars($searchQuery); ?>') + '&page=<?php echo htmlspecialchars($page); ?>&sortOrder=<?php echo htmlspecialchars($sortOrder); ?>&sortColumn=<?php echo htmlspecialchars($sortColumn); ?>';
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