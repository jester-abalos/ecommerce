<?php
require '../../connection/connection.php';

// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$productCollection = $client->GADGETHUB->products;

// Fetch products data (bestsellers)
$bestsellers = $productCollection->find(); // Adjust query for specific conditions if needed
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
    <?php include '../html/navbar.php'; ?>

    <div class="container">

        <div class="discounted_Product">
            <div class="pic-ctn">
                <img src="../../assets/img/Scroll_img/xiaomi book pro 16.png" alt="Product Image" class="pic">
                <img src="../../assets/img/Scroll_img/sale.jpg" alt="Sale Image" class="pic">
                <img src="../../assets/img/Scroll_img/1.png" alt="Product Image" class="pic">
                <img src="../../assets/img/Scroll_img/2.png" alt="Product Image" class="pic">
                <img src="../../assets/img/Scroll_img/2.png" alt="Product Image" class="pic">
            </div>

            <div class="Fix_Img">
                <img src="../../assets/img/Fix_img/gre.png" alt="Green Banner">
                <img src="../../assets/img/Fix_img/Year End Gadgets.png" alt="Year End Gadgets Banner">
            </div>
        </div>

        <div class="bestsellers">
            <?php foreach ($bestsellers as $product): ?>
                <div class="bestsellersitem">
                    <a href="Productdev.php?_id=<?php echo $product['_id']; ?>">
                        <!-- Display the product image -->
                         <div class="image">
                        <img src="../../assets/products/img<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                        </div>
                        <!-- Display the product name -->
                        <h1><?php echo htmlspecialchars($product['Name']); ?></h1>

                        <!-- Display the product price -->
                        <p><?php echo '₱ ' . htmlspecialchars(number_format($product['Price'], 2)); ?></p>


                        <!-- Display the stock quantity -->
                        <p class="product-stock">
                            <?php echo $product['Stock'] . ' items in stock'; ?>
                        </p>

                        <!-- Display the discount if available -->
                        <?php if (!empty($product['discount'])): ?>
                            <p class="discount">
                                <?php echo $product['discount']['value']; ?>% OFF
                            </p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <footer id="footer" class="footer">
        <div class="about">
            <img src="../../assets/img/LOGO1.png" alt="Company Logo">
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
