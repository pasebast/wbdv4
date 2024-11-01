<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];

    switch ($payment_method) {
        case 'gcash':
        case 'maya':
        case 'paypal':
        case 'credit_card':
        case 'debit_card':
            header("Location: process_payment.php");
            break;
        case 'cash_on_delivery':
            header("Location: process_cod.php");
            break;
        default:
            echo "Invalid payment method.";
            exit;
    }
    exit;
}
?>
