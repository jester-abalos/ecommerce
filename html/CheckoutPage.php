<?php
require '../connection/connection.php';
session_start();  // Start session to store cart

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch the user ID from the session
$userId = $_SESSION['user_id'];

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");

// Fetch user details (including address)
$userCollection = $client->GADGETHUB->users;
$user = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
if (!$user) {
    die("User not found. Please log in again.");
}

$cartCollection = $client->GADGETHUB->carts;
$productCollection = $client->GADGETHUB->products; // Products collection

// Fetch cart items for the logged-in user
$cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));

if (count($cartItems) == 0) {
    die("Your cart is empty. Add products to your cart first.");
}

// Calculate the total price and fetch product details
$totalAmount = 0;
foreach ($cartItems as &$item) { // Use reference to modify cart item
    // Fetch product details from the products collection
    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
    if ($product) {
        $item['image_url'] = $product['images'][0]['url'] ?? '../img/placeholder.png'; // Default to placeholder if no image
    } else {
        $item['image_url'] = '../img/placeholder.png'; // Fallback if product not found
    }
    $totalAmount += $item['price'] * $item['quantity'];
}

// Handle order placement
if (isset($_POST['place_order'])) {
    $deliveryAddress = $_POST['deliveryaddress'];
    $paymentMethod = $_POST['payment'];
    $messageForSeller = $_POST['message'];

    // Save order details to the orders collection
    $ordersCollection = $client->GADGETHUB->orders;
    $order = [
        'user_id' => $userId,
        'items' => $cartItems,
        'total_amount' => $totalAmount,
        'delivery_address' => $deliveryAddress,
        'payment_method' => $paymentMethod,
        'message_for_seller' => $messageForSeller,
        'status' => 'pending',  // Order status
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    // Insert order into orders collection
    $ordersCollection->insertOne($order);

    // Clear the cart after placing the order
    $cartCollection->deleteMany(['user_id' => $userId]);

    header('Location: OrderConfirmation.php');  // Redirect to a confirmation page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="../css/CheckoutPage.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <div id="navbar-container"></div>
    <div class="container">
        <form method="POST">
            <div class="checkoutbox">
                <!-- Address Section -->
                <div class="addressbox">
                    <label for="deliveryaddress"><img src="../img/locationpin.png" alt="">Delivery Address</label>
                    <p id="deliveryaddress">
                        <?php echo htmlspecialchars($user['address'] ?? 'No address found. Please update your profile.', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>

                <!-- Product List Section -->
                <div class="productbox">
                    <p id="productsorderedtitle">Products Ordered</p>
                    <div class="productsordered">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="product1">
                                <div class="fieldnames">
                                    <p>Unit Price</p>
                                    <p>Quantity</p>
                                    <p>Sub-Total</p>
                                </div>
                                <div class="productdetails">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image">
                                    <p id="productname"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p id="unitpricevalue">₱<?php echo number_format($item['price'], 2); ?></p>
                                    <p id="quantityvalue"><?php echo $item['quantity']; ?></p>
                                    <p id="subtotalvalue">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Message and Shipping Section -->
                <div class="messageshipping">
                    <label for="message">Messages for Seller:</label>
                    <input type="text" id="message" name="message">
                </div>

                <!-- Order Total Section -->
                <div class="ordertotal">
                    <p id="ordertotallabel">Order Total (# Items):</p>
                    <p id="ordertotalvalue">₱<?php echo number_format($totalAmount, 2); ?></p>
                </div>
            </div>

            <!-- Payment and Summary Section -->
            <div class="summarybox">
                <div class="paymentdiv">
                    <label for="payment">Payment Method:</label>
                    <select name="payment" id="payment" required>
                        <option value="cod">Cash on Delivery</option>
                        <option value="paypal">Paypal</option>
                    </select>
                </div>

                <div class="calculation">
                    <p>Merchandise Subtotal:</p>
                    <p id="merchandisevalue">₱<?php echo number_format($totalAmount, 2); ?></p>
                    <p>Shipping Subtotal:</p>
                    <p id="shippingvalue">₱85</p> <!-- Example shipping cost -->
                    <p>Total Payment:</p>
                    <p id="totalpaymentvalue">₱<?php echo number_format($totalAmount + 85, 2); ?></p>
                </div>

                <button type="submit" name="place_order" id="placeorder">Place Order</button>
            </div>
        </form>
    </div>
    <script>
        // JavaScript to load the external navbar HTML
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
