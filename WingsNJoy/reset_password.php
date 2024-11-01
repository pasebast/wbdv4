<?php
session_start();
include 'config.php'; // Database connection

$error = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Password validation checks
    if (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else if (!preg_match('/[A-Z]/', $newPassword)) {
        $error = 'Password must include at least 1 uppercase letter.';
    } else if (!preg_match('/[a-z]/', $newPassword)) {
        $error = 'Password must include at least 1 lowercase letter.';
    } else if (!preg_match('/[0-9]/', $newPassword)) {
        $error = 'Password must include at least 1 number.';
    } else if ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match. Please try again.';
    }

    // If there are no errors, proceed to reset the password
    if (empty($error)) {
        $hashedPassword = md5($newPassword); // Hash the new password

        // Debugging: Ensure token is received correctly
        error_log("Received token: $token");

        // Check if the token is valid
        $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expiry > NOW()");
        $stmt->bind_param("s", $token);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userId);
                $stmt->fetch();

                // Debugging: Ensure user ID is fetched correctly
                error_log("Fetched user_id: $userId");

                // Update the user's password in the users table
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);
                if ($stmt->execute()) {
                    // Delete the token from the password_resets table
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();

                    $successMessage = "Your password has been reset successfully. Please proceed to <a href='login.php'>Login</a>.";
                } else {
                    $error = "Error updating password.";
                    error_log("Error updating password: " . $stmt->error);
                }
            } else {
                $error = "Invalid or expired token.";
                error_log("Invalid or expired token: $token");
            }
        } else {
            $error = "Error executing the query.";
            error_log("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reset Password</title>
    <script>
        function validatePassword() {
            const password = document.querySelector('.new-password-input').value;
            const confirmPassword = document.querySelector('.confirm-password-input').value;
            let errorMessage = '';

            if (password.length < 8) {
                errorMessage = 'Password must be at least 8 characters long.';
            } else if (!/[A-Z]/.test(password)) {
                errorMessage = 'Password must include at least 1 uppercase letter.';
            } else if (!/[a-z]/.test(password)) {
                errorMessage = 'Password must include at least 1 lowercase letter.';
            } else if (!/[0-9]/.test(password)) {
                errorMessage = 'Password must include at least 1 number.';
            } else if (password !== confirmPassword) {
                errorMessage = 'Passwords do not match. Please try again.';
            }

            if (errorMessage) {
                document.querySelector('.error-message').textContent = errorMessage;
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
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

    <main class="reset-password-container">
        <h1 class="reset-password-title">Reset Password</h1>
        <?php if (!empty($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php elseif (empty($successMessage) && empty($error)): ?>
            <form method="POST" action="reset_password.php" class="reset-password-form" onsubmit="return validatePassword();">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <input type="password" name="new_password" placeholder="Enter your new password" required class="new-password-input">
                <input type="password" name="confirm_password" placeholder="Confirm your new password" required class="confirm-password-input">
                <button type="submit" class="reset-button">Reset Password</button>
                <?php if ($error) echo "<p class='error-message'>$error</p>"; ?>
                <p class="error-message" style="color:red;"></p>
            </form>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Delivery Service</p>
    </footer>
</body>
</html>
