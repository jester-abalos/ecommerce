<?php
session_start();
require '../../vendor/autoload.php'; // Ensure the path to autoload.php is correct

// Create a new MongoDB client instance
$client = new MongoDB\Client("mongodb://localhost:27017"); // Modify the connection string if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $fullName = htmlspecialchars($_POST["full_name"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Hash the password before storing it in the database
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Hash the password securely

        // MongoDB collection
        $db = $client->GADGETHUB; // Access the GADGETHUB database
        $collection = $db->admin; // Access the 'admin' collection

        // Check if the email already exists
        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            echo "<script>alert('Email already exists. Please use a different one.');</script>";
        } else {
            // Insert the new admin user into the database
            $newUser = [
                'full_name' => $fullName,  // Store full name
                'email' => $email,
                'passwordHash' => $passwordHash,
                'status' => 'offline' // Default status
            ];

            $collection->insertOne($newUser);

            // Redirect to login page after successful registration
            echo "<script>alert('Registration successful! You can now login.'); window.location.href = '/Admin/html/ADlogin.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | Gadget Hub</title>
    <link rel="stylesheet" href="../css/ADlogin.css"> <!-- Make sure this path is correct -->
</head>
<body>
    <!-- Navigation Bar -->
    <nav>   
        <img src="/img/LOGO.png" alt="Gadget Hub Logo">
        <span>Admin Portal</span>
    </nav>

    <!-- Main Container -->
     
    <div class="Container">
    <form action="/Admin/html/ADreg.php" method="POST">

            <h1>WELCOME TO GADGET HUB!</h1>
            <h3>Register as Admin</h3>
            
            <!-- Input Field for Full Name -->
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <!-- Submit Button -->
            <button type="submit">REGISTER</button>
            
            <!-- Link to Login -->
            <div class="create-one">
                <p>Already have an account?</p>
                <a href="ADlogin.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>
