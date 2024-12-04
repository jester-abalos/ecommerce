<?php
require '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if user exists
    $existingUser = $db->findOne(['username' => $email]);

    if ($existingUser) {
        echo "<script>alert('Account already exists');</script>";
    } else {
        // Insert the new user
        $result = $db->insertOne([
            'username' => $email,
            'name' => $name,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if ($result->getInsertedCount() === 1) {
            header("Location: Login.php");
            exit;
        } else {
            echo "<script>alert('Failed to create account');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="../img/LOGO1.png" alt="">
        </div>
        <div class="LOGIN">
            <h1>REGISTER</h1>
        </div>
    </nav>
    <div class="Login-bg">
        <div class="Login_Container">
            <form action="" method="POST" class="login-form">
                <img class="profile-login" src="../img/profile.png" alt="">
                <div class="form-group">
                    <input type="email" id="username" name="email" placeholder="Username / Email" required>
                </div>
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa fa-eye" id="show-pass" onclick="showPassword()"></i>
                </div>
                <div class="form-group">
                    <input type="password" id="retypepassword" name="confirm_password" placeholder="Re-Type Password"
                        required>
                    <i class="fa fa-eye" id="show-pass" onclick="showPassword()"></i>
                    <p id="incorrect-password" style="display: none; color: red;">Passwords do not match</p>
                </div>
                <script>
                    const password = document.getElementById('password');
                    const retypepassword = document.getElementById('retypepassword');
                    const incorrectPassword = document.getElementById('incorrect-password');
                    const submitButton = document.querySelector('button[type="submit"]');

                    retypepassword.addEventListener('input', () => {
                        if (password.value !== retypepassword.value) {
                            incorrectPassword.style.display = 'block';
                            submitButton.disabled = true;
                        } else {
                            incorrectPassword.style.display = 'none';
                            submitButton.disabled = false;
                        }
                    });
                </script>
                <button type="submit">
                    <p>SIGN-UP</p>
                </button>
                <div class="sign-up">
                    <p>Don't Have an Account?</p>
                    <a href="Login.php">
                        <p>Log-in</p>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>