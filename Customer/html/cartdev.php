<?php
require '../../connection/connection.php';
session_start();

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->products;
$cartsCollection = $client->GADGETHUB->carts;

$userLoggedIn = isset($_SESSION['user_id']);
$userId = $userLoggedIn ? $_SESSION['user_id'] : null;

$cartItems = $userLoggedIn ? iterator_to_array($cartsCollection->find(['user_id' => $userId])) : ($_SESSION['cart'] ?? []);

$totalPrice = 0; // Initialize total price

$selectedOptions = $_POST['select_option'] ?? [];

// Loop through the cart items and get the selected option for each item
foreach ($cartItems as $item) {
    $productId = (string)$item['product_id']['$oid'];
    $selectedOption = $selectedOptions[$productId] ?? null;

    // Do something with the selected option, e.g. store it in the database
    echo "Selected option for product $productId: $selectedOption";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../css/CartPage.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../html/navbar.php'; ?>

    <form method="POST" action="checkout.php">
        <div class="container">
            <div class="fieldnames">
                <span></span>
                <p id="productlabel">Product</p>
                <span></span>
                <p id="pricelabel">Unit Price</p>
                <p id="quantitylabel">Quantity</p>
                <p id="actionlabel">Action</p>
            </div>

            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php
                    // Extract item details
                    $productId = (string)$item['product_id']['$oid'];
                    $productName = htmlspecialchars($item['name']);
                    $productPrice = (float)$item['price'];
                    $quantity = (int)$item['quantity'];
                    $totalPrice += $productPrice * $quantity; // Calculate total price
                    ?>
                 <div class="cart-item">
    <input type="checkbox" name="selected_products[]" value="<?php echo $productId; ?>" class="product-checkbox">
    <span></span>
    <p class="product-name"><?php echo $productName; ?></p>
    <span></span>
    <p class="product-price">₱<?php echo number_format($productPrice, 2); ?></p>
    <p class="product-quantity"><?php echo $quantity; ?></p>
    <select name="select_option[]" class="select-option">
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
        <option value="option3">Option 3</option>
    </select>
    <button type="button" class="remove-item" data-product-id="<?php echo $productId; ?>">Remove</button>
</div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>

            <input type="hidden" id="selected_product_ids" name="selected_product_ids" value="">

            <div class="bottomoptions">
                <input type="checkbox" id="selectall" name="selectall" value="selectall" onclick="toggleSelectAll(this)">
                <p id="selectalllabel">Select All</p>
                <div id="selectedItemsContainer"></div>
                <p id="totalitem">Total: ₱<span id="totalvalue"><?php echo number_format($totalPrice, 2); ?></span></p>
                <button type="submit" id="add_to_cart" name="add_to_cart" <?php echo empty($cartItems) ? 'disabled' : ''; ?>>Proceed to Check Out</button>
            </div>
        </div>
    </form>

    <script>
       const selectAllCheckbox = document.getElementById('selectall');
const checkboxes = document.querySelectorAll('.product-checkbox');
const selectedItemsContainer = document.getElementById('selectedItemsContainer');

selectAllCheckbox.addEventListener('change', () => {
  const selectedProductIds = [];
  checkboxes.forEach(checkbox => {
    if (checkbox.checked) {
      selectedProductIds.push(checkbox.value);
    }
  });
  updateSelectedItems(selectedProductIds);
});

function updateSelectedItems(selectedProductIds) {
  const cartItems = document.querySelectorAll('.cart-item');
  cartItems.forEach(cartItem => {
    const productId = cartItem.querySelector('.product-checkbox').value;
    if (selectedProductIds.includes(productId)) {
      cartItem.style.display = 'block';
    } else {
      cartItem.style.display = 'none';
    }
  });
  updateTotalPrice(selectedProductIds);
}

function updateTotalPrice(selectedProductIds) {
  const totalPrice = 0;
  selectedProductIds.forEach(productId => {
    const cartItem = document.querySelector(`.cart-item [value="${productId}"]`).closest('.cart-item');
    const price = parseFloat(cartItem.querySelector('.product-price').textContent.replace('₱', ''));
    const quantity = parseInt(cartItem.querySelector('.product-quantity').textContent);
    totalPrice += price * quantity;
  });
  document.getElementById('totalvalue').textContent = totalPrice.toFixed(2);
}

        // JavaScript to handle the removal of items can be added here
    </script>
</body>
</html>