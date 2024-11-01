<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$transactionId = $_GET['id'];

// Prepare SQL statement to fetch transaction details
$stmt = $conn->prepare("SELECT t.id, t.total_amount, t.created_at, u.username FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $transactionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $transaction = $result->fetch_assoc();
} else {
    echo "Transaction not found.";
    exit();
}

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 100px);
            padding: 20px;
        }

        .receipt-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        .receipt-details p {
            margin: 10px 0;
            font-size: 16px;
        }

        .thank-you {
            font-weight: bold;
            margin: 20px 0;
        }

        .account-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .account-link:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #007BFF;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Delivery Service</div>
    </header>

    <main>
        <div class="receipt-container">
            <h1>Transaction Receipt</h1>
            <div class="receipt-details">
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction['id']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($transaction['username']); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo htmlspecialchars(number_format($transaction['total_amount'], 2)); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($transaction['created_at']); ?></p>
            </div>
            <p class="thank-you">Thank you for your purchase!</p>
            <a class="account-link" href="account.php">Go to My Account</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Delivery Service</p>
    </footer>
</body>
</html>