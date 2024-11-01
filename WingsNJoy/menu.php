<?php
session_start();
include 'config.php';

if (isset($_SESSION['email'])) {
    $_SESSION['loggedin'] = true;
} else {
    $_SESSION['loggedin'] = false;
}

// Initialize cart if not already initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Retrieve user information if logged in
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
if ($email) {
    $user_query = $conn->prepare("SELECT id, First_Name, phone_number, delivery_address FROM users WHERE email = ?");
    if (!$user_query) {
        die("Error preparing statement: " . $conn->error);
    }
    $user_query->bind_param("s", $email);
    $user_query->execute();
    $user_query->bind_result($user_id, $first_name, $phone_number, $delivery_address);
    $user_query->fetch();
    $user_query->close();

    // Store user info in session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $first_name;
    $_SESSION['user_phone'] = $phone_number;
    $_SESSION['user_address'] = $delivery_address;
}



// Initialize menu items (you may want to load this from a database)
$menuItems = array();
// Example menu items (Remove or load dynamically as needed)
$menuItems[] = array('id' => 1, 'name' => 'Cheeseburger Deluxe', 'price' => 100.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 2, 'name' => 'Bacon Cheeseburger', 'price' => 120.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 3, 'name' => '4 Cheeseburger', 'price' => 150.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 4, 'name' => 'Classic Burger', 'price' => 80.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 5, 'name' => 'Plant-Based Burger', 'price' => 200.00, 'category' => 'Burgers');

// Wings
$menuItems[] = array('id' => 6, 'name' => 'Buffalo Wings', 'price' => 150.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 7, 'name' => 'BBQ Wings', 'price' => 150.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 8, 'name' => 'Salted Egg Wings', 'price' => 160.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 9, 'name' => 'Garlic Parmesan Wings', 'price' => 170.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 10, 'name' => 'Honey BBQ Wings', 'price' => 160.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 11, 'name' => 'Teriyaki Wings', 'price' => 170.00, 'category' => 'Chicken Wings');

// Drinks
$menuItems[] = array('id' => 12, 'name' => 'Iced tea', 'price' => 60.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 13, 'name' => 'Coca-Cola', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 14, 'name' => 'Pepsi', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 15, 'name' => 'Sprite', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 16, 'name' => '7Up', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 17, 'name' => 'Royal', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 18, 'name' => 'Sarsi', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 19, 'name' => 'Coca-Cola Zero', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));

// Fries
$menuItems[] = array('id' => 20, 'name' => 'Small Fries', 'price' => 40.00, 'category' => 'French Fries');
$menuItems[] = array('id' => 21, 'name' => 'Medium Fries', 'price' => 60.00, 'category' => 'French Fries');
$menuItems[] = array('id' => 22, 'name' => 'Large Fries', 'price' => 80.00, 'category' => 'French Fries');

var_dump($_SESSION['email']); // This will help you see what's actually in the session.


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MENU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/LOGO1.png" type="image/png">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
/* Cart Modal Styles */
.myapp-cart-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(44, 44, 84, 0.8); /* Semi-transparent background */
    z-index: 2000;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s;
}

.myapp-cart-content {
    background: #fff; /* Clean white background for the modal */
    padding: 30px; /* Increased padding for a more spacious feel */
    border-radius: 12px; /* Slightly rounded corners */
    width: 600px; /* Increased width for better content fit */
    max-height: 80%;
    overflow-y: auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Deeper shadow for a more elevated look */
}


.myapp-cart-content h2 {
    margin-top: 0;
    color: #2C2C54; /* Darker color for the heading */
    font-size: 24px; /* Increased font size for emphasis */
    font-weight: 600; /* Slightly bolder font weight */
    text-align: center; /* Centering the heading */
}

.myapp-cart-close {
    background: #dc3545; /* Red color for close button */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 12px 15px; /* More padding for a larger click area */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease; /* Added transform for a subtle effect */
    position: absolute; /* Positioned relative to the modal */
    top: 20px; /* Positioned towards the top */
    right: 20px; /* Positioned towards the right */
}

.myapp-cart-close:hover {
    background: #c82333; /* Darker red on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

.myapp-cart-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0; /* Increased padding for better spacing */
    border-bottom: 1px solid #eee; /* Light separator between items */
}

.myapp-cart-item:last-child {
    border-bottom: none; /* Remove border for the last item */
}

.myapp-cart-item input {
    width: 70px; /* Adjusted width for better fit */
    text-align: center;
    border: 1px solid #ccc; /* Subtle border for input */
    border-radius: 5px; /* Rounded corners for input */
    padding: 12px; /* Increased padding for better touch area */
    font-size: 18px; /* Increased font size for better visibility */
}

/* Make the buttons larger */
.myapp-cart-button {
    background-color: #e0e0e0; /* Light background for buttons */
    color: #333; /* Dark color for better visibility */
    border: 1px solid #ccc; /* Subtle border */
    border-radius: 5px; /* Rounded corners */
    padding: 15px; /* Increased padding for a larger click area */
    font-size: 24px; /* Increased font size for better visibility */
    cursor: pointer; /* Change cursor on hover */
    transition: background-color 0.3s ease; /* Smooth background color transition */
    width: 60px; /* Set a fixed width for the buttons */
	 height: 40px; /* Fixed height for buttons */
    margin: 0 5px; /* Spacing between buttons */
}
.myapp-cart-button:hover {
    background-color: #d5d5d5; /* Slightly darker on hover */
}
.myapp-cart-remove {
    color: #dc3545; /* Red color for remove button */
    cursor: pointer;
    transition: color 0.3s ease;
}

.myapp-cart-remove:hover {
    color: #c82333; /* Darker red on hover */
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.myapp-checkout {
    background-color: #28a745; /* Green checkout button */
    color: white;
    padding: 12px; /* Increased padding for comfort */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin: 20px auto 0; /* Centered with margin at the top */
    width: 100%;
    text-align: center;
    font-size: 18px; /* Increased font size for emphasis */
    transition: background-color 0.3s ease, transform 0.2s ease; /* Added transform for a subtle effect */
}

.myapp-checkout:hover {
    background-color: #218838; /* Darker green on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}



    </style>
	
<script>
    var isLoggedIn = <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'true' : 'false'; ?>;
    console.log('isLoggedIn:', isLoggedIn); // Debugging
    </script>


</head>
<body class="myapp-menu-body">
    <main>
        <!-- Header Section with Navigation -->
        <section class="myapp-menu-header">
            <nav class="myapp-menu-nav">
                <a href="index.php"><img src="img/logo nav.png" alt="Website Logo"></a>
                <div class="myapp-menu-nav-links">
                    <ul>
                        <li><a href="index.php"> HOME </a></li>
                        <li><a href="menu.php"> MENU </a></li>
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li><a href="logout.php"> LOGOUT </a></li>
                        <?php else: ?>
                            <li><a href="register.php">REGISTER </a></li>
                            <li><a href="login.php"> LOGIN </a></li>
                        <?php endif; ?>
                        <li><a href="account.php"> MY ACCOUNT </a></li>
                        <li><a href="#" class="cart-icon" onclick="toggleCart()"><i class="fas fa-shopping-cart"></i> <span id="cart-count">0</span></a></li>
                    </ul>
                </div>
            </nav>
        </section>

        <!-- Tabs for Menu Categories -->
        <div class="myapp-menu-tabs">
            <button class="myapp-menu-tab" onclick="showCategory('Burgers')">BURGERS</button>
            <button class="myapp-menu-tab" onclick="showCategory('Chicken Wings')">CHICKEN WINGS</button>
            <button class="myapp-menu-tab" onclick="showCategory('Drinks')">DRINKS</button>
            <button class="myapp-menu-tab" onclick="showCategory('French Fries')">FRENCH FRIES</button>
        </div>

        <!-- Product List -->
        <section class="myapp-menu-product-list" id="product-list">
            <?php foreach ($menuItems as $item): ?>
                <div class="myapp-menu-product myapp-menu-product-<?php echo strtolower(str_replace(' ', '-', $item['category'])); ?>" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                    <img src="img/<?php echo strtolower(str_replace(' ', '-', $item['name'])); ?>.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>₱<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></p>

                    <?php if (isset($item['sizes'])): ?>
                        <select id="size-<?php echo $item['id']; ?>">
                            <?php foreach ($item['sizes'] as $size => $sizePrice): ?>
                                <option value="<?php echo $size; ?>" data-price="<?php echo $sizePrice; ?>"><?php echo $size; ?> - ₱<?php echo $sizePrice; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>

                    <button class="myapp-menu-add-to-cart" 
    onclick="addToCart(
        '<?php echo htmlspecialchars($item['name']); ?>', 
        <?php echo htmlspecialchars($item['price']); ?>, 
        '<?php echo $item['id']; ?>' // Use id as product_id
    )">
    ADD TO CART
</button>

                </div>
            <?php endforeach; ?>
        </section>

        <!-- Cart Modal -->
        <div class="myapp-cart-modal" id="cart-modal">
            <div class="myapp-cart-content">
                <button class="myapp-cart-close" onclick="toggleCart()">Close</button>
                <h2>Your Cart</h2>
                <div id="cart-items"></div>
                <h3>Total: <span id="cart-total">₱0.00</span></h3>
                <button class="myapp-checkout" onclick="checkout()">Checkout</button>
            </div>
        </div>

    </main>




<script>

let cartItems = [];
let total = 0;
let cartCount = 0;

const userIsLoggedIn = <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>;

function isLoggedIn() {
    return userIsLoggedIn;
}

function addToCart(name, price, sizeId, productId) {
    // Check if the user is logged in
    if (!isLoggedIn()) {
        console.log('User is not logged in.');
        alert('Please log in first to add items to the cart.');
        return; // Prevent adding to cart
    }

    const sizeSelect = document.getElementById(sizeId);
    const selectedSize = sizeSelect ? sizeSelect.value : null;
    const sizePrice = sizeSelect ? parseFloat(sizeSelect.options[sizeSelect.selectedIndex].getAttribute('data-price')) : 0;

    // Find item in cart
    const existingItem = cartItems.find(item => item.name === name && item.size === selectedSize);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cartItems.push({ name, price: price + sizePrice, quantity: 1, size: selectedSize, productId });
    }

    cartCount++;
    document.getElementById('cart-count').innerText = cartCount;
    updateCart();
}




function isLoggedIn() {
    // Check if the user is logged in by examining the presence of email in the session
    return <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>;
}



function updateCart() {
    const cartItemsDiv = document.getElementById('cart-items');
    cartItemsDiv.innerHTML = ''; // Clear existing items
    total = 0; // Reset total
    cartCount = 0; // Reset cart count

    console.log('Updating Cart... Current Cart Items:', cartItems); // Log the current cart items

    cartItems.forEach((item, index) => {
        // Detailed inspection of each item
        console.log('Inspecting Item:', item);
        if (typeof item.name === 'undefined' || typeof item.price === 'undefined') {
            console.error('Invalid item structure:', item);
        }

        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        cartCount += item.quantity; // Update cart count

        const itemDiv = document.createElement('div');
        itemDiv.className = 'myapp-cart-item';

        // Instead of using template literals, we'll concatenate the strings
        itemDiv.innerHTML = '<span>' + item.name + ' (' + (item.size || 'N/A') + ') - ₱' + item.price.toFixed(2) + '</span>' +
            '<span>' +
                '<button onclick="updateQuantity(' + index + ', -1)">-</button>' +
                '<input type="number" value="' + item.quantity + '" min="1" onchange="updateQuantity(' + index + ', this.value)">' +
                '<button onclick="updateQuantity(' + index + ', 1)">+</button>' +
            '</span>';

        cartItemsDiv.appendChild(itemDiv);
    });

    document.getElementById('cart-total').innerText = '₱' + total.toFixed(2);
    document.getElementById('cart-count').innerText = cartCount; // Update mini cart quantity

    console.log('Updated Cart Items:', cartItems); // Log the updated cart items
}



function updateQuantity(index, change) {
    const item = cartItems[index];
    if (item) {
        if (typeof change === 'number') {
            item.quantity += change;
        } else {
            item.quantity = parseInt(change);
        }

        if (item.quantity <= 0) {
            cartItems.splice(index, 1);
            updateCart();
        } else {
            updateCart();
        }
    }
}

function toggleCart() {
    const cartModal = document.getElementById('cart-modal');
    if (cartModal.style.display === 'flex') {
        cartModal.style.display = 'none'; // Hide cart
    } else {
        cartModal.style.display = 'flex'; // Show cart
    }
}

function removeFromCart(name, size) {
    const itemIndex = cartItems.findIndex(item => item.name === name && item.size === size);
    if (itemIndex > -1) {
        cartCount -= cartItems[itemIndex].quantity;
        cartItems.splice(itemIndex, 1);
    }
    document.getElementById('cart-count').innerText = cartCount;
    updateCart();
}

function checkout() {
    if (cartCount === 0) {
        alert("Your cart is empty!");
        return;
    }

    // Validate cart items before JSON.stringify
    console.log('Cart Items:', cartItems);
    cartItems.forEach((item, index) => {
        console.log('Inspecting Item:', item);
        if (typeof item.name === 'undefined' || typeof item.price === 'undefined') {
            console.error('Invalid item structure:', item);
        }
    });

    // Stringify cart items
    const cartData = JSON.stringify(cartItems);
    const subtotalData = total.toFixed(2);

    // Debugging logs
    console.log('Cart Data to be Stored:', cartData);
    console.log('Subtotal Data to be Stored:', subtotalData);
    console.log('Cart Data Type:', typeof cartData);

    // Verify JSON data
    try {
        JSON.parse(cartData);
    } catch (e) {
        console.error('Invalid JSON data:', e);
        alert("Invalid cart data. Please try again.");
        return;
    }

    localStorage.setItem("cart", cartData);
    localStorage.setItem("subtotal", subtotalData);

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "checkout_receipt.php";

    const cartInput = document.createElement("input");
    cartInput.type = "hidden";
    cartInput.name = "cart";
    cartInput.value = cartData;

    const subtotalInput = document.createElement("input");
    subtotalInput.type = "hidden";
    subtotalInput.name = "subtotal"
    subtotalInput.value = subtotalData;

    form.appendChild(cartInput);
    form.appendChild(subtotalInput);

    document.body.appendChild(form);
    console.log('Form Data (before submit):', new FormData(form));

    form.submit();
}






function showCategory(category) {
    const products = document.querySelectorAll('.myapp-menu-product');
    products.forEach(product => {
        if (product.getAttribute('data-category') === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

showCategory('Burgers');



</script>
</body>
</html>