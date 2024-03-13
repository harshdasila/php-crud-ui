 <?php
include 'db_connect_login&registration.php';
$flag = false;
$password = "";


$token = $_GET["token"];
$sqlgetuser = "SELECT * FROM security_token where token_value='$token'";
$result = mysqli_query($conn, $sqlgetuser);
$row = mysqli_fetch_assoc($result);
$userID = $row["token_user_id"];


// 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $userID = $_GET["userID"];
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$userID'";
    $update_result = mysqli_query($conn, $update_sql);

    if ($update_result) {
        $flag = true;
    } else {
        echo "Failed to update password: " . mysqli_error($conn);
    }
}
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        .button-container{
            margin-top: 15px;
            padding: 10px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        #back-btn {
            font-size: 14px;
            background-color: red;
            padding: 10px 26px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block; 
            font-weight: 400;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }
        .error {
            position: absolute;
            color: red;
            font-size: 0.8em; /* Adjust font size for error messages */
        }

        .form-input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .submit-button{
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-btn{
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        body{
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        /* Style for form container */
        .form-container {
            margin-top: 100px;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 450px;
        }

        /* Center align text */
        .center-text {
            text-align: center;
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
            padding: 50px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            max-width: 400px;
        }
        .confirm-model button:hover,
        .confirm-model button:focus {
            background-color: #0056b3;
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
        .confirm-model.show {
            display: block;
        }

        .confirm-model > div {
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
        input:focus {
            outline: none !important;
            border:2px solid #007bff;
            box-shadow: 0 0 1px #007bff;
        }
        .star{
            color: red;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="confirm-model" style="display:<?php if($flag==true){echo 'block';} else{echo 'none';}?>">
        <h3>Password Updated Successfully</h3>
        <div class="ok">
            <button id="ok-text">OK</button>
        </div>
    </div>

    <div class="center-text">
        <h1>Change Password</h1>
    </div>
    <br>
    <form id="change-password" class="registration-form" method="POST" action="reset-password.php?userID=<?php echo $userID;?>">
        <label for="password">Enter Your Password:<span class="star">*</span></label>
        <input type="password" name="password" id="password" class="form-input" value="<?php echo $password; ?>"><br>
        <span class="error" id="password-error"></span>
        <br><br>
        <label for="password_2">Confirm Your Password:<span class="star">*</span></label>
        <input type="password" name="password_2" id="password_2" class="form-input" value="<?php echo $password; ?>"><br>
        <span class="error" id="password-error-confirm"></span>
        <div class="button-container">
            <button id="cancelButton" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-button">Change password</button>
        </div>
        

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
    $('#password, #password_2').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#change-password').submit();
        }
    });
    

    // function handleCancel() {
    //     var email = "<?php echo $email; ?>";
    //     // Perform database operation to delete token
    //     console.log(email,"this");
    //     $.ajax({
    //         url: 'deletetoken.php',
    //         type: 'GET',
    //         data: { email: email },
    //         success: function(response) {
    //             // Handle success response here
    //             console.log(response);
    //             // Redirect to desired page after token deletion
    //             window.location.href = 'login.php';
    //         },
    //         error: function(xhr, status, error) {
    //             // Handle error
    //             console.error(xhr.responseText);
    //             // Optionally, display an error message to the user
    //         }
    //     });
    // }


    // $(document).ready(function() {
    //     // Bind click event to the Cancel button
    //     $('#cancelButton').click(function(e) {
    //         e.preventDefault(); // Prevent default form submission behavior
    //         // Call the handleCancel function
    //         handleCancel();
    //     });
    // });

    

    // Function to redirect to login page after successful password change
    document.addEventListener('DOMContentLoaded', function() {
        var okButton = document.getElementById('ok-text');
        if (okButton) { 
            okButton.addEventListener('click', function() {
                window.location.href = 'login.php'; 
            });
        } else {
            console.error('Button with ID "ok-text" not found.');
        }
    });

    // Bind input event listeners for password fields
    $('#password').on('input', function() {
        validatePassword($(this), '#password-error');
    });

    $('#password_2,#password').on('input', function() {
        validatePasswordMatch($('#password').val().trim(), $(this).val().trim(), '#password-error-confirm');
    });

    // Function to validate password strength
    function validatePassword(inputField, errorElement) {
        var value = inputField.val().trim();
        if (value === '') {
            $(errorElement).text('Please enter your password.');
        } else if (!isStrongPassword(value)) {
            $(errorElement).text('8+ characters with at least 1 uppercase letter and 1 special character.');
        } else {
            $(errorElement).text('');
        }
    }

    // Function to validate password match
    function validatePasswordMatch(password1, password2, errorElement) {
        if (password1 !== password2) {
            $(errorElement).text('Passwords do not match.');
        } else {
            $(errorElement).text('');
        }
    }

    // Function to check if password meets strength criteria
    function isStrongPassword(password) {
        var uppercaseRegex = /[A-Z]/;
        var specialCharacterRegex = /[@]/;

        return (password.length >= 8 && uppercaseRegex.test(password) && specialCharacterRegex.test(password));
    }

    $('#cancelButton').click(function(e) {
        e.preventDefault(); // Prevent default form submission behavior
        // Call the handleCancel function
        handleCancel();
    });

    // Form submission handling
    $('#change-password').submit(function(e) {
        e.preventDefault(); // Prevent form submission

        // Get password and confirm password values
        var password = $('#password').val().trim();
        var confirmPassword = $('#password_2').val().trim();

        // Reset error messages
        $('#password-error').text('');
        $('#password-error-confirm').text('');

        // Check if password is empty
        if (password === '') {
            $('#password-error').text('Please enter your password.');
            return;
        }

        // Check if password meets strength criteria
        if (!isStrongPassword(password)) {
            $('#password-error').text('8+ characters with at least 1 uppercase letter and 1 special character.');
            return;
        }

        // Check if confirm password is empty
        if (confirmPassword === '') {
            $('#password-error-confirm').text('Please confirm your password.');
            return;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            $('#password-error-confirm').text('Passwords do not match.');
            return;
        }

        // If all validations pass, submit the form
        this.submit();
    });

    document.addEventListener('DOMContentLoaded', function() {
            var okButton = document.getElementById('ok-text');
            if (okButton) { 
                okButton.addEventListener('click', function() {
                    window.location.href = 'login.php'; 
                });
            } else {
                console.error('Button with ID "ok-text" not found.');
            }
        });
</script>




</body>
</html>
