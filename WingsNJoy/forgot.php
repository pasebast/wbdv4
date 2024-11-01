<?php
session_start();
include 'config.php'; // Database connection

require 'src/PHPMailerAutoload.php'; // PHPMailer 5.2 uses this file to load

$error = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId);
            $stmt->fetch();

            // Generate a random token using md5 and uniqid
            $token = md5(uniqid(rand(), true));

            // Store the token in the database with an expiry date (e.g., 24 hours from now)
            $expiry = date("Y-m-d H:i:s", strtotime('+24 hours'));
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $token, $expiry);
            if ($stmt->execute()) {
                // Debugging: Ensure token is stored
                error_log("Token stored: $token for user_id: $userId");

                // Send an email with the reset link
                $resetLink = "http://localhost/WingsNJoy/reset_password.php?token=" . urlencode($token);
                error_log("Reset link: $resetLink"); // Debugging: Ensure reset link is correct

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'wingsnjoydelivery@gmail.com'; // Your SMTP username
                $mail->Password = 'pbswtjgicuntzcgr'; // Your SMTP password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587; // or 465

                $mail->setFrom('wingsnjoydelivery@gmail.com', 'WingsNJoy Admin');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                $mail->Body = "Click this link to reset your password: <a href='" . $resetLink . "'>Reset Password</a>";

                if ($mail->send()) {
                    $successMessage = "A password reset link has been sent to your email.";
                } else {
                    $error = "Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                $error = "Error storing the reset token.";
                error_log("Error storing the reset token: " . $stmt->error);
            }
        } else {
            $error = "No account found with that email.";
        }
    } else {
        $error = "Error executing the query.";
        error_log("Error executing query: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Forgot Password</title>
</head>
<body>
    <!-- Header Section with Navigation -->
    <section class="header-forgot">
        <nav>
            <a href="index.php"><img src="img/logo nav.png" alt="Website Logo"></a>
            <div class="nav-links">
                <ul>
                    <li><a href="menu.php">MENU</a></li>
                    <li><a href="register.php">REGISTER</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php" class="logout-link">LOGOUT</a></li>
                    <?php else: ?>
                        <li><a href="login.php">LOG IN</a></li>
                    <?php endif; ?>
                    <li><a href="account.php">MY ACCOUNT</a></li>
                    <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a></li>
                </ul>
            </div>
        </nav>
    </section>

    <!-- Forgot Password Form -->
    <div class="forgot-password-container">
        <h1>Forgot Password</h1>
        <form method="POST" action="forgot.php" class="forgot-password-form">
            <input type="email" name="email" placeholder="Enter your email" required class="email-input">
            <button type="submit" class="submit-button">Send Reset Link</button>
            <?php if ($error) echo "<p class='error-message'>$error</p>"; ?>
            <?php if ($successMessage) echo "<p class='success-message'>$successMessage</p>"; ?>
        </form>
    </div>

    <footer class="footer-forgot">
        <p>&copy; 2024 Delivery Service</p>
    </footer>
</body>
</html>