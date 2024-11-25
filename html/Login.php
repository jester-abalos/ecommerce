<?php
session_start();
require "C:/xampp/htdocs/GADGETHUB/connection/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $filter = ['username' => $username];
        $db = $client->ecommerce;
        $collection = $db->users;
        $user = $collection->findOne($filter);

        if ($user) {
            // Check if the password matches
            if (password_verify($password, $user->password)) {
                $_SESSION["username"] = $username;
                header("Location: Dashboard.html");
                exit;
            } else {
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            echo "<script>alert('Username does not exist');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="../IMAGE/LOGO1.png" alt="">
        </div>
        <div class="LOGIN">
            <h1>LOG-IN</h1>
        </div>
    </nav>
    <div class="Login-bg">
        <div class="Login_Container">
            <form action="" method="POST" class="login-form">
                <img class="profile-login" src="../IMAGE/profile.png" alt="">
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder="Username / Email" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa fa-eye" id="show-pass" onclick="showPassword()"></i>
                </div>
                <div class="For-pass">
                    <p>Forgot Password</p>
                </div>
                <button type="submit"><p>LOGIN</p></button>
                <div class="or-span">
                    <span></span>
                    <p>Or</p>
                    <span></span>
                </div>
                <div class="connect-with">
                    <img src="../IMAGE/fb.png" alt="">
                    <img src="../IMAGE/google.png" alt="">
                </div>
                <div class="sign-up">
                    <p>Don't Have an Account?</p>
                    <a href="Register.html"><p>Sign-Up</p></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showPassword() {
            const passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
