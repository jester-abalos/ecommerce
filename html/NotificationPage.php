<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../css/NotificationPage.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/ManageProfile.css" />
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
            <img src="../img/iconaccount.png" alt="" />My Account
          </button>
          <span></span>
          <button id="myorders" onclick="location.href='orderspage.php'">
            <img src="../img/iconorder.png" alt="" />My Orders
          </button>
          <span></span>
          <button id="notifications" onclick="location.href='notificationpage.php'">
            <img src="../img/iconnotif.png" alt="" />Notifications
          </button>
          <span></span>
          <button id="logout" onclick="location.href='Logout.php'">Log Out</button>
        </div>

        <div class="notificationcontainer">

           <div class="notification1">
            <img src="../img/shoppingbag.png" alt="">
<div class="notificationinfo">
<h3>Package delivered</h3>
<p>Your package (product title) is delivered</p>
<img src="../img/cartproduct.png" alt="">
</div>
           </div>

           <div class="notification1">
            <img src="../img/shoppingbag.png" alt="">
<div class="notificationinfo">
<h3>Package delivered</h3>
<p>Your package (product title) is delivered</p>
<img src="../img/cartproduct.png" alt="">
</div>
           </div>

           <div class="notification1">
            <img src="../img/shoppingbag.png" alt="">
<div class="notificationinfo">
<h3>Package delivered</h3>
<p>Your package (product title) is delivered</p>
<img src="../img/cartproduct.png" alt="">
</div>
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
    </script>
</body>
</html>
