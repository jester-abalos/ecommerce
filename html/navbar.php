<nav class="navbar">
    <div class="navbar-logo">
        <a href="#footer"><img src="/img/LOGO1.png" alt="Logo"></a>
    </div>
    <ul class="navbar-links" id="navbar-links">
        <li><a href="Dashboard.php">Home</a></li>
        <li><a href="Categories.php">Categories</a></li>
        <li><a href="#Brands">Brands</a></li>
        <li><a href="#Order">Order</a></li>
    </ul>
    <div class="search-container">
        <input type="text" placeholder="Search..." id="search-bar">
        <span id="search-icon"><img src="/img/search.png" alt="Search"></span>
    </div>
    <div class="username">
        <p><?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div class="cart-user">
        <img src="/img/cart.png" alt="Cart">
        <span></span>
        <a href="ManageProfile.php"><img src="/img/user.png" alt="User"></a>
    </div>
    <div class="navbar-toggle" id="navbar-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>
