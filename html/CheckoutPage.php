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

// Fetch user details (including address)
$userCollection = $client->GADGETHUB->users;
$user = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
if (!$user) {
    die("User not found. Please log in again.");
}

$cartCollection = $client->GADGETHUB->carts;
// Include all items, even if already checked out
$cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));  // Include all items, even if already checked out

// Check if cart is empty
if (count($cartItems) == 0) {
    die("Your cart is empty. Add products to your cart first.");
}

// Calculate the total price and fetch product details
$totalAmount = 0;
foreach ($cartItems as &$item) { // Use reference to modify cart item
    // Fetch product details from the products collection
    $productCollection = $client->GADGETHUB->products; // Assuming this is where your products are stored
    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
    
    if ($product) {
        $item['image_url'] = $product['images'][0]['url'] ?? '../img/placeholder.png'; // Default to placeholder if no image
        $item['product_name'] = $product['name']; // Assuming 'name' is the field for product name
    } else {
        $item['image_url'] = '../img/placeholder.png'; // Fallback if product not found
        $item['product_name'] = 'Unknown Product'; // Fallback product name
    }
    $totalAmount += $item['price'] * $item['quantity'];
}

if (isset($_POST['place_order'])) {
    $deliveryAddress = $_POST['deliveryaddress'] ?? $user['address']; // Use default address if not set
    $paymentMethod = $_POST['payment'];
    $messageForSeller = $_POST['message'] ?? '';

    // Save order details to the orders collection
    $ordersCollection = $client->GADGETHUB->orders;
    $order = [
        'user_id' => $userId,
        'items' => $cartItems,
        'total_amount' => $totalAmount + 85, // Adding shipping cost to total amount
        'payment_method' => $paymentMethod,
        'status' => 'pending',  // Order status
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'delivery_address' => $deliveryAddress,
        'message_for_seller' => $messageForSeller,
        'shipping_cost' => 85  // Example shipping cost
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
    <link rel="stylesheet" href="/css/check.css">
    <link rel="stylesheet" href="/css/navbar.css">
</head>
<body>
    <?php include 'navbar.php' ?>
    <form action="" method="POST">
    <div class="checkout-container">
        <div class="checkout-header">
            <h2>Checkout</h2>
            <p>Complete your order</p>
        </div>

        <div class="checkout-content">
            
            <!-- User Address Section -->
            <div class="section address-section">
                <h3>Shipping Address</h3>
                <form action="#" method="POST">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="deliveryaddress" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </form>
            </div>

            <!-- Cart Items Section -->
            <div class="section cart-section">
                <h3>Order Summary</h3>
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image">
                            <div class="item-info">
                                <p class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                <p class="item-price">₱<?php echo number_format($item['price'], 2); ?></p>
                                <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Payment Method Section -->
            <div class="section payment-section">
                <h3>Payment Method</h3>
                <select name="payment" id="payment-method" required>
                    <option value="cod">Cash on Delivery</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>

            <!-- Summary Section -->
            <div class="section summary-section">
                <h3>Order Summary</h3>
                <p>Merchandise Subtotal: ₱<?php echo number_format($totalAmount, 2); ?></p>
                <p>Shipping: ₱85.00</p>
                <p><strong>Total Payment: ₱<?php echo number_format($totalAmount + 85, 2); ?></strong></p>
            </div>

            <!-- Checkout Button -->
            <div class="checkout-actions">
            <a href="cartpage.php" class="btn btn-secondary">Back to Cart</a>
            <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
               
            </div>
        </div>
    </div>
    </form>
    <script src="https://www.paypal.com/sdk/js?client-id=AdnUxRv5JKvsOmhs2TL_BVcQP_OJEEtLfgOY6o6TddtPgWXbRHWMCKNoLsqkV1kqoxdYxcq13-RHTs4P&currency=USD"></script>
    <script>
        // Example of showing PayPal button when PayPal is selected
        const paymentMethodSelect = document.getElementById('payment-method');
        const paypalButtonContainer = document.createElement('div');
        const codContainer = document.querySelector('.payment-section p:nth-child(1)');

        paymentMethodSelect.addEventListener('change', function() {
            if (paymentMethodSelect.value === 'paypal') {
                // Render PayPal button
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?php echo $totalAmount + 85; ?>'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            alert('Transaction completed by ' + details.payer.name.given_name);
                        });
                    }
                }).render(paypalButtonContainer);

                // Hide COD option
                codContainer.style.display = 'none';
            } else {
                // Hide PayPal button
                paypalButtonContainer.style.display = 'none';

                // Show COD option
                codContainer.style.display = 'block';
            }
        });

        // Adding the PayPal button to the DOM when needed
        document.querySelector('.payment-section').appendChild(paypalButtonContainer);
    </script>

</body>
</html>