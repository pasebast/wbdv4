<?php
session_start(); // Start the session

// Assuming you have validated the user's credentials
if ($validCredentials) { // Replace with your validation logic
    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $userEmail; // Set the user's email

    // Redirect to the homepage or dashboard
    header("Location: index.php");
    exit;
} else {
    // Handle login failure
    echo "Invalid credentials!";
}
?>
