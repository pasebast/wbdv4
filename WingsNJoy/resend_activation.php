<?php
// Start the session
session_start();
include('config.php'); // Include your database connection file

// Check if the email is set in the query string
if (isset($_GET['email'])) {
    $email = $_GET['email']; // Capture email for the resend activation link

    // Generate a new verification token
    $verification_token = md5(uniqid(rand(), true)); // Generate a unique token
    $expires = date("Y-m-d H:i:s", strtotime('+1 day')); // Set expiration date

    // Get the user ID based on the email
    $query = "SELECT id FROM users WHERE email = ?"; // Query for email
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Insert the new token into the email_verifications table
        $insert_verification = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_verification);
        $insert_stmt->bind_param("iss", $user_id, $verification_token, $expires);
        
        if ($insert_stmt->execute()) {
            // Prepare the verification email
            $verification_link = "http://localhost/WingsNJoy/verify.php?token=" . $verification_token;

            // Include PHPMailer classes
            require 'src/PHPMailerAutoload.php'; // Ensure this path is correct
            $mail = new PHPMailer();

            // Set up PHPMailer
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'wingsnjoydelivery@gmail.com'; // Your email
            $mail->Password   = 'pbswtjgicuntzcgr'; // Your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Set email format to HTML
            $mail->isHTML(true);
            $mail->setFrom('wingsnjoydelivery@gmail.com', 'WingsNJoy Admin');
            $mail->addAddress($email); // Using the email for sending
            $mail->Subject = 'Email Verification';
            $mail->Body = "Please verify your email by clicking the following link: <a href='$verification_link'>Verify Email</a>";

            // Send the email
            if ($mail->send()) {
                echo "<script>alert('Activation link has been resent. Please check your email.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Failed to resend activation link. Please try again.'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Failed to insert verification token.'); window.location.href='login.php';</script>";
        }
        $insert_stmt->close();
    } else {
        echo "<script>alert('No user found with that email.'); window.location.href='login.php';</script>";
    }
} else {
    // If the email is not set in the query string
    echo "<script>alert('No email provided.'); window.location.href='login.php';</script>";
}
?>