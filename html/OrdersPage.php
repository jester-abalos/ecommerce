<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Page</title>
    <link rel="stylesheet" href="../css/OrdersPage.css">
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

        <div class="ordercontainer">
            <div class="fieldnamebox">
                <div class="fieldnames">
                    <span></span>
                    <p id="productlabel">Product</p>
                    <span></span>  <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
                    <p id="pricelabel">Unit Price</p>
                    <p id="quantitylabel">Quantity</p>
                    <p id="totalpricelabel">Total Price</p>
                    <p id="status">Status</p>
                    <p id="actionlabel">Action</p>
                </div>
            </div>
            <div class="productlist">


                <div class="productbox">
                    <div class="productdetails">
                        <span></span> <span></span> <span></span> 
                        <img src="../img/cartproduct.png" alt=""> <span></span> <span></span> 
                        <p id="productname">PRODUCT NAME</p> <span></span> <span></span> 
                        <p id="variationvalue">Variations</p> <span></span> <span></span> 
                        <p id="pricevalue">P100</p>
                        <p id="quantityvalue">1</p>
                        <p id="totalpricevalue">P100</p>
                        <p id="statusvalue">Pending</p>
                        <span></span> 
                        <button class="cancel">Cancel</button>
                    </div>
                </div>

                <div class="productbox">
                    <div class="productdetails">
                        <span></span> <span></span> <span></span> 
                        <img src="../img/cartproduct.png" alt=""> <span></span> <span></span> 
                        <p id="productname">PRODUCT NAME</p> <span></span> <span></span> 
                        <p id="variationvalue">Variations</p> <span></span> <span></span> 
                        <p id="pricevalue">P100</p>
                        <p id="quantityvalue">1</p>
                        <p id="totalpricevalue">P100</p>
                        <p id="statusvalue">Pending</p>
                        <span></span> 
                        <button class="cancel">Cancel</button>
                    </div>
                </div>

                <div class="productbox">
                    <div class="productdetails">
                        <span></span> <span></span> <span></span> 
                        <img src="../img/cartproduct.png" alt=""> <span></span> <span></span> 
                        <p id="productname">PRODUCT NAME</p> <span></span> <span></span> 
                        <p id="variationvalue">Variations</p> <span></span> <span></span> 
                        <p id="pricevalue">P100</p>
                        <p id="quantityvalue">1</p>
                        <p id="totalpricevalue">P100</p>
                        <p id="statusvalue">Pending</p>
                        <span></span> 
                        <button class="cancel">Cancel</button>
                    </div>
                </div>

            </div>

        </div>



    </div>

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

