<?php
session_start();
include 'config.php';
date_default_timezone_set('Asia/Manila'); // Set to your local time zone
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : null;

// Ensure the order ID is set
if (!$order_id) {
    die("Order ID is missing.");
}

// Retrieve order details including the random order number
$stmt = $conn->prepare("SELECT order_number FROM orders WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($order_number);
$stmt->fetch();
$stmt->close();
// Simulate payment verification
$payment_verified = true; // Example: Replace with actual payment verification

if ($payment_verified) {
    $stmt = $conn->prepare("UPDATE orders SET payment_method = ?, status = 'Paid' WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("si", $payment_method, $order_id);
if ($stmt->execute()) {
    $message = 'Payment processed successfully';
} else {
    $message = 'Error updating order status: ' . $stmt->error; // Add error message for debugging
}

} else {
    $message = 'Payment verification failed';
}

// Log the values being passed
echo "Updating order ID: $order_id with payment method: $payment_method<br>";


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assuming you have a styles.css file -->
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
            position: relative;
            margin-bottom: 20px;
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

        /* Container for payment confirmation */
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 150px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        h1 {
            font-size: 32px;
            color: #4a3c31;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #4a3c31;
            margin-bottom: 10px;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <header class="header">
        <nav>
            <img src="img/logo nav.png" alt="WingsNJoy Logo">
            <div class="nav-links">
                <ul>
                    <li><a href="index.php"> HOME </a></li>
                    <li><a href="menu.php"> MENU </a></li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li><a href="logout.php"> LOGOUT </a></li>
                        <?php else: ?>
                            <li><a href="register.php">REGISTER </a></li>
                            <li><a href="login.php"> LOGIN </a></li>
                        <?php endif; ?>
                    <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="container">
        <h1>Payment Confirmation</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if ($payment_verified): ?>
            <p class="order-number">Order Number: <?php echo htmlspecialchars($order_number); ?></p>
        <?php endif; ?>
        <a href="index.php" class="button">Return to Home</a>
    </div>
</body>
</html>
