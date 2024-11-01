<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch user email from session
$userEmail = $_SESSION['email'];

// Fetch order history for the logged-in user by joining orders with users based on email
$stmt_orders = $conn->prepare("SELECT o.order_number, o.recipient_name, o.recipient_phone, o.delivery_address, o.payment_method, o.subtotal, o.delivery_fee, o.total, o.created_at, o.status 
FROM orders o
JOIN users u ON o.recipient_name = CONCAT(u.First_Name, ' ', u.Last_Name) WHERE u.email = ? ORDER BY o.created_at DESC");
if (!$stmt_orders) {
    die("Prepare failed: " . $conn->error); // Add error handling here
}
$stmt_orders->bind_param("s", $userEmail);
$stmt_orders->execute();
$stmt_orders->bind_result($order_number, $recipient_name, $recipient_phone, $delivery_address, $payment_method, $subtotal, $delivery_fee, $total, $created_at, $status);
$orders = array();
while ($stmt_orders->fetch()) {
    $orders[] = array(
        'order_number' => $order_number,
        'recipient_name' => $recipient_name,
        'recipient_phone' => $recipient_phone,
        'delivery_address' => $delivery_address,
        'payment_method' => $payment_method,
        'subtotal' => $subtotal,
        'delivery_fee' => $delivery_fee,
        'total' => $total,
        'created_at' => $created_at,
        'status' => $status
    );
}
$stmt_orders->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .order-history {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .order-history h2 {
            font-size: 32px;
            color: #4a3c31;
            text-align: center;
            margin-bottom: 20px;
        }

        .order {
            padding: 15px;
            border-bottom: 1px solid #ccc;
        }

        .order p {
            margin: 5px 0;
        }

        .order-number {
            font-weight: bold;
            color: #e74c3c;
        }
		.back-button {
            text-align: center;
            margin-bottom: 20px;
        }

        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .back-button a:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }
		.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

    </style>
</head>
<body>
<div class="back-button">
        <a href="account.php">Back to My Account</a>
    </div>
    <div class="order-history">
        <h2>Your Order History</h2>
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <p class="order-number">
					<a href="javascript:void(0);" onclick="showOrderDetails('<?php echo $order['order_number']; ?>')">
						Order Number: <?php echo htmlspecialchars($order['order_number']); ?>
					</a>
					</p>
                    <p>Recipient: <?php echo htmlspecialchars($order['recipient_name']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($order['recipient_phone']); ?></p>
                    <p>Address: <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p>Payment Method: <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p>Subtotal: ₱<?php echo htmlspecialchars(number_format($order['subtotal'], 2)); ?></p>
                    <p>Delivery Fee: ₱<?php echo htmlspecialchars(number_format($order['delivery_fee'], 2)); ?></p>
                    <p>Total: ₱<?php echo htmlspecialchars(number_format($order['total'], 2)); ?></p>
                    <p>Order Date: <?php echo htmlspecialchars($order['created_at']); ?></p>
                    <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </div>


<!-- Modal Structure -->
<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Order Details</h2>
        <div id="orderDetailsContent">
            <!-- Order details will be populated here -->
        </div>
    </div>
</div>


<script>
function showOrderDetails(orderNumber) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_order_details.php?order_number=' + encodeURIComponent(orderNumber), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var orderDetails = JSON.parse(xhr.responseText);
            var items = orderDetails.items.map(function(item) {
                return '<p>Item: ' + item.product_name + ' (Quantity: ' + item.quantity + ', Price: ₱' + parseFloat(item.price).toFixed(2) + ')</p>';
            }).join('');
            var orderDetailsContent = '<p>Order Number: ' + orderDetails.order_number + '</p>' +
                '<p>Recipient: ' + orderDetails.recipient_name + '</p>' +
                '<p>Phone: ' + orderDetails.recipient_phone + '</p>' +
                '<p>Address: ' + orderDetails.delivery_address + '</p>' +
                '<p>Payment Method: ' + orderDetails.payment_method + '</p>' +
                '<p>Subtotal: ₱' + parseFloat(orderDetails.subtotal).toFixed(2) + '</p>' +
                '<p>Delivery Fee: ₱' + parseFloat(orderDetails.delivery_fee).toFixed(2) + '</p>' +
                '<p>Total: ₱' + parseFloat(orderDetails.total).toFixed(2) + '</p>' +
                '<p>Order Date: ' + orderDetails.created_at + '</p>' +
                '<p>Status: ' + orderDetails.status + '</p>' +
                items;
            document.getElementById('orderDetailsContent').innerHTML = orderDetailsContent;
            document.getElementById('orderDetailsModal').style.display = 'block';
        }
    };
    xhr.send();
}


function closeModal() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

</script>	


</body>
</html>
