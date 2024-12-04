<?php
require '../connection/connection.php';

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->products;

// Get product ID from query string
$productId = $_GET['id'] ?? null;
if ($productId) {
    $product = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if (!$product) {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="../css/ProductDetails.css">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="#home"><img src="../img/LOGO1.png" alt="Logo"></a>
        </div>
        <ul class="navbar-links" id="navbar-links">
            <li><a href="Dashboard.php">Home</a></li>
            <li><a href="Categories.php">Categories</a></li>
            <li><a href="#Brands">Brands</a></li>
            <li><a href="#Order">Order</a></li>
        </ul>
        <div class="search-container">
            <input type="text" placeholder="Search..." id="search-bar">
            <span id="search-icon"><img src="../img/search.png" alt="Search"></span>
        </div>
        <div class="cart-user">
            <img src="../img/cart.png" alt="Cart">
            <span></span>
            <a href="ManageProfile.html"><img src="../img/user.png" alt="User"></a>
        </div>
        <div class="navbar-toggle" id="navbar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container">
        <div class="ProductImage">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
        </div>
        <div class="ProductInfo">
            <h1 class="ProductName"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="ProductPrice">₱<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <div class="CartorBuy">
                <button id="addtocart">ADD TO CART</button>
                <button id="buy">BUY</button>
            </div>
        </div>
    </div>

    <div class="Specifications">
        <h2>Specifications</h2>
        <ul>
            <?php if (!empty($product['specifications'])): ?>
                <?php foreach ($product['specifications'] as $key => $value): ?>
                    <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No specifications available.</li>
            <?php endif; ?>
        </ul>
    </div>

    <footer class="footer">
        <div class="about">
            <img src="../img/LOGO1.png">
            <p>"Your Ultimate Destination for Cutting-Edge Technology and Innovation."</p>
        </div>
    </footer>
</body>

</html>