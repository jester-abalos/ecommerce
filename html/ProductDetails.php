<?php
require '../connection/connection.php';
session_start();  // Start session to store cart

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->products;  // Initialize the $collection variable for products

// Get product ID from query string
$productId = $_GET['id'] ?? null;
if ($productId) {
    // Find the product by its ID
    $product = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if (!$product) {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}

// Handle Add to Cart action
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        // Assume user is logged in, add product to cart in the database
        $cartCollection = $client->GADGETHUB->carts;

        // Get selected variation from the form
       // Default to 'N/A' if no variation is selected
        $selectedQuantity = $_POST['quantity'];

        // Prepare cart item
        $cartItem = [
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product['_id'],
            'name' => $product['name'],
            'price' => $product['price']['amount'],
            'quantity' => $selectedQuantity,
            'added_to_cart_at' => new MongoDB\BSON\UTCDateTime(),
        ];

        $cartCollection->insertOne($cartItem);  // Add to user's cart in DB
        header('Location: dashboard.php');  // Redirect to cart page after adding to cart
        exit();
    } else {
        // If the user is not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }
}
// Handle Buy Now action
if (isset($_POST['buy_now'])) {
    if (isset($_SESSION['user_id'])) {
        // Assume user is logged in, direct buy in the database
        $buyCollection = $client->GADGETHUB->buy;

        // Get selected variation from the form
        // Default to 'N/A' if no variation is selected
        $selectedQuantity = $_POST['quantity'] ?? 1;

        // Prepare buy item
        $buyItem = [
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product['_id'],
            'name' => $product['name'],
            'price' => $product['price']['amount'],
            'quantity' => $selectedQuantity, // Assuming 1 item is added for simplicity
        ];

        // Insert into the buy collection
        $buyCollection->insertOne($buyItem);  // Corrected variable: $buyCollection
        header('Location: CheckoutPage.php');  // Redirect to checkout page after adding to buy
        exit();
    } else {
        // If the user is not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="../css/ProductDetails.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>
    <?php include './navbar.php'?>
    <div class="container">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['images'][0]['url']); ?>" alt="Product Image">
        </div>
        <div class="product-info">
            <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">₱<?php echo htmlspecialchars(number_format($product['price']['amount'], 2)); ?></p>

            <?php if (isset($product['discount']) && $product['discount']['value'] > 0): ?>
                <p class="product-discount"><?php echo $product['discount']['value']; ?>% OFF</p>
            <?php endif; ?>
            <p class="product-stock"><?php echo $product['inventory']['stock']; ?> items in stock</p>

            <div class="cart-or-buy">
                <form method="post" action="">
                <div id="quantity-container">
                <button type="button" id="quantity-button-down" onclick="decrementQuantity()">-</button>
                <input id="quantity" type="number" name="quantity" value="1" min="1" max="<?php echo $product['inventory']['stock']; ?>" onchange="updateQuantity()">
                 <button type="button" id="quantity-button-up" onclick="incrementQuantity()">+</button>
                 </div>

                    <button type="submit" name="add_to_cart" id="add-to-cart">ADD TO CART</button>
                    <button type="submit" name="buy_now" id="buy-now">BUY NOW</button>
                </form>
            </div>
        </div>
    </div>

    <div class="specifications">
        <h2>Specifications</h2>
        <ul>
            <?php if (!empty($product['attributes'])): ?>
                <?php foreach ($product['attributes'] as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No specifications available.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="reviews">
        <h2>Customer Reviews</h2>
        <?php if (!empty($product['reviews'])): ?>
            <?php foreach ($product['reviews'] as $review): ?>
                <div class="review-item">
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?> stars</p>
                    <p><strong>Comment:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>
                    <p><strong>Posted on:</strong> <?php echo $review['timestamp']->toDateTime()->format('Y-m-d H:i'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>

    <footer id="footer" class="footer">
        <div class="about">
            <img src="../img/LOGO1.png" alt="Company Logo">
            <p>"Your Ultimate Destination for Cutting-Edge Technology and Innovation, Where Every Gadget Enthusiast Can
                Discover, Compare, and Purchase the Latest and Greatest Tech Products, All in One Convenient Place."</p>
            <div class="footer-btn">
                <button>Home</button>
                <button>About</button>
                <button>Contact</button>
                <button>Shop</button>
            </div>
        </div>
        
    </footer>
    <script>
        // Function to increment quantity
function incrementQuantity() {
    var quantityInput = document.getElementById("quantity");
    var currentQuantity = parseInt(quantityInput.value);
    var maxQuantity = parseInt(quantityInput.max);

    if (currentQuantity < maxQuantity) {
        quantityInput.value = currentQuantity + 1;
    }
}

// Function to decrement quantity
function decrementQuantity() {
    var quantityInput = document.getElementById("quantity");
    var currentQuantity = parseInt(quantityInput.value);
    
    if (currentQuantity > 1) {
        quantityInput.value = currentQuantity - 1;
    }
}

// Ensure input is within the valid range
function updateQuantity() {
    var quantityInput = document.getElementById("quantity");
    var minQuantity = parseInt(quantityInput.min);
    var maxQuantity = parseInt(quantityInput.max);
    var currentQuantity = parseInt(quantityInput.value);

    if (currentQuantity < minQuantity) {
        quantityInput.value = minQuantity;
    } else if (currentQuantity > maxQuantity) {
        quantityInput.value = maxQuantity;
    }
}

    </script>
</body>
</html>
