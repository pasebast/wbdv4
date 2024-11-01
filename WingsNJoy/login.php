<?php
session_start();
include 'config.php'; // Database connection

$error = ''; // Initialize error variable
$successMessage = ''; // Initialize success message variable
$showResendLink = false; // Flag to control link visibility

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend_activation'])) {
        $email = $_POST['email']; // Capture email for resend activation link
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND account_status = 'Pending'");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userId);
                $stmt->fetch();
                // TODO: Send activation email using PHPMailer
                $successMessage = "Activation link has been resent to your email.";
            } else {
                $error = "Email does not exist or is already verified.";
            }
        } else {
            $error = "Error executing the query.";
        }
        $stmt->close();
    } else {
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $stmt = $conn->prepare("SELECT id, email, account_status FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        if ($stmt->execute()) {
            $stmt->bind_result($userId, $fetchedEmail, $accountStatus);
            if ($stmt->fetch()) {
                if ($accountStatus == 'Active') {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['email'] = $fetchedEmail; // Ensure email is correctly set
                    header("Location: index.php");
                    exit();
                } elseif ($accountStatus == 'Pending') {
                    $error = "You need to verify your email before logging in.";
                    $showResendLink = true;
                } else {
                    $error = "Account status is not valid.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Error executing the query.";
        }
        $stmt->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/LOGO1.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Linking FontAwesome -->
</head>
<body>
    <!-- Login Navbar -->
    <nav class="login-nav">
        <a href="index.php"><img src="img/logo nav.png" alt="Website Logo"></a>
        <div class="nav-links">
            <ul>
                <li><a href="index.php"> HOME </a></li>
                <li><a href="menu.php"> MENU </a></li>
                <li><a href="register.php"> REGISTER </a></li>
                <li><a href="account.php"> MY ACCOUNT </a></li>
                
            </ul>
        </div>
    </nav>
    <!-- Login Form -->
    <div class="form-container">
        <div class="form-header">
            <h2>Login</h2>
        </div>
        <form action="login.php" method="POST">
            <div class="input-container">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" required>
                <i class="input-icon">✉️</i>
            </div>
            <div class="input-container">
                <label for="login-password">Password:</label>
                <div class="password-container">
                    <input type="password" id="login-password" name="password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('login-password')">
                        <i class="eye-icon"></i>
                    </button>
                </div>
            </div>
            <div class="options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="custom-checkbox"></span>
                    Keep me logged in
                </label>
                <a href="forgot.php" class="forgot-password">Forgot Password?</a>
            </div>
            <button type="submit" class="submit-btn">Login</button>
            <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if ($successMessage) echo "<p style='color:green;'>$successMessage</p>"; ?>
            <?php if ($showResendLink): ?>
                <button type="submit" name="resend_activation" class="resend-btn">Resend Activation Link</button>
            <?php endif; ?>
            <p>Don't have an account? <a href="register.php" class="register-link">Register</a></p>
        </form>
    </div>


    <!-- Styles for Login Navbar and Form -->
    <style>
       /* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
}

html, body {
    height: 100%;
}

/* Header Section */
.header {
	width: 100%;
    position: fixed;
    top: 0;
    z-index: 1000;
}

nav {
    display: flex;
    padding: 1% 6%;
    justify-content: space-between;
    align-items: center;
    background-color: #2C2C54; /* Added a background color */
    position: fixed; /* Keep the nav at the top */
    top: 0;
    width: 100%; /* Span full width */
    z-index: 1000; /* Ensure it stays on top of other elements */
}

nav img {
    width: 200px;
    height: auto; /* Ensures the logo scales properly */
}

.nav-links ul {
    display: flex;
    justify-content: space-between;
}

.nav-links ul li {
    list-style: none;
    display: inline-block;
    padding: 8px 15px; /* Adjust spacing between nav items */
    position: relative;
}

.nav-links ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px; /* Adjusted font size */
    transition: color 0.3s ease;
}

.nav-links ul li a:hover {
    color: #fefefe; /* Optional: Change color on hover for better UX */
}

.nav-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #fafafa;
    display: block;
    margin: auto;
    transition: width 0.5s;
}

.nav-links ul li:hover::after {
    width: 100%;
}

/* Login Navbar Styles */
.login-nav {
    display: flex;
    padding: 1% 6%;
    justify-content: space-between;
    align-items: center;
    background-color: #2C2C54; /* Adjusted background color */
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
}

.login-nav .logo {
    font-size: 24px;
    color: #ffffff;
    font-weight: bold;
}

.login-nav .nav-links ul {
    display: flex;
    justify-content: space-between;
}

.login-nav .nav-links ul li {
    list-style: none;
    padding: 8px 15px;
    position: relative;
}

.login-nav .nav-links ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.3s ease;
}

.login-nav .nav-links ul li a:hover {
    color: #fefefe;
}

/* Form and Body Styling */
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #fde799;
    padding-top: 80px; /* Offset for fixed navbar */
}

.form-container {
    width: 100%;
    max-width: 400px;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    background-color: #f7f7f7;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

h2 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

input, button.submit-btn {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button.submit-btn {
    background-color: #e74c3c;
    color: #ffffff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button.submit-btn:hover {
    background-color: #c0392b; /* Darker red on hover */
}

/* Additional styling */
.input-container {
    margin-bottom: 15px;
}

.input-container label {
    margin-bottom: 5px;
    display: block;
}

.options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.forgot-password {
    color: #e74c3c;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Remember Me Custom Styles */
.remember-me {
    font-size: 16px; /* Change font size */
    color: #333; /* Change text color */
}

.remember-me input {
    display: none; /* Hide default checkbox */
}

.custom-checkbox {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 1px solid #ccc;
    border-radius: 3px;
    margin-right: 5px;
    vertical-align: middle;
    background-color: #fff;
}

input[type="checkbox"]:checked + .custom-checkbox {
    background-color: #e74c3c; /* Checked color */
    border-color: #e74c3c;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .form-container {
        width: 90%; /* Make the form responsive */
    }

    .login-nav .nav-links ul li {
        padding: 8px 10px; /* Adjust padding for mobile */
    }

    h2 {
        font-size: 20px; /* Responsive header size */
    }
}

    </style>

    <script>
        function togglePassword(inputId) {
            var input = document.getElementById(inputId);
            input.type = (input.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>