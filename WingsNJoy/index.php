<?php
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Only set session variables after a successful login validation
if (!$isLoggedIn && isset($_POST['email']) && isset($_POST['password'])) {
    // Example: Assuming user credentials validation here
    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];
    
    // Replace this with actual credential validation logic
    if (validateUser($userEmail, $userPassword)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $userEmail;
        $isLoggedIn = true;
    }
}

// Example function for user validation
function validateUser($email, $password) {
    // Perform your user validation here (e.g., check against a database)
    // Return true if valid, false otherwise
    return true; // This is just a placeholder, implement actual validation
}
// Prepare modal messages for success and error
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear session messages after use
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOMEPAGE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/LOGO1.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
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
    border-radius: 10px; /* Rounded corners */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
<body>
    <main>
        <!-- Header Section with Navigation -->
       <section class="header">
    <nav>
        <a href="index.php"><img src="img/logo nav.png" alt="Website Logo"></a>
        <div class="nav-links">
            <ul>
                <li><a href="menu.php">MENU</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li><a href="register.php">REGISTER</a></li>
                    <li><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
                <li><a href="account.php">MY ACCOUNT</a></li>
                <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a></li>
            </ul>
        </div>
    </nav>
</section>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h2>OPEN FOR DELIVERY & PICK UP</h2>
                    <h1>GET IT WHILE IT'S HOT</h1>
                    <a href="menu.php" class="cta-button">Order Online</a>
                </div>
                <div class="hero-image">
                    <img src="img/crispy-chicken-bucket.png" alt="Hot Crispy Chicken">
                </div>
            </div>
        </section>

        <!-- All Time Favorites Section -->
        <section class="favorites">
            <h2>All Time Favorites</h2>
            <div class="favorite-items">
                <div class="favorite-item">
                    <img src="IMG/crispy-chicken-sandwich.jpeg" alt="Crispy Chicken Sandwich">
                    <p>CRISPY CHICKEN SANDWICH</p>
                </div>
                <div class="favorite-item">
                    <img src="IMG/SpicyChicken_Wings.jpg" alt="Hot & Spicy Wings">
                    <p>HOT & SPICY WINGS</p>
                </div>
                <div class="favorite-item">
                    <img src="img/french-fries.jpg" alt="French Fries">
                    <p>FRENCH FRIES</p>
                </div>
            </div>
            <a href="menu.php" class="cta-button">Full Menu</a>
        </section>

        <!-- Delivery Section -->
        <section class="delivery-section">
            <div class="delivery-container">
                <div class="delivery-image">
                    <img src="IMG/Paperbag.png" alt="Paper Bag">
                </div>
                <div class="delivery-content">
                    <h2>WE DELIVER</h2>
                    <h3>SATISFYING YOUR CRAVING<br>JUST GOT EASIER</h3>
                    <p>Get ready to satisfy your cravings with our speedy delivery of mouth-watering wings, juicy burgers, crispy fries, and other savory treats.</p>
                    <a href="#" class="order-button">Order Online</a>
                </div>
            </div>
        </section>
    </main>
	<div id="modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span onclick="closeModal()" class="close">&times;</span>
        <p id="modal-message"></p>
    </div>
</div>
	<script>
        function showModal(message, isSuccess) {
            const modal = document.getElementById('modal');
            const modalMessage = document.getElementById('modal-message');
            modalMessage.textContent = message;
            modal.style.display = 'block';

            if (isSuccess) {
                setTimeout(() => {
                    window.location.href = 'index.php'; // Optional: Redirect after displaying success message
                }, 3000);
            }
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onload = function() {
            <?php if ($successMessage): ?>
                showModal("<?php echo $successMessage; ?>", true);
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                showModal("<?php echo $errorMessage; ?>", false);
            <?php endif; ?>
        };
    </script>
	
</body>
</html>