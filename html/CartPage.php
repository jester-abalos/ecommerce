<?php
require '../connection/connection.php';
session_start();  // Start session to access user data

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit();
}

if (isset($_POST['cart_id'])) {
    $cartId = $_POST['cart_id'];

    // MongoDB connection
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $cartCollection = $client->GADGETHUB->carts;

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
</head>

<body>
    <div id="navbar-container"></div>
    <div class="container">
        <div class="fieldnames">
            <span></span>
            <p id="productlabel">Product</p>
            <span></span> <span></span> <span></span>
            <p id="pricelabel">Unit Price</p>
            <p id="quantitylabel">Quantity</p>
            <p id="actionlabel">Action</p>
        </div>

        <!-- Product list container -->
        <?php
        // Assuming session is started and user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo "<p>Please log in to view your cart.</p>";
        } else {
            // MongoDB connection and fetching user's cart items
            $client = new MongoDB\Client("mongodb://localhost:27017");
            $cartCollection = $client->GADGETHUB->carts;

            $userId = $_SESSION['user_id'];
            $cartItems = iterator_to_array($cartCollection->find(['user_id' => $userId]));

            if (count($cartItems) == 0) {
                echo "<p>Your cart is empty. Add some products!</p>";
            } else {
                // Loop through cart items and display each product
                foreach ($cartItems as $item) {
                    $totalPrice = $item['price'] * $item['quantity'];
                    echo "
                    <div class='productbox'>
                        <div class='productdetails'>
                            <span></span>
                            <input type='checkbox' id='selectitem' name='selectitem' value='selectitem'><span></span>
                            <img src='../img/cartproduct.png' alt=''>
                            <p id='productname'>" . htmlspecialchars($item['name']) . "</p>
                            <p id='pricevalue'>₱" . number_format($item['price'], 2) . "</p>
                            <div class='quantity'>
                                <button onclick='updateQuantity(\"" . $item['_id'] . "\", \"increase\")'>+</button>
                                <p>" . $item['quantity'] . "</p>
                                <button onclick='updateQuantity(\"" . $item['_id'] . "\", \"decrease\")'>-</button>
                            </div>
                            <span></span>
                            <form method='POST' action='CartPage.php'>
                                <input type='hidden' name='cart_id' value='" . $item['_id'] . "'>
                                <button type='submit' class='delete'><img src='../img/trash.png' alt=''>Delete</button>
                            </form>
                        </div>
                    </div>";
                }

                // Calculate the total price
                $totalAmount = 0;
                foreach ($cartItems as $item) {
                    $totalAmount += $item['price'] * $item['quantity'];
                }
                echo "<p id='totalitem'>Total: ₱" . number_format($totalAmount, 2) . "</p>";
            }
        }
        ?>
    </div>

    <div class="bottomoptions">
        <input type="checkbox" id="selectall" name="selectall" value="selectall">
        <p id="selectalllabel">Select All</p>
        <button id="deleteall"> <img src="../img/trash.png" alt="">Delete</button>

        <button id="checkout" onclick="location.href='checkoutpage.php'">Check Out</button>
    </div>

    <script>
        window.onload = function() {
            fetch('navbar.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('navbar-container').innerHTML = data;
                });
        };

        // Update cart quantity
        function updateQuantity(cartId, action) {
            fetch('updateCartQuantity.php', {
                method: 'POST',
                body: JSON.stringify({ cartId: cartId, action: action }),
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();  // Reload the page to reflect updated quantities
                } else {
                    alert('Failed to update quantity');
                }
            });
        }
    </script>
</body>

</html>

