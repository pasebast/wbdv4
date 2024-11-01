<?php
$host = 'localhost';
$db = 'WingsNJoy'; // Ensure this matches your actual database name
$user = 'root'; // Default username for WAMP
$pass = ''; // Leave blank if you haven't set a password for root

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
