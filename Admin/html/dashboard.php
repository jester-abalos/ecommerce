<?php  
require '../../connection/connection.php'; 

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $ordersCollection = $client->GADGETHUB->orders;
    
    // Fetch the orders sorted by 'created_at' in descending order
    $orders = iterator_to_array($ordersCollection->find([], [
        'sort' => ['created_at' => -1]  // -1 for descending order
    ]));  
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/navbarside.css">
</head>
<body>

<div class="Container">
    <!-- Top Navigation -->
    <nav class="nav-top">
        <div class="menu-toggle" id="menu-toggle-button">
            <img src="../image/Icons/navbarside.png" alt="Menu">
        </div>
        <div class="search-notification">
            <img src="../image/Icons/search-icon.png" alt="Search">
            <img src="../image/Icons/notifications-icon.png" alt="Notifications">
        </div>
        <div class="Admin">
            <h6>Admin <img src="../image/Icons/arrow_down-icon.png" alt="Dropdown"></h6>
        </div>
    </nav>
    <!-- Sidebar Navigation -->
    <nav class="nav-side" id="sidebar">
        <img src="../image/Logo-Admin.png" alt="Logo">
        <ul>
            <li class="active"><a href="./dashboard.php">Dashboard</a></li>
            <li><a href="./update.php">All Product</a></li>
            <li><a href="./order-list.php">Order List</a></li>
        </ul>
    </nav>

    <div class="content">
        <div class="Header-Container">
            <h1>Dashboard</h1>
            <div class="directory">
                <p>Home  >  Dashboard</p>
                <div class="calendar">
                    <img src="../image/Icons/calendar-icon.png" alt="Calendar">
                    <p>Oct 11, 2023 - Nov 11, 2023</p>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="card">
                <div class="Order">
                    <p>Total Product</p>
                    <img src="../image/Icons/3Dots-icon.png" alt="Menu">
                </div>
                <div class="Value">
                    <div class="value-num">
                        <img src="../image/Icons/basket-icon.png" alt="Basket">
                        <h1>₹126,500</h1>
                    </div>
                    <div class="value-percent">
                        <img src="../image/Icons/arrow_up-icon.png" alt="Arrow Up">
                        <p>34.7%</p>
                    </div>
                </div>
                <div class="compared">
                    <p>Compared to last month</p>
                </div>
            </div>
            
            <div class="Order-Container">
                <div class="Recent-Orders">
                    <h1>Recent Purchase</h1>
                    <img src="../image/Icons/3Dots-icon.png" alt="Menu">
                </div>
                <span></span>
                <div class="Order-Labels">
                    <p>Product</p>
                    <p id="order" >Order ID</p>
                    <p >Date</p>
                    <p id="customer">Customer name</p>
                    <p  id="status">Status</p>
                    <p id="amount">Amount</p>
                </div>
                <span></span>
                <?php 
foreach ($orders as $order): 
    // Check if 'items' exists and is iterable
    $items = isset($order['items']) && is_iterable($order['items']) ? iterator_to_array($order['items']) : [];
    // Map product names from the items array
    $products = array_map(fn($item) => $item['product_name'] ?? 'Unknown', $items);
    
    // Check if 'created_at' exists and is a valid BSONDateTime object
    $createdAt = isset($order['created_at']) && $order['created_at'] instanceof MongoDB\BSON\UTCDateTime 
                 ? $order['created_at']->toDateTime()->format('M jS, Y') 
                 : 'Unknown Date';
    
    // Check if 'total_amount' exists
    $totalAmount = isset($order['total_amount']) ? '₹' . $order['total_amount'] : '₹0';
?>
    <div class="Order-Info">
        <p>
            <?php echo implode(', ', $products); ?>
        </p>
        <p>#<?php echo $order['_id']; ?></p>
        <p><?php echo $createdAt; ?></p>
        <p><?php echo $order['delivery_address'] ?? 'Unknown'; ?></p>
        <p><?php echo ucfirst($order['status']); ?></p>
        <p><?php echo $totalAmount; ?></p>  <!-- Safely output the total amount -->
    </div>
    <span></span>
<?php endforeach; ?>


            </div>

        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('menu-toggle-button');
        const sidebar = document.getElementById('sidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    });
</script>
</body>
</html>
