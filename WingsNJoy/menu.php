<?php
session_start();
include 'config.php';

$user_email = "user@example.com"; // Replace with actual user email from session or login form
$user_query = $conn->prepare("SELECT id, First_Name, phone_number, delivery_address FROM users WHERE email = ?");
$user_query->bind_param("s", $user_email);
$user_query->execute();
$user_query->bind_result($user_id, $first_name, $phone_number, $delivery_address);
$user_query->fetch();
$user_query->close();

$_SESSION['user_id'] = $user_id; // Setting the correct user ID in session
$_SESSION['user_name'] = $first_name;
$_SESSION['user_phone'] = $phone_number;
$_SESSION['user_address'] = $delivery_address;


// Initialize menu items (you may want to load this from a database)
$menuItems = array();

// Example menu items (Remove or load dynamically as needed)
$menuItems[] = array('id' => 1, 'name' => 'Cheeseburger Deluxe', 'price' => 100.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 2, 'name' => 'Bacon Cheeseburger', 'price' => 120.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 3, 'name' => '4 Cheeseburger', 'price' => 150.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 4, 'name' => 'Classic Burger', 'price' => 80.00, 'category' => 'Burgers');
$menuItems[] = array('id' => 5, 'name' => 'Plant-Based Burger', 'price' => 200.00, 'category' => 'Burgers');

// wings 
$menuItems[] = array('id' => 6, 'name' => 'Buffalo Wings', 'price' => 150.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 7, 'name' => 'BBQ Wings', 'price' => 150.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 8, 'name' => 'Salted Egg Wings', 'price' => 160.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 9, 'name' => 'Garlic Parmesan Wings', 'price' => 170.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 10, 'name' => 'Honey BBQ Wings', 'price' => 160.00, 'category' => 'Chicken Wings');
$menuItems[] = array('id' => 11, 'name' => 'Teriyaki Wings', 'price' => 170.00, 'category' => 'Chicken Wings');

// drinks
$menuItems[] = array('id' => 9, 'name' => 'Iced tea', 'price' => 60.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 8, 'name' => 'Coca-Cola', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 9, 'name' => 'Pepsi', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 10, 'name' => 'Sprite', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 11, 'name' => '7Up', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 12, 'name' => 'Royal', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 13, 'name' => 'Sarsi', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));
$menuItems[] = array('id' => 14, 'name' => 'Coca-Cola Zero', 'price' => 50.00, 'category' => 'Drinks', 'sizes' => array('Small' => 50, 'Medium' => 60, 'Large' => 70));


//fries
$menuItems[] = array('id' => 10, 'name' => 'Small Fries', 'price' => 40.00, 'category' => 'French Fries');
$menuItems[] = array('id' => 11, 'name' => 'Medium Fries', 'price' => 60.00, 'category' => 'French Fries');
$menuItems[] = array('id' => 12, 'name' => 'Large Fries', 'price' => 80.00, 'category' => 'French Fries');




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
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s;
        }

        .myapp-cart-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-height: 80%;
            overflow-y: auto;
        }

        .myapp-cart-content h2 {
            margin-top: 0;
        }

        .myapp-cart-close {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            float: right;
        }

        .myapp-cart-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .myapp-cart-item input {
            width: 50px;
            text-align: center;
        }

        .myapp-cart-remove {
            color: #dc3545;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .myapp-checkout {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 10px auto;
            width: 100%;
            text-align: center;
        }

        .myapp-checkout:hover {
            background-color: #218838;
        }
    </style>
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

                    <button class="myapp-menu-add-to-cart" onclick="addToCart('<?php echo htmlspecialchars($item['name']); ?>', <?php echo htmlspecialchars($item['price']); ?>, '<?php echo $item['sizes'] ? 'size-' . $item['id'] : ''; ?>')">ADD TO CART</button>
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

function addToCart(name, price, sizeId) {
    const sizeSelect = document.getElementById(sizeId);
    const selectedSize = sizeSelect ? sizeSelect.value : null;
    const sizePrice = sizeSelect ? parseFloat(sizeSelect.options[sizeSelect.selectedIndex].getAttribute('data-price')) : 0;
    // Find item in cart
    const existingItem = cartItems.find(item => item.name === name && item.size === selectedSize);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cartItems.push({ name, price: price + sizePrice, quantity: 1, size: selectedSize });
    }
    cartCount++;
    document.getElementById('cart-count').innerText = cartCount;
    updateCart();
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