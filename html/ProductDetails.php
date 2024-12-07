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
        $selectedQuantity = $_POST['quantity'] ?? 1;

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
        // Assume user is logged in, add product to cart in the database
        $cartCollection = $client->GADGETHUB->carts;

        // Get selected variation from the form
       // Default to 'N/A' if no variation is selected
        $selectedQuantity = $_POST['quantity'] ?? 1;

        // Prepare cart item
        $cartItem = [
            'user_id' => $_SESSION['user_id'],
            'product_id' => $product['_id'],
            'name' => $product['name'],
            'price' => $product['price']['amount'],
            'quantity' => $selectedQuantity,  // Assuming 1 item is added for simplicity
        ];

        $cartCollection->insertOne($cartItem);  // Add to user's cart in DB
        header('Location: CheckoutPage.php');  // Redirect to checkout page immediately after adding to cart
        exit();
    } else {
        // If the user is not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
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
   
    <div class="container">
        <div class="ProductImage">
            <img src="<?php echo htmlspecialchars($product['images'][0]['url']); ?>" alt="Product Image">
        </div>
        <div class="ProductInfo">
            <h1 class="ProductName"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="ProductPrice">₱<?php echo htmlspecialchars(number_format($product['price']['amount'], 2)); ?></p>

            <?php if (isset($product['discount']) && $product['discount']['value'] > 0): ?>
                <p class="ProductDiscount"><?php echo $product['discount']['value']; ?>% OFF</p>
            <?php endif; ?>
            <p class="ProductStock"><?php echo $product['inventory']['stock']; ?> items in stock</p>

            <div class="CartorBuy">
                <form method="post" action="">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['inventory']['stock']; ?>">
                    <button type="submit" name="add_to_cart" id="addtocart">ADD TO CART</button>
                    <button type="submit" name="buy_now" id="buynow">BUY NOW</button>
                
                </form>
            </div>
        </div>
    </div>

    <div class="Specifications">
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

    <div class="Reviews">
        <h2>Customer Reviews</h2>
        <?php if (!empty($product['reviews'])): ?>
            <?php foreach ($product['reviews'] as $review): ?>
                <div class="ReviewItem">
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
            <img src="../img/LOGO1.png">
            <p>"Your Ultimate Destination for Cutting-Edge Technology and Innovation, Where Every Gadget Enthusiast Can
                Discover, Compare, and Purchase the Latest and Greatest Tech Products, All in One Convenient Place."</p>
            <div class="footerbtn">
                <button>Home</button>
                <button>About</button>
                <button>Contact</button>
                <button>Shop</button>
            </div>
        </div>
        <div class="contactus">
            <p>Contact Us:</p>
            <button id="footerfacebook"></button>
            <button id="footerinstagram"></button>
            <button id="footertwitter"></button>
        </div>
    </footer>
</body>

</html>

