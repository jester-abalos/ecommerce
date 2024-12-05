<?php
require '../connection/connection.php';

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
    <link rel="stylesheet" href="../css/footer.css">

</head>

<body>
<div id="navbar-container"></div>

    <div class="container">

        <div class="discounted_Product">
            <div class="pic-ctn">
                <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
                <img src="../img/Scroll_img/sale.jpg" alt="" class="pic">
                <img src="../img/Scroll_img/xiaomi book pro 16.png" alt="" class="pic">
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
                        <a href="ProductDetails.php?id=<?php echo $product['_id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image'] ?? '../img/placeholder.png'); ?>"
                                alt="Product Image">
                            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                            <p><?php echo '₱' . htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        </a>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="about">
            <img src="../img/LOGO1.png">
            <p>"Your Ultimate Destination for Cutting-Edge Technology and Innovation, Where Every Gadget Enthusiast Can
                Discover, Compare, and Purchase the Latest and Greatest Tech Products, All in One Convenient Place."</p>
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
    <script>
        // JavaScript to load the external navbar HTML
        window.onload = function() {
            fetch('navbar.html')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                });
        };
    </script>
</body>

</html>