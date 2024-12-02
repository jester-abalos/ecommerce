<?php
require "C:/xampp/htdocs/ecommerce/vendor/autoload.php"; // MongoDB Library

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->products;

// Fetch products data
$bestsellers = $collection->find(); // Adjust query for specific conditions if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/Dashboard.css">
    <link rel="stylesheet" href="../css/scroll.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="#home"><img src="../img/LOGO1.png" alt="Logo"></a>
        </div>
        <ul class="navbar-links" id="navbar-links">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="Categories.html">Categories</a></li>
            <li><a href="#Brands">Brands</a></li>
            <li><a href="#Order">Order</a></li>
        </ul>
        <div class="search-container">
            <input type="text" placeholder="Search..." id="search-bar">
            <span id="search-icon"><img src="../img/search.png" alt="Search"></span>
        </div>
        <div class="cart-user">
            <img src="../img/cart.png" alt="Cart">
            <span></span>
            <img src="../img/user.png" alt="User">
        </div>
        <div class="navbar-toggle" id="navbar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="container">
    
    <div class="discounted_Product">
        <div class="pic-ctn">
            <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
            <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
            <img src="../img/Scroll_img/xiaomi book pro 16.png"alt="" class="pic">
            <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
            <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
        </div>

        
        <div class="Fix_Img">
            <img src="../img/Fix_img/gre.png" alt="">
            <img src="../img/Fix_img/Year End Gadgets.png" alt="">
        </div>
    </div>
        <div class="bestsellers">
            <div class="bestsellersheader">
                <h1>BEST SELLERS</h1>
            </div>
            <div class="bestsellersgrid">
                <?php foreach ($bestsellers as $product): ?>
                    <div class="bestsellersitem">
                        <img src="<?php echo htmlspecialchars($product['image'] ?? '../img/placeholder.png'); ?>" alt="Product Image">
                        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p><?php echo '₱' . htmlspecialchars($product['price']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="about">
            <img src="../img/LOGO1.png">
            <p>"Your Ultimate Destination for Cutting-Edge Technology and Innovation, Where Every Gadget Enthusiast Can Discover, Compare, and Purchase the Latest and Greatest Tech Products, All in One Convenient Place."</p>
            <div class="footerbtn">
                <button>Home</button>
                <button>About</button>
                <button>Contact</button>
                <button>Shop</button>
            </div>
        </div>
        <div class="contactus">
            <p>Contact Us:</p>
            <button id="footerfacebook"></button>
            <button id="footerinstagram"></button>
            <button id="footertwitter"></button>
        </div>
    </footer>

    <script src="../Javascript/Dashboard.js"></script>
</body>
</html>

