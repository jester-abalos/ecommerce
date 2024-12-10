<?php
require '../../connection/connection.php';
session_start(); // Start session to access user data

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch the user ID from the session
$userId = $_SESSION['user_id'];

// Fetch user details
$userCollection = $client->GADGETHUB->users;
$user = $userCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

if (!$user) {
    echo "<p>User not found. Please log in again.</p>";
    exit();
}

// Fetch orders for the user
$ordersCollection = $client->GADGETHUB->orders;
$orders = $ordersCollection->find(['user_id' => $userId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Page</title>
    <link rel="stylesheet" href="../css/OrdersPage.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>

<body>
    <div id="navbar-container"></div>

    <div class="container">
        <div class="menu">
            <div class="useraccount">
                <div id="profilepic"><img src="../img/profilepic.png" alt="" /></div>
                <div class="profilename"><?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <button id="myaccount" onclick="location.href='manageprofile.php'">
                <img src="../../assests/img/iconaccount.png" alt="" />My Account
            </button>
            <span></span>
            <button id="myorders" onclick="location.href='orderspage.php'">
                <img src="../../assests/img/iconorder.png" alt="" />My Orders
            </button>
            <span></span>
            <button id="notifications" onclick="location.href='notificationpage.php'">
                <img src="../../assests/img/iconnotif.png" alt="" />Notifications
            </button>
            <span></span>
            <button id="logout" onclick="location.href='Logout.php'">Log Out</button>
        </div>

        <div class="ordercontainer">
            <div class="fieldnamebox">
                <div class="fieldnames">
                    <p id="productlabel">Product</p>
                    <p id="pricelabel">Unit Price</p>
                    <p id="quantitylabel">Quantity</p>
                    <p id="totalpricelabel">Total Price</p>
                    <p id="status">Status</p>
                    <p id="actionlabel">Action</p>
                </div>
            </div>
            <div class="productlist">
                <?php foreach ($orders as $order): ?>
                    <div class="orderbox">
                        <div class="orderdetails">
                            <?php if (isset($order['cart_items']) && is_array($order['cart_items']) && count($order['cart_items']) > 0): ?>
                                <?php foreach ($order['cart_items'] as $item): ?>
                                    <div class="productbox">
                                        <div class="productdetails">
                                            <img src="<?php echo htmlspecialchars($item['image_url'] ?? '../img/cartproduct.png'); ?>" alt="">
                                            <p id="productname"><?php echo htmlspecialchars($item['name']); ?></p>
                                            <p id="pricevalue">₱<?php echo number_format($item['price'], 2); ?></p>
                                            <p id="quantityvalue"><?php echo $item['quantity']; ?></p>
                                            <p id="totalpricevalue">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                            <p id="statusvalue"><?php echo ucfirst($order['status']); ?></p>
                                            <button class="cancel" onclick="cancelOrder('<?php echo $order['_id']; ?>')">Cancel</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No items found for this order.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
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

        // Function to handle order cancellation
        function cancelOrder(orderId) {
            if (confirm("Are you sure you want to cancel this order?")) {
                fetch('cancelOrder.php', {
                    method: 'POST',
                    body: JSON.stringify({ order_id: orderId }),
                    headers: {
                        'Content-Type': 'application/json',
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload(); // Reload the page to update the order status
                    } else {
                        alert("Failed to cancel order.");
                    }
                });
            }
        }
    </script>
</body>

</html>
