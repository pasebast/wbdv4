<?php
session_start();
include 'config.php';

// Fetch user profile data from the database using user email
function getUserDetails($conn, $email) {
    $stmt = $conn->prepare("SELECT First_Name, phone_number, delivery_address FROM users WHERE email = ?");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return null;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_name, $user_phone, $user_address);
    $stmt->fetch();
    $stmt->close();
    return array('user_name' => $user_name, 'user_phone' => $user_phone, 'user_address' => $user_address);
}

// Fetch user email from session
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;
if ($userEmail) {
    $userDetails = getUserDetails($conn, $userEmail);
    $user_name = $userDetails['user_name'];
    $user_phone = $userDetails['user_phone'];
    $user_address = $userDetails['user_address'];
} else {
    $user_name = $user_phone = $user_address = "N/A";
}

// Check if cart and subtotal are included in the POST data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cart']) && isset($_POST['subtotal'])) {
        $cartJson = stripslashes($_POST['cart']);
        $_SESSION['cart'] = json_decode($cartJson, true);
        $_SESSION['subtotal'] = $_POST['subtotal'];
        if ($_SESSION['cart'] === null) {
            echo "JSON Decode Error: Invalid JSON format";
        }
    } else {
        header('Location: menu.php');
        exit();
    }
}

// Retrieve cart and subtotal from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;
$delivery_fee = 10;
$total = $subtotal + $delivery_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Receipt</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .receipt-container {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        h1 {
            font-size: 24px;
        }

        h2 {
            font-size: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        li:last-child {
            border-bottom: none;
        }

        p {
            margin-bottom: 10px;
        }

        strong {
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="button"] {
            background-color: #ccc;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #3e8e41;
        }

        button[type="button"]:hover {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h1>Checkout Receipt</h1>
        <h2>Recipient Details</h2>
        <p>Name: <?php echo htmlspecialchars($user_name); ?></p>
        <p>Phone: <?php echo htmlspecialchars($user_phone); ?></p>
        <p>Address: <?php echo htmlspecialchars($user_address); ?></p>
        <h2>Order Summary</h2>
        <ul>
            <?php if (!empty($cart)): ?>
                <?php foreach ($cart as $item): ?>
                    <li><?php echo htmlspecialchars($item['name']) . ' - ₱' . htmlspecialchars($item['price']) . ' (Qty: ' . htmlspecialchars($item['quantity']) . ')'; ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No items in your cart.</li>
            <?php endif; ?>
        </ul>
        <p>Subtotal: ₱<?php echo htmlspecialchars($subtotal); ?></p>
        <p>Delivery Fee: ₱<?php echo htmlspecialchars($delivery_fee); ?></p>
        <p><strong>Total: ₱<?php echo htmlspecialchars($total); ?></strong></p>
        <h2>Payment Method</h2>
        <form action="process_checkout.php" method="post">
            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="gcash">GCash</option>
                <option value="maya">Maya</option>
                <option value="paypal">PayPal</option>
                <option value="credit_card">Credit Card</option>
                <option value="debit_card">Debit Card</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
            </select>
            <button type="submit">Place Order</button>
            <button type="button" onclick="window.history.back();">Back</button>
        </form>
    </div>
</body>
</html>
