<?php
session_start();
include 'config.php'; // Include your database connection
date_default_timezone_set('Asia/Manila'); // Set to your local time zone
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user details from session
    $userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

    // Ensure user details are available
    if (!$userEmail) {
        echo "User not logged in.";
        exit();
    }

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT First_Name, Last_Name, phone_number, delivery_address FROM users WHERE email = ?");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($user_first_name, $user_last_name, $user_phone, $user_address);
    $stmt->fetch();
    $stmt->close();

    // Combine first and last name
    $user_name = $user_first_name . ' ' . $user_last_name;

    // Generate a unique order number
    $order_number = uniqid('ORD-');

    // Retrieve cart and subtotal from session
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;
    $delivery_fee = 10;
    $total = $subtotal + $delivery_fee;

    // Prepare to insert order into the database
	$payment_method = $_POST['payment_method'];

	// Debugging
	if (empty($payment_method)) {
		die("Payment method is not set!");
	}

	// Continue with the existing code...
error_log("Cart contents: " . print_r($cart, true));

	$stmt = $conn->prepare("INSERT INTO orders (order_number, recipient_name, recipient_phone, delivery_address, payment_method, subtotal, delivery_fee, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    if ($stmt) {
        $stmt->bind_param("ssssssdd", $order_number, $user_name, $user_phone, $user_address, $payment_method, $subtotal, $delivery_fee, $total);
        if ($stmt->execute()) {
        // Get the newly created order ID
        $orderId = $stmt->insert_id;
        
        // Insert order items
        $cart = $_SESSION['cart'];
        foreach ($cart as $item) {
			$product_name = $item['name'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            if ($stmt_items) {
                $stmt_items->bind_param("isid", $orderId, $product_name, $quantity, $price);
                $stmt_items->execute();
                $stmt_items->close();
            } else {
                echo "Error preparing statement for order items: " . $conn->error;

            }
			
        }

        // Redirect based on payment method
        header("Location: process_payment.php?order_id=$orderId&payment_method=$payment_method");
        exit();
    } else {
            echo "Error placing order: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
