<?php
require '../connection/connection.php';
session_start();  // Start session to access user data

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit();
}

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$cartCollection = $client->GADGETHUB->carts;
$productCollection = $client->GADGETHUB->products; // Products collection

$userId = $_SESSION['user_id'];
$cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));

if (count($cartItems) == 0) {
    echo "<p>Your cart is empty. Add some products!</p>";
    exit();
}

// Fetch product details and calculate the total amount
$totalAmount = 0;
foreach ($cartItems as &$item) { // Use reference to modify cart item
    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
    if ($product) {
        $item['image_url'] = $product['images'][0]['url'] ?? '../img/placeholder.png'; // Default to placeholder if no image
        $item['name'] = $product['name'] ?? 'Unknown Product';
    } else {
        $item['image_url'] = '../img/placeholder.png';
        $item['name'] = 'Unknown Product';
    }
    $totalAmount += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Page</title>
    <link rel="stylesheet" href="../css/CartPage.css">
</head>

<body>
    <div id="navbar-container"></div>
    <div class="container">
        <div class="fieldnames">
            <span></span>
            <p id="productlabel">Product</p>
            <span></span> <span></span> <span></span>
            <p id="pricelabel">Unit Price</p>
            <p id="quantitylabel">Quantity</p>
            <p id="actionlabel">Action</p>
        </div>

        <!-- Product list container -->
        <?php foreach ($cartItems as $item): ?>
            <div class="productbox">
                <div class="productdetails">
                    <span></span>
                    <input type="checkbox" id="selectitem" name="selectitem" value="selectitem"><span></span>
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image">
                    <p id="productname"><?php echo htmlspecialchars($item['name']); ?></p>
                    <p id="pricevalue">₱<?php echo number_format($item['price'], 2); ?></p>
                    <div class="quantity">
                        <button onclick="updateQuantity('<?php echo $item['_id']; ?>', 'increase')">+</button>
                        <p><?php echo $item['quantity']; ?></p>
                        <button onclick="updateQuantity('<?php echo $item['_id']; ?>', 'decrease')">-</button>
                    </div>
                    <span></span>
                    <form method="POST" action="CartPage.php">
                        <input type="hidden" name="cart_id" value="<?php echo $item['_id']; ?>">
                        <button type="submit" class="delete"><img src="../img/trash.png" alt="">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <p id="totalitem">Total: ₱<?php echo number_format($totalAmount, 2); ?></p>
    </div>

    <div class="bottomoptions">
        <input type="checkbox" id="selectall" name="selectall" value="selectall">
        <p id="selectalllabel">Select All</p>
        <button id="deleteall"> <img src="../img/trash.png" alt="">Delete</button>

        <button id="checkout" onclick="location.href='checkoutpage.php'">Check Out</button>
    </div>

    <script>
        window.onload = function() {
            fetch('navbar.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                });
        };

    </script>
</body>
</html>
        