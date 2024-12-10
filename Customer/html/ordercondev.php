<?php
// Include Composer's autoloader
require '../../connection/connection.php';

// Start the session to track the user
session_start();

// Set up MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017"); // Change to your MongoDB URI
$ordersCollection = $client->GADGETHUB->orders; // Orders collection

// Check if the user is logged in
$userLoggedIn = isset($_SESSION['user_id']);
$userId = $userLoggedIn ? $_SESSION['user_id'] : null;

// Retrieve the latest order for the logged-in user (you can add more checks here if needed)
$order = null;
if ($userLoggedIn) {
    $order = $ordersCollection->findOne(
        ['user_id' => $userId],
        ['sort' => ['order_date' => -1]] // Get the most recent order
    );
}

// If the order doesn't exist, redirect to the homepage or an error page
if (!$order) {
    header('Location: dashboard.php'); // Or show an error page
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/Confirmation.css">
    <link rel="stylesheet" href="../css/check.css">
</head>
<body>
    <?php include '../html/navbar.php'; ?>
    <div class="confirmation-container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been successfully placed.</p>
        <img src="../../assets/img/barcode.png" alt="Barcode" class="barcode">
        <div class="order-summary">
            <h2>Order #<?php echo (string) $order['_id']; ?></h2> <!-- Cast to string for proper display -->
            <p><strong>Order Date:</strong> <?php echo $order['order_date']->toDateTime()->format('Y-m-d H:i:s'); ?></p>
            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
            <div class="item-list">
                <h3>Items</h3>
                <ul>
                    <?php
                    $subtotal = 0;
                    foreach ($order['cart_items'] as $item) {
                        // Fetch the product details from MongoDB
                        $product = $client->GADGETHUB->products->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
                        $productName = $product['Name'] ?? 'No name available';
                        $productPrice = number_format($product['Price'], 2);
                        $productQuantity = $item['quantity'];

                        // Calculate subtotal for items
                        $subtotal += $product['Price'] * $productQuantity;

                        echo "<li>{$productName} x {$productQuantity} - ₱{$productPrice}</li>";
                    }
                    ?>
                </ul>

                <h3>Shipping Address</h3>
                <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>

                <h3>Payment Method</h3>
                <p><?php echo ucfirst($order['payment_method']); ?></p>

                <h3>Order Summary</h3>
                <p><strong>Subtotal:</strong> ₱<?php echo number_format($subtotal, 2); ?></p>
                <p><strong>Shipping:</strong> ₱85.00</p>
                <p><strong>Total Payment:</strong> ₱<?php echo number_format($subtotal + 85, 2); ?></p>

                <div class="actions">
                    <a href="dashboard.php" class="btn btn-primary">Back to Home</a>
                    <a href="order_history.php" class="btn btn-secondary">View Order History</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
