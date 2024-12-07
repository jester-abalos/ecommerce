<?php
session_start();
require '../connection/connection.php'; // MongoDB connection setup

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->users;

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: Login.php");
    exit;
}

// Fetch the user from the database by their user ID
$user = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);

// Check if the user is online
if ($user['status'] !== 'online') {
    echo "<script>alert('You must be logged in as online to manage your profile.'); window.location.href='Login.php';</script>";
    exit;
}

// Safely get the gender value (if it exists, otherwise default to an empty string)
$gender = $user['gender'] ?? ''; // Default to an empty string if 'gender' doesn't exist

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phoneNumber = trim($_POST['phonenumber'] ?? '');
    $gender = trim($_POST['gender'] ?? ''); // Update gender from form submission
    $dateOfBirth = trim($_POST['dateofbirth'] ?? '');
    $newPassword = trim($_POST['newpassword'] ?? '');

    $updateData = [
        'username' => $username,
        'name' => $name,
        'address' => $address,
        'email' => $email,
        'phone_number' => $phoneNumber,
        'gender' => $gender,
        'date_of_birth' => $dateOfBirth,
    ];

    // Check if the user wants to update the password
    if (!empty($newPassword)) {
        // Only update password if it is provided (no need for old password)
        $updateData['passwordHash'] = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Update the user profile
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($userId)],
        ['$set' => $updateData]
    );

    header("Location: ManageProfile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    <link rel="stylesheet" href="../css/ManageProfile.css" />
    <link rel="stylesheet" href="../css/navbar.css" />
   
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

    <div class="settings">
        <div class="settingsheader">
            <h2>My Profile</h2>
            <p>Manage and protect your account</p>
        </div>
        <form id="profileForm" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user ['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label for="phonenumber">Phone Number:</label>
            <input type="text" id="phonenumber" name="phonenumber" value="<?php echo htmlspecialchars($user['phone_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>
            <label>Gender:</label>
            <input type="radio" id="male" name="gender" value="male" <?php echo ($user['gender'] === 'male') ? 'checked' : ''; ?>> Male
            <input type="radio" id="female" name="gender" value="female" <?php echo ($user['gender'] === 'female') ? 'checked' : ''; ?>> Female
            <br>
            <label for="dateofbirth">Date of Birth:</label>
            <input type="date" id="dateofbirth" name="dateofbirth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <br>

            <label for="newpassword">New Password</label>
            <input type="password" id="newpassword" name="newpassword" />

            <label for="confirmpassword">Confirm Password</label>
            <input type="password" id="confirmpassword" name="confirmpassword" />
            <br>

            <!-- Save Button that triggers the modal -->
            <!-- Button trigger modal -->
             
<button id="save" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
  Save
</button>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Save Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to save the changes?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('profileForm').submit()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Bootstrap JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


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
