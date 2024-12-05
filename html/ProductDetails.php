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


<div id="navbar-container"></div>
    <div class="container">
        <div class="ProductImage">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
        </div>
        <div class="ProductInfo">
            <h1 class="ProductName"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="ProductPrice">₱<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <div class="CartorBuy">
                <button id="addtocart"  onclick="location.href='CartPage.html'" >ADD TO CART</button>
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
<script>
        // JavaScript to load the external navbar HTML
        window.onload = function() {
            fetch('navbar.html')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                });
        };
    </script>
</html>
