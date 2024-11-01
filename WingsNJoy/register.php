<?php
session_start();
include 'config.php';

// Function to display messages in a modal
function showModal($message, $isSuccess) {
    echo "<script>showModal('$message', $isSuccess);</script>";
}

// Initialize error variable
$error = '';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'register_process.php';
}

// Check for a success message in the session
if (isset($_SESSION['success_message'])) {
    echo "<script>showModal('{$_SESSION['success_message']}', true);</script>";
    unset($_SESSION['success_message']); // Clear the success message from the session
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/LOGO1.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<style>
      /* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
}

html, body {
    height: 100%;
}

/* Header Section */
.header {
    width: 100%;
    position: relative;
    margin-bottom: 150px;
}

nav {
    display: flex;
    padding: 1% 6%;
    justify-content: space-between;
    align-items: center;
    background-color: #2C2C54; /* Added a background color */
    position: fixed; /* Keep the nav at the top */
    top: 0;
    width: 100%; /* Span full width */
    z-index: 1000; /* Ensure it stays on top of other elements */
}

nav img {
    width: 200px;
    height: auto; /* Ensures the logo scales properly */
}

.nav-links ul {
    display: flex;
    justify-content: space-between;
}

.nav-links ul li {
    list-style: none;
    display: inline-block;
    padding: 8px 15px; /* Adjust spacing between nav items */
    position: relative;
}

.nav-links ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px; /* Adjusted font size */
    transition: color 0.3s ease;
}

.nav-links ul li a:hover {
    color: #fefefe; /* Optional: Change color on hover for better UX */
}

.nav-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #fafafa;
    display: block;
    margin: auto;
    transition: width 0.5s;
}

.nav-links ul li:hover::after {
    width: 100%;
}

/* Form and Body Styling */
body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #fde799;
    padding-top: 80px; /* Offset for fixed navbar */
}

.form-container {
    width: 600px; /* Increased width for a wider form */
    padding: 30px; /* Adjusted padding to reduce height */
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    background-color: #f7f7f7;
    /* Removed max-height and overflow-y to eliminate scroll */
    margin-top: 600px; /* Added margin to ensure it doesn't overlap the navbar */
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

h2 {
    font-size: 28px; /* Increased font size for better visibility */
    margin-bottom: 10px; /* Increased spacing below the header */
}

input, button.submit-btn {
    width: 100%;
    padding: 12px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button.submit-btn {
    background-color: #e74c3c;
    color: #ffffff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    padding: 15px;
}

button.submit-btn:hover {
    background-color: #c0392b; /* Darker red on hover */
}

.input-container {
    margin-bottom: 10px;
}

.input-container label {
    margin-bottom: 5px;
    display: block;
}

.options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.forgot-password {
    color: #e74c3c;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .form-container {
        width: 90%; /* Make the form responsive */
    }

    h2 {
        font-size: 20px; /* Responsive header size */
    }
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
    <header class="header">
        <nav>
            <img src="img/logo nav.png" alt="WingsNJoy Logo">
            <div class="nav-links">
                <ul>
                    <li><a href="index.php"> HOME </a></li>
                    <li><a href="menu.php"> MENU </a></li>
                    <li><a href="login.php"> LOGIN </a></li>
                    <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="form-container">
        <form action="register.php" method="POST" onsubmit="return validateForm();">
            <h2>Register</h2>
            <div class="input-container">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="input-container">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="input-container">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="input-container">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-container">
                <label for="password">Password</label>
                <input type="password" id="register-password" name="password" required>
                <span class="eye-icon" onclick="togglePasswordVisibility()" style="cursor: pointer;"></span>
            </div>
            <div class="input-container">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
                <span class="eye-icon" onclick="togglePasswordVisibility1()" style="cursor: pointer;"></span>
            </div>
            <div class="input-container">
                <label for="delivery_address">Delivery Address</label>
                <input type="text" id="delivery_address" name="delivery_address" required>
            </div>
            <div class="input-container">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="input-container">
                <label for="zip_code">Zip Code</label>
                <input type="text" id="zip_code" name="zip_code" required>
            </div>
            <div class="input-container">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" required>
            </div>
            <div class="input-container">
                <label>
                    <input type="checkbox" id="terms" name="terms">
                    I agree to the <a href="javascript:void(0);" onclick="openModal('termsModal')">Terms and Conditions</a> and <a href="javascript:void(0);" onclick="openModal('privacyModal')">Privacy Policy</a>
                </label>
            </div>
            <button type="submit" class="submit-btn">Register</button>
            <div class="options">
                <a href="login.php" class="register-link">Already have an account?</a>
            </div>
            <?php if ($error): ?>
                <div class="error-message" style="color: red; text-align: center;"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>
    </div>
    <!-- Modals -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('termsModal')">&times;</span>
            <h2>Terms and Conditions</h2>
            <!-- Terms and Conditions content -->
			<p><strong>Effective Date:</strong> (Last Updated: 26 October 2024)</p>
			<p>Welcome to WingsNJoy! By registering for an account and using our services, you agree to comply with and be bound by the following terms and conditions. Please read these terms carefully.</p>
			
			<p><strong>1. Acceptance of Terms:</strong> By accessing or using our services, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree, please do not use our services.</p>
			
			<p><strong>2. Registration:</strong> To use certain features of our services, you must register for an account. You agree to provide accurate and complete information during registration and to update your information as necessary.</p>
			
			<p><strong>3. User Responsibilities:</strong> You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account. You agree to notify us immediately of any unauthorized use of your account.</p>
			
			<p><strong>4. Use of Services:</strong> You agree to use our services only for lawful purposes and in accordance with these Terms and Conditions. You may not use our services in any way that violates any applicable federal, state, local, or international law.</p>
			
			<p><strong>5. Intellectual Property:</strong> All content and materials on our website, including text, graphics, logos, and images, are the property of WingsNJoy and are protected by copyright and intellectual property laws. You may not reproduce or distribute any content without our prior written consent.</p>
			
			<p><strong>6. Limitation of Liability:</strong> To the fullest extent permitted by law, WingsNJoy shall not be liable for any direct, indirect, incidental, or consequential damages arising from your use of our services.</p>
			
			<p><strong>7. Indemnification:</strong> You agree to indemnify and hold WingsNJoy harmless from any claims, losses, liabilities, damages, costs, or expenses arising from your use of our services or violation of these Terms and Conditions.</p>
			
			<p><strong>8. Changes to Terms:</strong> We reserve the right to modify these Terms and Conditions at any time. Any changes will be effective immediately upon posting on our website. Your continued use of our services after any changes constitutes your acceptance of the new Terms and Conditions.</p>
			
			<p><strong>9. Governing Law:</strong> These Terms and Conditions shall be governed by and construed in accordance with the laws of [Insert Governing Law Jurisdiction].</p>
			
			<p><strong>10. Contact Us:</strong> If you have any questions about these Terms and Conditions, please contact us at:<br>
			Email: wingsnjoydelivery@gmail.com<br>
			Phone: +63 9087633011</p>
        </div>
    </div>
    <div id="privacyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('privacyModal')">&times;</span>
            <h2>Privacy Policy</h2>
            <!-- Privacy Policy content -->
			<p><strong>Effective Date:</strong> (Last Updated: 26 October 2024)</p>
        <p>At WingsNJoy, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, disclose, and safeguard your information when you register and use our services.</p>
        
        <p><strong>Information We Collect:</strong></p>
        <ul>
            <li><strong>Personal Information:</strong> We collect information that you provide to us when registering, including your name, email address, phone number, delivery address, city, zip code, and country.</li>
            <li><strong>Usage Data:</strong> We may collect information about how you access and use our website, including your IP address, browser type, and operating system.</li>
        </ul>
        
        <p><strong>How We Use Your Information:</strong></p>
        <ul>
            <li><strong>To Provide Services:</strong> We use your information to facilitate your registration and provide you with our services.</li>
            <li><strong>Communication:</strong> We may use your information to contact you regarding your account, updates, and promotional offers.</li>
            <li><strong>Improvement:</strong> We use the data to improve our services, user experience, and customer support.</li>
        </ul>
        
        <p><strong>Disclosure of Your Information:</strong></p>
        <ul>
            <li><strong>Third-Party Service Providers:</strong> We may share your information with third-party vendors who assist us in operating our website, conducting our business, or servicing you.</li>
            <li><strong>Legal Requirements:</strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities.</li>
        </ul>
        
        <p><strong>Security of Your Information:</strong> We take reasonable measures to protect your personal information from unauthorized access, use, or disclosure. However, no method of transmission over the Internet or method of electronic storage is 100% secure.</p>
        
        <p><strong>Your Rights:</strong> You have the right to:</p>
        <ul>
            <li>Access the personal information we hold about you.</li>
            <li>Request correction of any inaccurate or incomplete information.</li>
            <li>Request deletion of your personal information.</li>
        </ul>
        
        <p><strong>Changes to This Privacy Policy:</strong> We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
        
        <p><strong>Contact Us:</strong> If you have any questions about this Privacy Policy, please contact us at:<br>
        Email: wingsnjoydelivery@gmail.com<br>
        Phone: +63 9087633011</p>
        </div>
    </div>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('successModal')">&times;</span>
            <p>Registration successful! Please check your email to verify your account.</p>
        </div>
    </div>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('errorModal')">&times;</span>
            <p id="errorMessage"></p>
        </div>
    </div>
    <!-- General Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modal-message"></p>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('register-password');
            const eyeIcon = document.querySelector('.eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = ''; // Change icon to indicate visibility
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = ''; // Change icon back to hidden
            }
        }

        function togglePasswordVisibility1() {
            const passwordInput = document.getElementById('confirm-password');
            const eyeIcon = document.querySelector('.eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = ''; // Change icon to indicate visibility
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = ''; // Change icon back to hidden
            }
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Close modals when clicking outside of the modal content
        window.onclick = function(event) {
            if (event.target.className === "modal") {
                event.target.style.display = "none";
            }
        }

        function validateForm() {
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const termsChecked = document.getElementById('terms').checked;
            let errorMessage = '';

            if (!termsChecked) {
                errorMessage = 'You must agree to the Terms and Conditions.';
            } else if (password.length < 😎 {
                errorMessage = 'Password must be at least 8 characters long.';
            } else if (!/[A-Z]/.test(password)) {
                errorMessage = 'Password must include at least 1 uppercase letter.';
            } else if (!/[a-z]/.test(password)) {
                errorMessage = 'Password must include at least 1 lowercase letter.';
            } else if (!/[0-9]/.test(password)) {
                errorMessage = 'Password must include at least 1 number.';
            } else if (password !== confirmPassword) {
                errorMessage = 'Passwords do not match. Please try again.';
            }

            if (errorMessage) {
                showModal(errorMessage, false);
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        function showModal(message, isSuccess) {
            const modal = document.getElementById('modal');
            const modalMessage = document.getElementById('modal-message');
            modalMessage.textContent = message;
            modal.style.display = 'block';
            // Redirect to index.php if registration is successful
            if (isSuccess) {
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 3000);
            }
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.style.display = 'none';
        }
    </script>
</body>
</html>