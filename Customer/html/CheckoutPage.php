<?php 
require '../../connection/connection.php';
session_start();

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
    header('Location: login.php');
    exit("User not found. Please log in again.");
}

// Fetch cart items from the `carts` collection
$cartCollection = $client->GADGETHUB->carts;
$cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));

// Check if the cart is empty
if (empty($cartItems)) {
    die("Your cart is empty. Add products to your cart first.");
}

// Calculate the total price and fetch product details
$totalAmount = 0;
$productCollection = $client->GADGETHUB->products;

foreach ($cartItems as &$item) { // Pass by reference to update items directly
    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
    if ($product) {
        $item['image'] = $product['image'] ?? '../img/placeholder.png';
        $item['name'] = $product['name'] ?? 'Unknown Product';
        $item['price'] = $product['price'] ?? 0;
    } else {
        $item['image'] = '../img/placeholder.png';
        $item['name'] = 'Unknown Product';
        $item['price'] = 0;
    }
    $totalAmount += $item['price'] * (int)($item['quantity'] ?? 1);
}

// Handle "Place Order" action
if (isset($_POST['place_order'])) {
    $deliveryAddress = $_POST['deliveryaddress'] ?? $user['address'];
    $paymentMethod = $_POST['payment'];
    $messageForSeller = $_POST['message'] ?? '';

    $ordersCollection = $client->GADGETHUB->orders;
    $order = [
        'user_id' => $userId,
        'items' => $cartItems,
        'total_amount' => $totalAmount + 85,
        'payment_method' => $paymentMethod,
        'status' => 'pending',
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'delivery_address' => $deliveryAddress,
        'message_for_seller' => $messageForSeller,
        'shipping_cost' => 85
    ];

    $ordersCollection->insertOne($order);

    // Clear the cart
    $cartCollection->deleteMany(['user_id' => $userId]);

    header('Location: OrderConfirmation.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="../css/check.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
<?php include '../html/navbar.php'; ?>
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
                <label for="address">Address</label>
                <input type="text" id="address" name="deliveryaddress" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
            </div>

            <!-- Cart Items Section -->
            <div class="section cart-section">
                <h3>Order Summary</h3>
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                            <div class="item-info">
                                <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="item-price">₱<?php echo number_format($item['price'] ?? 0, 2); ?></p>
                                <p class="item-quantity">Quantity: <?php echo htmlspecialchars($item['quantity'] ?? 1); ?></p>
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
                <div id="paypal-button-container" style="display: none;"></div>
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
                <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
            </div>
        </div>
    </div>
</form>
<script src="https://www.paypal.com/sdk/js?client-id=ASHmrHe3Otqtu4COLTbV4qGmOoTNOKMIsup17wcFFsa_1qK9k88xq5K0Ycm96jjpEhOIy3Rp_DTT4b7R&currency=USD"></script>
<script>
    const paymentMethodSelect = document.getElementById('payment-method');
    const paypalButtonContainer = document.getElementById('paypal-button-container');

    paymentMethodSelect.addEventListener('change', function () {
        if (paymentMethodSelect.value === 'paypal') {
            paypalButtonContainer.style.display = 'block';
            paypal.Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: { value: '<?php echo $totalAmount + 85; ?>' }
                        }]
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        alert('Transaction completed by ' + details.payer.name.given_name);
                    });
                }
            }).render('#paypal-button-container');
        } else {
            paypalButtonContainer.style.display = 'none';
        }
    });
</script>
</body>
</html>
