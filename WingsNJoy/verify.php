<?php
session_start();
include('config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $query = "SELECT user_id FROM email_verifications WHERE token = ? AND expires_at > NOW() LIMIT 1";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Failed to prepare SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Get user_id and update account status
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Update the user's email verification status
        $update_user_query = "UPDATE users SET account_status = 'Active' WHERE id = ?";
        $update_user_stmt = $conn->prepare($update_user_query);
        if ($update_user_stmt === false) {
            die("Failed to prepare SQL statement: " . $conn->error);
        }

        $update_user_stmt->bind_param("i", $user_id);
        if (!$update_user_stmt->execute()) {
            die("Failed to update account status: " . $update_user_stmt->error);
        }

        // Optionally delete the verification token after it has been used
        $delete_token_query = "DELETE FROM email_verifications WHERE token = ?";
        $delete_token_stmt = $conn->prepare($delete_token_query);
        if ($delete_token_stmt === false) {
            die("Failed to prepare SQL statement: " . $conn->error);
        }

        $delete_token_stmt->bind_param("s", $token);
        $delete_token_stmt->execute();

        echo "Your email has been verified successfully! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}

$conn->close();
?>