<?php
session_start();
include 'config.php';

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = isset($input['orderID']) ? $input['orderID'] : $_SESSION['order_id'];

$payment_verified = true; // Example: Replace with actual payment verification

if ($payment_verified) {
    $stmt = $conn->prepare("UPDATE orders SET payment_method = 'PayPal', status = 'Paid' WHERE id = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo json_encode(array('status' => 'success', 'message' => 'Payment processed successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error updating order status'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Payment verification failed'));
}
?>
