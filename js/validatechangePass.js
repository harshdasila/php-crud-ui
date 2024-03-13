function validateForm() {
    var opassword = document.getElementById("opassword").value;
    var npassword = document.getElementById("npassword").value;
    var cpassword = document.getElementById("cpassword").value;
console.log("on js")
    // Reset error messages
    document.getElementById("opassmsg").innerHTML = "";
    document.getElementById("npassmsg").innerHTML = "";
    document.getElementById("cpassmsg").innerHTML = "";

    // Password validation rules
    var passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
    // Check if old password is empty
    if (opassword.trim() === "") {
        document.getElementById("opassmsg").innerHTML = "Please enter your old password.";
        return false;
    }

    // Check if new password meets validation rules
    if (!passwordRegex.test(npassword)) {
        document.getElementById("npassmsg").innerHTML = "Password must contain at least 8 characters, including one uppercase letter, one lowercase letter, one digit, and one special character.";
        return false;
    }

    // Check if confirm password matches new password
    if (npassword !== cpassword) {
        document.getElementById("cpassmsg").innerHTML = "Passwords do not match.";
        return false;
    }else{
        document.getElementById("cpassmsg").innerHTML = "";
    }
    
    // If all validations pass, return true to submit the form
    return true;
}