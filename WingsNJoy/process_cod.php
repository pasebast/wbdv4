<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_SESSION['order_id'];
    
    $stmt = $conn->prepare("UPDATE orders SET payment_method = 'COD', status = 'Pending' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo "Your Cash on Delivery order has been placed successfully!";
    } else {
        echo "Error processing your order. Please try again.";
    }
}
?>
