<?php
session_start();

// Initialize variables
$recipient_name = '';
$recipient_phone = '';
$delivery_address = '';
$payment_method = '';
$subtotal = 0;
$delivery_fee = 0;
$total = 0;

// Check if data is set in session storage
if (isset($_SESSION['recipient_name'])) {
    $recipient_name = $_SESSION['recipient_name'];
    $recipient_phone = $_SESSION['recipient_phone'];
    $delivery_address = $_SESSION['delivery_address'];
    $payment_method = $_SESSION['payment_method'];
    $subtotal = $_SESSION['subtotal'];
    $delivery_fee = $_SESSION['delivery_fee'];
    $total = $_SESSION['total'];
} else {
    // Redirect back to cart or an error page if no data is available
    header('Location: cart.php');
    exit();
}

// Process the order when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $recipient_name = $_POST['recipient_name'];
    $recipient_phone = $_POST['recipient_phone'];
    $delivery_address = $_POST['delivery_address'];
    $payment_method = $_POST['payment_method'];
    $subtotal = $_POST['subtotal'];
    $delivery_fee = $_POST['delivery_fee'];
    $total = $_POST['total'];

    // Save order logic goes here (e.g., save to database)
    /*
    $stmt = $pdo->prepare("INSERT INTO orders (recipient_name, recipient_phone, delivery_address, payment_method, subtotal, delivery_fee, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$recipient_name, $recipient_phone, $delivery_address, $payment_method, $subtotal, $delivery_fee, $total]);
    */
    
    // Clear session data after processing the order
    session_destroy(); // Clear the session or unset specific variables

    // Redirect or display a success message
    echo "<h2>Order processed successfully!</h2>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
</head>
<body>

    <div class="checkout-container">
        <h1>Checkout Confirmation</h1>

        <form action="checkout.php" method="post">
            <!-- Recipient Details -->
            <div class="recipient-details">
                <h2>Recipient Details</h2>
                <p>Name: <?php echo htmlspecialchars($recipient_name); ?></p>
                <input type="hidden" name="recipient_name" value="<?php echo htmlspecialchars($recipient_name); ?>">

                <p>Phone: <?php echo htmlspecialchars($recipient_phone); ?></p>
                <input type="hidden" name="recipient_phone" value="<?php echo htmlspecialchars($recipient_phone); ?>">
            </div>

            <!-- Delivery Address -->
            <div class="delivery-address">
                <h2>Delivery Address</h2>
                <p><?php echo htmlspecialchars($delivery_address); ?></p>
                <input type="hidden" name="delivery_address" value="<?php echo htmlspecialchars($delivery_address); ?>">
            </div>

            <!-- Payment Method -->
            <div class="payment-method">
                <h2>Payment Method</h2>
                <p><?php echo htmlspecialchars($payment_method); ?></p>
                <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($payment_method); ?>">
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                <p>Subtotal: ₱<span id="subtotal"><?php echo htmlspecialchars($subtotal); ?></span></p>
                <p>Delivery Fee: ₱<span id="delivery_fee"><?php echo htmlspecialchars($delivery_fee); ?></span></p>
                <p><strong>Total: ₱<span id="total"><?php echo htmlspecialchars($total); ?></span></strong></p>
                <input type="hidden" name="subtotal" value="<?php echo htmlspecialchars($subtotal); ?>">
                <input type="hidden" name="delivery_fee" value="<?php echo htmlspecialchars($delivery_fee); ?>">
                <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>">
            </div>

            <button type="submit">Confirm Order</button>
        </form>
    </div>

    <script src="script.js"></script> <!-- Link to your JavaScript -->
</body>
</html>
