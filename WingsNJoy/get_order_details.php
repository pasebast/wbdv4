<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(array('error' => 'User not logged in.'));
    exit();
}

$order_number = isset($_GET['order_number']) ? $_GET['order_number'] : '';

if (!$order_number) {
    echo json_encode(array('error' => 'Order number is missing.'));
    exit();
}

// Fetch order details along with items
$stmt = $conn->prepare("
    SELECT o.order_number, o.recipient_name, o.recipient_phone, o.delivery_address, o.payment_method,
           o.subtotal, o.delivery_fee, o.total, o.created_at, o.status,
           oi.product_name, oi.quantity, oi.price
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.order_number = ?
");

$stmt->bind_param("s", $order_number);
$stmt->execute();
// Bind the results
$stmt->bind_result($order_number, $recipient_name, $recipient_phone, $delivery_address, $payment_method,
                   $subtotal, $delivery_fee, $total, $created_at, $status, 
                   $product_name, $quantity, $price);
$order_details = array(
    'order_number' => '',
    'recipient_name' => '',
    'recipient_phone' => '',
    'delivery_address' => '',
    'payment_method' => '',
    'subtotal' => 0,
    'delivery_fee' => 0,
    'total' => 0,
    'created_at' => '',
    'status' => '',
    'items' => array()
);

// Fetch data
while ($stmt->fetch()) {
    // Set order details only once
    if ($order_details['order_number'] == '') {
        $order_details['order_number'] = $order_number;
        $order_details['recipient_name'] = $recipient_name;
        $order_details['recipient_phone'] = $recipient_phone;
        $order_details['delivery_address'] = $delivery_address;
        $order_details['payment_method'] = $payment_method;
        $order_details['subtotal'] = $subtotal;
        $order_details['delivery_fee'] = $delivery_fee;
        $order_details['total'] = $total;
        $order_details['created_at'] = $created_at;
        $order_details['status'] = $status;
    }
    
    // Add items
    $order_details['items'][] = array(
        'product_name' => $product_name,
        'quantity' => $quantity,
        'price' => $price
    );
}

$stmt->close();
$conn->close();

echo json_encode($order_details);
?>
