<?php
session_start();
include 'config.php';
require 'src/PHPMailerAutoload.php';

// Initialize error variable
$error = '';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['terms'])) {
        $error = "You must agree to the Terms and Conditions.";
    } else {
        // Collecting data from the form
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $phoneNumber = $_POST['phone_number'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $deliveryAddress = $_POST['delivery_address'];
        $city = $_POST['city'];
        $zipCode = $_POST['zip_code'];
        $country = $_POST['country'];

        // Check if email already exists
        $stmt_email_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt_email_check->bind_param("s", $email);
        $stmt_email_check->execute();
        $stmt_email_check->bind_result($email_count);
        $stmt_email_check->fetch();
        $stmt_email_check->close();

        if ($email_count > 0) {
            $error = "This email has already been used.";
			$_SESSION['error_message'] = $error; // Store error in session
            echo "<script>showModal('$error', false);</script>";
        } else {
            $verification_token = md5(uniqid(rand(), true));
            $verification_code = md5(uniqid(rand(), true));

            // Prepare SQL query for inserting into users table
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, phone_number, email, password, delivery_address, city, zip_code, country, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Failed to prepare the SQL statement: " . $conn->error);
            }
            
            // Bind parameters for the users table
            $stmt->bind_param("ssssssssss", $firstName, $lastName, $phoneNumber, $email, $password, $deliveryAddress, $city, $zipCode, $country, $verification_code);

            // Execute the statement and check for errors
            if ($stmt->execute()) {
                // Get the ID of the newly registered user
                $user_id = $conn->insert_id;

                // Prepare SQL query for inserting into the email_verifications table
                $stmt_verification = $conn->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)");
                if ($stmt_verification === false) {
                    die("Failed to prepare the email verification SQL statement: " . $conn->error);
                }
                
                // Set expiration date for token
                $expires = date("Y-m-d H:i:s", strtotime('+1 day'));
                
                // Bind parameters for the email_verifications table
                $stmt_verification->bind_param("iss", $user_id, $verification_token, $expires);

                // Execute the email verification query
                if ($stmt_verification->execute()) {
                    // Send verification email
                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'wingsnjoydelivery@gmail.com'; // Your email
                    $mail->Password = 'pbswtjgicuntzcgr'; // Your app password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('wingsnjoydelivery@gmail.com', 'WingsNJoy Admin');
                    $mail->addAddress($email); // Add a recipient

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification';
                    $mail->Body = "Please verify your email by clicking on the link: http://localhost/WingsNJoy/verify.php?token=$verification_token";

                    // Send the email
                    if ($mail->send()) {
                        $_SESSION['success_message'] = 'Registration successful! Please check your email to verify your account.';
                        header('Location: index.php');
                        exit();
                    } else {
                        $error = "Failed to send verification email. Mailer Error: {$mail->ErrorInfo}";
                        $_SESSION['error_message'] = $error; // Store error in session
                        header('Location: index.php'); // Redirect to index.php
                        exit();
                    }
                } else {
                    $error = "Failed to insert email verification details.";
                    $_SESSION['error_message'] = $error; // Store error in session
                    header('Location: index.php'); // Redirect to index.php
                    exit();
                }
            } else {
                $error = "Failed to register. Please try again.";
                $_SESSION['error_message'] = $error; // Store error in session
                header('Location: index.php'); // Redirect to index.php
                exit();
            }
        }
    }
}
?>