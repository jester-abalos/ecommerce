<?php
require '../connection/connection.php';
session_start();  // Start session to store cart

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$cartCollection = $client->GADGETHUB->carts;

// Fetch cart items for the logged-in user
$userId = $_SESSION['user_id'];
$cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));

if (count($cartItems) == 0) {
    die("Your cart is empty. Add products to your cart first.");
}

// Calculate the total price
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Handle order placement
if (isset($_POST['place_order'])) {
    $deliveryAddress = $_POST['deliveryaddress'];
    $paymentMethod = $_POST['payment'];
    $messageForSeller = $_POST['message'];
    // You can also save order details to the orders collection
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

    // Optionally, clear the cart after placing the order
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
                <div class="addressbox">
                    <label for="deliveryaddress"><img src="../img/locationpin.png" alt="">Delivery Address</label>
                    <input type="text" id="deliveryaddress" name="deliveryaddress" required>
                </div>

                <div class="productbox">
                    <p id="productsorderedtitle">Products Ordered</p>
                    <div class="productsordered">
                        <!-- PHP loop to display cart items -->
                        <?php foreach ($cartItems as $item): ?>
                            <div class="product1">
                                <div class="fieldnames">
                                    <p>Unit Price</p>
                                    <p>Quantity</p>
                                    <p>Sub-Total</p>
                                </div>
                                <div class="productdetails">
                                    <img src="../img/checkoutimg.png" alt="">
                                    <p id="productname"><?php echo htmlspecialchars($item['name']); ?></p>

                                    <!-- Check if variation exists -->
                                    <?php if (isset($item['variation'])): ?>
                                        <p id="variation"><?php echo htmlspecialchars($item['variation']); ?></p>
                                    <?php else: ?>
                                        <p id="variation">N/A</p>
                                    <?php endif; ?>
                                    <p id="unitpricevalue">₱<?php echo number_format($item['price'], 2); ?></p>
                                    <p id="quantityvalue"><?php echo $item['quantity']; ?></p>
                                    <p id="subtotalvalue">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="messageshipping">
                    <label for="message">Messages for Seller:</label>
                    <input type="text" id="message" name="message">
                    <span></span>
                    <label for="shipping">Shipping Option:</label>
                    <div id="shipping">
                        <button id="change">Change</button>
                    </div>
                </div>

                <div class="ordertotal">
                    <p id="ordertotallabel">Order Total (# Items):</p>
                    <p id="ordertotalvalue">₱<?php echo number_format($totalAmount, 2); ?></p>
                </div>
            </div>

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
                    <p>Voucher Discount:</p>
                    <p id="discountvalue">₱0</p> <!-- Example discount -->
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
</html>
