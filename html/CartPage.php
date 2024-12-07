<?php
require '../connection/connection.php';
session_start(); // Start session to access user data

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

if (isset($_POST['cart_id'])) {
    $cartId = $_POST['cart_id'];

    // Remove the cart item from the database
    $cartCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($cartId)]);

    // Redirect back to the cart page after removal
    header('Location: CartPage.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Page</title>
    <link rel="stylesheet" href="../css/CartPage.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../html/navbar.php'; ?>
    <div class="container">
        <div class="fieldnames">
            <span></span>
            <p id="productlabel">Product</p>
            <span></span> <span></span> <span></span>
            <p id="pricelabel">Unit Price</p>
            <p id="quantitylabel">Quantity</p>
            <p id="actionlabel">Action</p>
        </div>

        <?php
        if (!isset($_SESSION['user_id'])) {
            echo "<p>Please log in to view your cart.</p>";
        } else {
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $cartCollection = $client->GADGETHUB->carts;
            $productCollection = $client->GADGETHUB->products; // Define the product collection
            $userId = $_SESSION['user_id'];

            // Fetch cart items sorted by 'added_to_cart_at' in descending order
            $cartItems = iterator_to_array($cartCollection->find(
                ['user_id' => $userId],
                ['sort' => ['added_to_cart_at' => -1]] // Sort by 'added_to_cart_at'
            ));

            $totalAmount = 0;

            if (count($cartItems) == 0) {
                echo "<p>Your cart is empty. Add some products!</p>";
            } else {
                foreach ($cartItems as $item) {
                    // Fetch the product details based on the product_id in the cart
                    $product = $productCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);

                    // Check if the product has an image URL or use a default image
                    $productImage = (isset($product['images']) && !empty($product['images'])) 
                        ? $product['images'][0]['url'] 
                        : '../img/default-product.png';

                    echo "
                    <div class='productbox'>
                        <div class='productdetails'>
                            <span></span>
                            <input 
                                type='checkbox' 
                                id='selectitem' 
                                name='selectitem' 
                                data-price='" . $item['price'] . "' 
                                data-quantity='" . $item['quantity'] . "' 
                                onclick='updateTotal()'
                            >
                            <span></span>
                            <!-- Display Product Image -->
                            <img src='" . htmlspecialchars($productImage, ENT_QUOTES) . "' alt='" . htmlspecialchars($product['name'], ENT_QUOTES) . "' class='product-image'>
                            <p id='productname'>" . htmlspecialchars($item['name']) . "</p>
                            <p id='pricevalue'>₱" . number_format($item['price'], 2) . "</p>
                            <p id='quantity'>" . htmlspecialchars($item['quantity']) . "</p>
                            <form method='POST' action='CartPage.php'>
                                <input type='hidden' name='cart_id' value='" . $item['_id'] . "'>
                                <button type='submit' id='img' class='delete'><img src='../img/trash.png' alt=''></button>
                            </form>
                        </div>
                    </div>";
                }
            }
        }
        ?>

        <div class="bottomoptions">
            <input type="checkbox" id="selectall" name="selectall" value="selectall" onclick="toggleSelectAll(this)">
            <p id="selectalllabel">Select All</p>
            
            <p id="totalitem">Total: ₱<span id="totalvalue">0.00</span></p>
            <button id="checkout" onclick="location.href='checkoutpage.php'">Check Out</button>
        </div>
    </div>

    <script>
        // Toggle all checkboxes
        function toggleSelectAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll("input[name='selectitem']");
            let total = 0;

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;

                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.price || 0) * parseInt(checkbox.dataset.quantity || 1);
                }
            });

            const formattedTotal = total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            document.getElementById("totalvalue").innerText = formattedTotal;
        }

        // Update total dynamically based on selected items
        function updateTotal() {
            const checkboxes = document.querySelectorAll("input[name='selectitem']");
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const price = parseFloat(checkbox.dataset.price || 0);
                    const quantity = parseInt(checkbox.dataset.quantity || 1);
                    total += price * quantity;
                }
            });

            const formattedTotal = total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            document.getElementById("totalvalue").innerText = formattedTotal;
        }
    </script>
</body>
</html>
