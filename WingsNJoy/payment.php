<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// PayPal sandbox configuration
$paypal_client_id = 'YOUR_PAYPAL_SANDBOX_CLIENT_ID';
$paypal_secret = 'YOUR_PAYPAL_SANDBOX_SECRET';

// Get order details from session or database
$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;
$order_total = isset($_SESSION['order_total']) ? $_SESSION['order_total'] : 0;

if (!$order_id || !$order_total) {
    die("Error: Order information is missing.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - WingsNJoy</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_client_id; ?>&currency=PHP"></script>
</head>
<body>
    <h1>Choose Payment Method</h1>
    
    <h2>PayPal / Credit Card / Debit Card</h2>
    <div id="paypal-button-container"></div>

    <h2>GCash / Maya</h2>
    <p>For GCash and Maya, please use the following QR code:</p>
    <img src="path_to_your_qr_code_image.png" alt="GCash/Maya QR Code">

    <h2>Cash on Delivery</h2>
    <form action="process_cod.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="submit" value="Place Cash on Delivery Order">
    </form>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $order_total; ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    // Call your server to save the transaction
                    return fetch('/process_payment.php', {
                        method: 'post',
                        headers: {
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            orderID: data.orderID
                        })
                    });
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>