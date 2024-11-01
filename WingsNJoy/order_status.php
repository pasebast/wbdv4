<?php
session_start();
include 'config.php'; // Ensure this points to your database configuration file

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your orders.");
}

$userId = $_SESSION['user_id'];

// Prepare a statement to fetch the user's orders
$stmt = $conn->prepare("SELECT item_id, quantity, payment_method, order_status, created_at FROM transactions WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

// Bind result variables
$stmt->bind_result($itemId, $quantity, $paymentMethod, $orderStatus, $createdAt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Your Orders</title>
</head>
<body>
    <header>
     
		  <div class="logo">Delivery Service</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="order_status.php">View Your Orders</a></li> <!-- Added this line -->
            </ul>
        </nav>
    </header>

    <main>
        <h1>Your Orders</h1>
        <table>
            <tr>
                <th>Item ID</th>
                <th>Quantity</th>
                <th>Payment Method</th>
                <th>Order Status</th>
                <th>Order Date</th>
            </tr>
            <?php
            // Fetch the results
            while ($stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($itemId); ?></td>
                    <td><?php echo htmlspecialchars($quantity); ?></td>
                    <td><?php echo htmlspecialchars($paymentMethod); ?></td>
                    <td><?php echo htmlspecialchars($orderStatus); ?></td>
                    <td><?php echo htmlspecialchars($createdAt); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <?php if ($stmt->num_rows == 0): ?>
            <p>You have no orders.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Delivery Service</p>
    </footer>
</body>
</html>

<?php
// Close the statement
$stmt->close();
$conn->close();
?>
