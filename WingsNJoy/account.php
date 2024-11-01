<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login.php if the user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['email'];

// Fetch user details
$user = array();
$query = "SELECT First_Name, Last_Name, phone_number, delivery_address, city, zip_code, country FROM users WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo "<p>Error preparing statement: " . $conn->error . "</p>";
    exit();
}

$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $phoneNumber, $deliveryAddress, $city, $zipCode, $country);
$result = $stmt->fetch(); // Fetch the result

$stmt->close(); // Close the statement

if (!$result) {
    echo "<p>User not found.</p>";
    exit();
}

// Store fetched data in $user array
$user = array(
    'First_Name' => $firstName,
    'Last_Name' => $lastName,
    'phone_number' => $phoneNumber,
    'delivery_address' => $deliveryAddress,
    'city' => $city,
    'zip_code' => $zipCode,
    'country' => $country,
);

// Update user details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phoneNumber = $_POST['phone_number'];
    $deliveryAddress = $_POST['delivery_address'];
    $city = $_POST['city'];
    $zipCode = $_POST['zip_code'];
    $country = $_POST['country'];

    // Update user details in the database
    $query = "UPDATE users SET First_Name = ?, Last_Name = ?, phone_number = ?, delivery_address = ?, city = ?, zip_code = ?, country = ? WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<p>Error preparing update statement: " . $conn->error . "</p>";
        exit();
    }

    $stmt->bind_param("ssssssss", $firstName, $lastName, $phoneNumber, $deliveryAddress, $city, $zipCode, $country, $userEmail);

    if ($stmt->execute()) {
        echo "<p>Account details updated successfully.</p>";

        // Refresh user data
        $stmt->close(); // Close the statement before preparing a new one

        // Re-fetch user details
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->bind_result($firstName, $lastName, $phoneNumber, $deliveryAddress, $city, $zipCode, $country);
        $stmt->fetch();

        $user = array(
            'First_Name' => $firstName,
            'Last_Name' => $lastName,
            'phone_number' => $phoneNumber,
            'delivery_address' => $deliveryAddress,
            'city' => $city,
            'zip_code' => $zipCode,
            'country' => $country,
        );
    } else {
        echo "<p>Error updating account details: " . $stmt->error . "</p>";
    }
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY ACCOUNT</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/LOGO1.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function enableEditing() {
            const inputs = document.querySelectorAll('.myAccount-input');
            const updateButton = document.getElementById('updateButton');
            const warningMessage = document.getElementById('warningMessage');
            inputs.forEach(input => input.removeAttribute('disabled'));
            updateButton.style.display = 'none'; // Hide the update button initially
            warningMessage.style.display = 'none'; // Hide warning message initially

            inputs.forEach(input => {
                input.addEventListener('input', checkFormValidity);
            });
        }

        function checkFormValidity() {
            const inputs = document.querySelectorAll('.myAccount-input');
            const updateButton = document.getElementById('updateButton');
            const warningMessage = document.getElementById('warningMessage');
            let allFilled = true;

            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFilled = false; // If any field is empty
                }
            });

            updateButton.style.display = allFilled ? 'block' : 'none'; // Show button only if all fields are filled
            warningMessage.style.display = allFilled ? 'none' : 'block'; // Show warning if not all fields are filled
        }
    </script>
</head>
<body>
    <section class="header">
        <nav>
            <a href="index.php"><img src="img/logo nav.png" alt="Website Logo"></a>
            <div class="nav-links">
                <ul>
				    <li><a href="index.php"> HOME </a></li>
                    <li><a href="menu.php"> MENU </a></li>
                    <li><a href="account.php"> MY ACCOUNT </a></li>

                </ul>
            </div>
        </nav>
    </section>

    <div class="myAccount-account-details">
        <h2 class="myAccount-heading">My Account</h2>
        <form method="POST" action="account.php">
            <label for="firstName" class="myAccount-label">First Name:</label>
            <input type="text" id="firstName" name="first_name" class="myAccount-input" value="<?php echo htmlspecialchars($user['First_Name']); ?>" required disabled>

            <label for="lastName" class="myAccount-label">Last Name:</label>
            <input type="text" id="lastName" name="last_name" class="myAccount-input" value="<?php echo htmlspecialchars($user['Last_Name']); ?>" required disabled>

            <label for="phoneNumber" class="myAccount-label">Phone Number:</label>
            <input type="tel" id="phoneNumber" name="phone_number" class="myAccount-input" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required disabled>

            <label for="deliveryAddress" class="myAccount-label">Delivery Address:</label>
            <input type="text" id="deliveryAddress" name="delivery_address" class="myAccount-input" value="<?php echo htmlspecialchars($user['delivery_address']); ?>" required disabled>

            <label for="city" class="myAccount-label">City:</label>
            <input type="text" id="city" name="city" class="myAccount-input" value="<?php echo htmlspecialchars($user['city']); ?>" required disabled>

            <label for="zipCode" class="myAccount-label">Zip Code:</label>
            <input type="text" id="zipCode" name="zip_code" class="myAccount-input" value="<?php echo htmlspecialchars($user['zip_code']); ?>" required disabled>

            <label for="country" class="myAccount-label">Country:</label>
            <input type="text" id="country" name="country" class="myAccount-input" value="<?php echo htmlspecialchars($user['country']); ?>" required disabled>

            <button type="button" onclick="enableEditing()" class="myAccount-button">Edit My Account</button>
            <button type="submit" class="myAccount-button" id="updateButton" style="display: none;">Update My Account</button>
            <p id="warningMessage" style="color: red; display: none;">Please fill in all required fields before updating your account.</p>
        </form>
    </div>
</body>
</html>
