<?php
include 'db_connect.php';

$flag=0; //to show modal

$name = $number = $password = $email = "";
$name_error = $email_error = $number_error = $password_error = $password_same_error = false; //setting default as false

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $number = $_POST["number"];
    $password = $_POST["password"];
    $password_2 = $_POST["password_2"];
    
    // Error checking
    if (!preg_match("/^[a-zA-Z ]*$/", $name) || $name === "") {
        $name_error = true;
    }
    
    if (strpos($email, '@') === false) {
        $email_error = true; 
    }
    if (strlen($number) !== 10 || !ctype_digit($number)) {
        $number_error = true; 
    }
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[^a-zA-Z0-9]/", $password)) {
        $password_error = true; 
    }
    if($password!==$password_2){
        $password_same_error = true;
    }

    // Insertion only if there are no errors
    if (!$name_error && !$email_error && !$number_error && !$password_error && !$password_same_error) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sqlInsert = "INSERT INTO crud (name, email, mobile, password) VALUES ('$name', '$email', '$number', '$hashed_password')";
        
        $result = mysqli_query($conn, $sqlInsert);
        if ($result) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .buttons-container{
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 15px;
        }
        #back-btn{
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
        #back-btn:hover,.submit-button:hover{
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
    padding: 20px; /* Increased padding */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    width: 380px;
    height: 180px;
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
            border:2px solid #007bff;
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
.registration-form > p {
            position: absolute;
            font-size: 14px;
            color: red;
            margin-top: -29px;
        }
.confirm-model button:hover,
.confirm-model button:focus {
    background-color: #0056b3;
}
.required-fields{
            font-size: 13px;
            margin-bottom: 20px;
            margin-top: -4px;
        }
        .star{
            color: red;
        }
        .data-container{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h3{
            font-size: 1.8em;
        }


</style>
</head>

<body>
    <div>
        <div class="confirm-model" style="display:<?php if($flag==1){echo 'block';} else{echo 'none';}?>">
            <div class="data-container">
                <h3>Data Added Successfully </h3>
            </div>
            
            <div class="ok">
                <button id="ok-text">OK</button>
            </div>
        </div>

        <form id="registrationForm" class="registration-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="user-details-container">
                <h1>ADD USER DETAILS</h1>
            </div>
            
            <label for="name">Name:<span class="star">*</span></label>
            <input type="text" name="name" id="name" class="form-input" value="<?php echo $name;?>">
            <p id="nameError" class="error"></p>
            
            <label for="email">Email:<span class="star">*</span></label>
            <input type="text" name="email" id="email" class="form-input" value="<?php echo $email;?>">
            <p id="emailError" class="error"></p>
            
            <label for="number">Mobile Number:<span class="star">*</span></label>
            <input type="text" name="number" id="number" class="form-input" value="<?php echo $number;?>">
            <p id="mobileError" class="error"></p>
            
            <label for="password">Password:<span class="star">*</span></label>
            <input type="password" name="password" id="password" class="form-input" value="<?php echo $password; ?>">
            <p id="passwordError" class="error"></p>


            <label for="password">Confirm Password:<span class="star">*</span></label>
            <input type="password" name="password_2" id="password_2" class="form-input" value="<?php echo $password_2; ?>">
            <p id="passwordError2" class="error"></p>

            <div class="buttons-container">
                <a href="display.php" id="back-btn">Back</a>
                <button type="submit" class="submit-button">Submit</button>
            </div>
            
        </form>
    </div>

    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            var okButton = document.getElementById('ok-text');
            if (okButton) { 
                okButton.addEventListener('click', function() {
                    window.location.href = 'list-users.php'; 
                });
            } else {
                console.error('Button with ID "ok-text" not found.');
            }
        });

        $(document).ready(function() {
    var timeoutIdEmail;
    var timeoutIdNumber,timeoutIdPassword,timeoutIdPassword2;
    var emailErrorPersisted = false; 

    $('#name').on('input', function() {
        validateField();
    });

    $('#email').on('input', function() {
        clearTimeout(timeoutIdEmail); // Clear previous timeout
        var emailInput = $(this).val().trim();
        var emailError = $('#emailError');
        timeoutIdEmail = setTimeout(function() {
            validateEmail(emailInput, emailError);
            if (emailError.text() === '') {
                // Email is valid, check if it exists
                checkEmailExists(emailInput);
            }
        }, 1000); // Set new timeout
        // Hide error message immediately when user starts typing again
        emailError.text('');
    });

    $('#number').on('input', function() {
        clearTimeout(timeoutIdNumber);
        timeoutIdNumber =setTimeout(()=>{
            validateNumber();
        },1000)
        $('#mobileError').text('');
        
    });

    $('#password').on('input', function() {
        clearTimeout(timeoutIdPassword);
        timeoutIdPassword =setTimeout(()=>{
            validatePassword();
        },1000)
        $('#passwordError').text('');
        
    });

    $('#password').on('input', function() {
        validatePassword2($(this), '#password_error_2');
    });

    $('#password_2,#password').on('input', function() {
        clearTimeout(timeoutIdPassword2);
        timeoutIdPassword2 =setTimeout(()=>{
            validatePassword_match($(this), '#password_error_match');
        },1000)
        $('#passwordError2').text('');
        
    });

    $('#registrationForm').submit(function(e) {
    e.preventDefault(); // Prevent form submission
    
    // Reset error messages
    $('#emailError').text('');
    $('#nameError').text('');
    $('#mobileError').text('');
    $('#passwordError').text('');
    $('#passwordError2').text('');
    
    // Get input values
    var email = $('#email').val().trim();
    var name = $('#name').val().trim();
    var number = $('#number').val().trim();
    var password = $('#password').val().trim();
    var password2 = $('#password_2').val().trim();

    // Check for empty fields
    var hasErrors = false;

    if (email === '') {
        $('#emailError').text('Please enter your email.');
        hasErrors = true;
    }
    if (name === '') {
        $('#nameError').text('Please enter your name.');
        hasErrors = true;
    }
    if (number === '') {
        $('#mobileError').text('Please enter your number.');
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

    // Check if passwords match
    if (password !== password2) {
        $('#passwordError2').text('Passwords do not match.');
        hasErrors = true;
    }

    // If there are errors, stop form submission
    if (hasErrors) {
        return;
    }

    // If all validations pass, submit the form
    this.submit();
});


    // Function to validate name input
    function validateField() {
        var value = $('#name').val().trim();
        console.log(value,'ye');
        var containsSpecialChars = /[^a-zA-Z\s]/.test(value); // Regular expression to check for non-alphabetic characters

        if (value === '') {
            $("#nameError").text('Please enter your name.').show();
        } else if (containsSpecialChars) {
            $("#nameError").text('Name should not contain numbers or special characters.').show();
        } else {
            $("#nameError").hide();
        }
    }


    function validateEmail() {
        var value = $('#email').val().trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(value === ''){
            $('#emailError').text('Please Enter your email.');
        }
        else if (!emailRegex.test(value)) {
            $('#emailError').text(value === '' ? '' : 'Please enter a valid email.');
        } else {
            $('#emailError').text('');
        }
    }

    function validateNumber() {
        var number = $('#number').val().trim();
        if (number === '' || isNaN(number) || number.length !== 10 || number < 0) {
            $('#mobileError').text('Please enter a valid 10-digit number.').show();
        } else {
            $('#mobileError').hide();
        }
    }


    function validatePassword() {
        var password = $('#password').val().trim();
        var passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
        if(password===''){
            $('#passwordError').text('Please Enter Password.').show();
        }
        else if(!passwordRegex.test(password)) {
            $('#passwordError').text('8+ characters with at least 1 uppercase letter and 1 special character.').show();
        } else {
            $('#passwordError').hide();
        }
    }

    function validatePassword2(inputField, errorElement) {
        var value = inputField.val().trim();
        if(value === ""){
            $(errorElement).text('Please enter password.').show();
        }
        else {
            $(errorElement).hide();
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

    function validateEmptyEmail(inputField, errorElement){
        console.log("Validating empty email field...");
        var value = inputField.val().trim();
        if (value === '') {
            $(errorElement).text('Please enter your email.').show();
        } else {
            $(errorElement).hide();
        }
    }
});



    </script>

</body>
</html>