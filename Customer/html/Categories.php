<?php
require "../../connection/connection.php"; // MongoDB Library

// MongoDB connection

// Fetch products data
$bestsellers = $collection->find(); // Adjust query for specific conditions if needed
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="../css/DBCategories.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/navbar.css">

</head>

<body>

<div id="navbar-container"></div>
            
    <div class="container">
        <div class="Category">
            <h1 class="categoryTitle">CATEGORIES</h1>
            <div class="categorygrid">
                <div class="catitem" onclick="location.href='/html/smartphones.html'"><img src="../img/Categories/smart-phone.png"></img>
                    <p>Smartphones</p>
                </div>
                <div class="catitem" onclick="location.href='/html/laptops.php'"><img src="../img/Categories/Laptop.png"></img>
                    <p>Laptops</p>
                </div>
                <div class="catitem" onclick="location.href='/html/tablets.php'"><img src="../img/Categories/tablet.png"></img>
                    <p>Tablets</p>
                </div>
                <div class="catitem"onclick="location.href='/html/wearables.php'"><img src="../img/Categories/wareable.png"></img>
                    <p>Wearables</p>
                </div>
                <div class="catitem" onclick="location.href='/html/audio.php'"><img src="../img/Categories/audio.png"></img>
                    <p>Audio</p>
                </div>
                <div class="catitem" onclick="location.href='/html/gaming.php'"><img src="../img/Categories/gaming.png"></img>
                    <p>Gaming</p>
                </div>
                <div class="catitem" onclick="location.href='/html/camera.php'"><img src="../img/Categories/camera.png"></img>
                    <p>Cameras</p>
                </div>
                <div class="catitem" onclick="location.href='/html/homegadgets.php'"><img src="../img/Categories/home gadgets.png"></img>
                    <p>Home Gadgets</p>
                </div>
                <div class="catitem" onclick="location.href='/html/computers.php'"><img src="../img/Categories/computer.png"></img>
                    <p>Computers and Components</p>
                </div>
                <div class="catitem" onclick="location.href='/html/accesories.php'"><img src="../img/Categories/accesories.png"></img>
                    <p>Accessories</p>
                </div>

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
