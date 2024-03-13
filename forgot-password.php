<?php
include "db_connect_login&registration.php";

$sql = "SELECT * FROM email_templates WHERE slug = 'reset_password'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$subject = $row["subject"];
$content = $row["content"];

function generateToken($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numCharacters = strlen($characters);
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $characters[random_int(0, $numCharacters - 1)];
    }
    return $token;
}
$tokenLimitQuery = "SELECT * FROM settings WHERE id=1";
$resultToken = mysqli_query($conn, $tokenLimitQuery);

if (!$resultToken) {
    // Query failed, handle the error
    echo "Error: " . mysqli_error($conn);
} else {
    // Check if any rows were returned
    if (mysqli_num_rows($resultToken) > 0) {
        // Fetch the row
        $rowToken = mysqli_fetch_array($resultToken);
        // Extract the token_expiry_time value
        $tokenTimeLimit = $rowToken["token_expiry_time"];
        // Dump the value for debugging
        var_dump($tokenTimeLimit);
    } else {
        // No rows returned
        echo "No settings found for ID 1";
    }
}

$forgot_pass_mail = false; // Initialize the variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        $not_registered = true;
    } else {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];
        $generatedToken = generateToken(32);
        $sqlInsert = "INSERT INTO security_token (token_user_id, token_type, token_value, token_expiry_time) 
              VALUES ('$user_id', 'reset_password', '$generatedToken', DATE_ADD(NOW(), INTERVAL $tokenTimeLimit MINUTE))";

        $insertResult = mysqli_query($conn, $sqlInsert);
        if ($insertResult) {
            require('script.php');
            require('template.php');
            $content = str_replace("YOUR_RESET_PASSWORD_LINK_HERE", "http://localhost/php-crud-ui/reset-password.php?token=$generatedToken", $content);
            sendMail("$email", "$subject", "$content");
            $forgot_pass_mail = true;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .forgot-pass-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .forgot-pass-form {
            background-color: #f8f9fa; /* Light gray background */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .forgot-pass-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #000000; /* Black text */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #000000; /* Black text */
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc; /* Light gray border */
            border-radius: 3px;
            box-sizing: border-box;
        }

        .btn-send-mail {
            width: 100%;
            padding: 10px;
            background-color: black; /* Blue button */
            color: #ffffff; /* White text */
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-send-mail:hover {
            background-color: black; /* Darker blue on hover */
        }
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <div class="forgot-pass-container">
        <div class="forgot-pass-form">
            <h2>Forgot Password</h2>
            <form method="post" action="forgot-password.php">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <?php if($not_registered) :?> <p class="error">Email not registered</p>
                        <?php endif;?>
                </div>
                <button type="submit" class="btn-send-mail">Send Mail</button>
            </form>
        </div>
    </div>
    <script>
        const forgot_pass = <?php echo json_encode($forgot_pass_mail); ?>; 
		if (forgot_pass) {
			Swal.fire({
				title: "Mail Sent!!",
				text: "Check Your Inbox !",
				icon: "success",
				confirmButtonColor: "#FF651B",
				iconColor: "#FF651B"
			});
		}
    </script>
</body>
</html>
