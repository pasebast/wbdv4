<?php
session_start();
include 'config.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array(); // Use old array syntax
}

// Example function to add an item to the cart
function addToCart($itemId, $quantity) {
    if (isset($_SESSION['cart'][$itemId])) {
        $_SESSION['cart'][$itemId] += $quantity; // Increment quantity if item already in cart
    } else {
        $_SESSION['cart'][$itemId] = $quantity; // Add new item to cart
    }
}

// Example to simulate adding an item (you can replace this with actual item logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    addToCart($itemId, $quantity);
}

// Retrieve cart items for display
$cartItems = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Your Cart</title>
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
            </ul>
        </nav>
    </header>

    <main>
        <h1>Your Shopping Cart</h1>
        <table>
            <tr>
                <th>Item ID</th>
                <th>Quantity</th>
            </tr>
            <?php foreach ($cartItems as $itemId => $quantity): ?>
                <tr>
                    <td><?php echo htmlspecialchars($itemId); ?></td>
                    <td><?php echo htmlspecialchars($quantity); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <form method="POST" action="cart.php">
            <input type="text" name="item_id" placeholder="Item ID" required>
            <input type="number" name="quantity" placeholder="Quantity" required min="1">
            <button type="submit">Add to Cart</button>
        </form>

        <a href="checkout.php">Proceed to Checkout</a>
    </main>

    <footer>
        <p>&copy; 2024 Delivery Service</p>
    </footer>
</body>
</html>
