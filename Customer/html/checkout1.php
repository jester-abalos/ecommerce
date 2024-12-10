<?php
require '../../connection/connection.php';
session_start();

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$productCollection = $client->GADGETHUB->products;

// Check user login
$userLoggedIn = isset($_SESSION['user_id']);
$userId = $userLoggedIn ? $_SESSION['user_id'] : null;
$user = null;

// Fetch user details if logged in
if ($userLoggedIn) {
    $usersCollection = $client->GADGETHUB->users;
    $user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
}

// Initialize product items
$productItems = [];
if (!$userLoggedIn && isset($_SESSION['pro'])) {
    $productItems = $_SESSION['cart'];
}
if ($userLoggedIn) {
    $cartsCollection = $client->GADGETHUB->carts;
    $productItems = iterator_to_array($cartsCollection->find(['user_id' => $userId]));
}

// Direct product purchase handling
if (isset($_GET['id']) && isset($_GET['quantity'])) {
    $productId = $_GET['id'];
    $quantity = (int)$_GET['quantity'];

    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($productId)]);
    if ($product) {
        $productItems[] = [
            'product_id' => $productId,
            'quantity' => $quantity
        ];
        if (!$userLoggedIn) {
            $_SESSION['cart'] = $productItems;
        }
    }
}

// Calculate total price
$totalAmount = 0;
foreach ($productItems as $item) {
    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
    if ($product) {
        $productPrice = isset($product['Price']) ? floatval($product['Price']) : 0;
        $totalAmount += $productPrice * $item['quantity'];
    }
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $shippingAddress = $_POST['shipping_address'];
    $paymentMethod = $_POST['payment_method'];
    $status = $_POST['status'];

    // Validate payment method
    if (!in_array($paymentMethod, ['paypal', 'cod'])) {
        die('Invalid payment method.');
    }

    $ordersCollection = $client->GADGETHUB->orders;
    $orderData = [
        'user_id' => $userId,
        'product_items' => $productItems,
        'total_price' => $totalAmount + 85,
        'shipping_address' => $shippingAddress,
        'payment_method' => $paymentMethod,
        'order_date' => new MongoDB\BSON\UTCDateTime(),
        'status' => $status,
    ];

    $insertResult = $ordersCollection->insertOne($orderData);
    $orderId = (string)$insertResult->getInsertedId();

    if ($userLoggedIn) {
        $cartsCollection->deleteMany(['user_id' => $userId]);
    } else {
        unset($_SESSION['cart']);
    }

    // Redirect based on payment method
    if ($paymentMethod === 'paypal') {
        echo "<script>
            alert('PayPal Payment Successful! Your order is marked as Shipped.');
            window.location.href = 'ordercondev.php?order_id=$orderId';
        </script>";
    } else {
        header("Location: ordercondev.php?order_id=$orderId");
        exit();
    }
}
?>

<!-- HTML for Checkout -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/check.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../html/navbar.php'; ?>
    
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your order</p>
        </div>
        <form method="POST" action="checkout.php">
            <div class="checkout-content">
                <div class="section address-section">
                    <h3>Shipping Address</h3>
                    <label for="address">Address</label>
                    <input type="text" id="address" name="shipping_address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
                </div>

                <div class="section cart-section">
                    <h3>Order Summary</h3>
                    <div class="cart-items">
                        <?php
                        if (count($productItems) == 0) {
                            echo "<p>Your cart is empty. Add some products!</p>";
                        } else {
                            foreach ($productItems as $item) {
                                $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);

                                if ($product) {
                                    $productName = isset($product['Name']) ? $product['Name'] : 'No name available';
                                    $productPrice = isset($product['Price']) ? floatval($product['Price']) : 0;
                                    $productQuantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                                    echo "
                                    <div class='cart-item'>
                                        <p>{$productName}</p>
                                        <p>₱" . number_format($productPrice, 2) . " x {$productQuantity}</p>
                                    </div>";
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="section payment-section">
                        <h3>Payment Method</h3>
                        <label>
                            <input type="radio" name="payment_method" value="cod" id="payment-cod" required>
                            Cash on Delivery
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="paypal" id="payment-paypal" required>
                            PayPal
                        </label>
                    </div>
                    <div id="paypal-button-container" style="display: none;"></div>

                    <div class="section summary-section">
                        <h3>Order Summary</h3>
                        <p>Merchandise Subtotal: ₱<?php echo number_format($totalAmount, 2); ?></p>
                        <p>Shipping: ₱85.00</p>
                        <p><strong>Total Payment: ₱<?php echo number_format($totalAmount + 85, 2); ?></strong></p>
                    </div>
                </div>

                <div class="checkout-actions">
                    <button type="submit" name="confirm_order" class="btn btn-primary">Place Order</button>
                </div>
            </div>
        </form>
    </div>
  
    <script src="https://www.paypal.com/sdk/js?client-id=ASHmrHe3Otqtu4COLTbV4qGmOoTNOKMIsup17wcFFsa_1qK9k88xq5K0Ycm96jjpEhOIy3Rp_DTT4b7R&currency=USD"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const paypalRadioButton = document.getElementById("payment-paypal");
            const codRadioButton = document.getElementById("payment-cod");
            const paypalButtonContainer = document.getElementById("paypal-button-container");
            const confirmOrderButton = document.querySelector('button[name="confirm_order"]');
            let paymentStatus = "To Pay";
            const totalAmount = <?php echo json_encode($totalAmount + 85); ?>;

            function togglePayPalButton() {
                if (paypalRadioButton.checked) {
                    paypalButtonContainer.style.display = "block";
                } else {
                    paypalButtonContainer.style.display = "none";
                    paymentStatus = "To Pay";
                }
            }

            paypalRadioButton.addEventListener("change", togglePayPalButton);
            codRadioButton.addEventListener("change", togglePayPalButton);

            paypal.Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: { value: totalAmount.toFixed(2) }
                        }]
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        paymentStatus = "To Ship";
                        alert(`Transaction completed by ${details.payer.name.given_name}.`);
                    });
                },
                onError: function (err) {
                    console.error(err);
                    alert('Something went wrong with PayPal.');
                }
            }).render('#paypal-button-container');

            confirmOrderButton.addEventListener("click", function (event) {
                const orderForm = document.querySelector('form');
                if (totalAmount <= 0) {
                    event.preventDefault();
                    alert('Your cart is empty!');
                    return;
                }

                const statusInput = document.createElement("input");
                statusInput.type = "hidden";
                statusInput.name = "status";
                statusInput.value = paymentStatus;
                orderForm.appendChild(statusInput);

                orderForm.submit();
            });
        });
    </script>

</body>
</html>

