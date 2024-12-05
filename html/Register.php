<?php
require '../connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    // Check if name is set before using htmlspecialchars to prevent deprecated warning
    $name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8') : '';
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($email) || empty($name) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // MongoDB collection
        $db = $client->GADGETHUB;
        $collection = $db->users;

        // Check if user already exists
        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            echo "<script>alert('An account with this email already exists.');</script>";
        } else {
            // Insert the new user
            $result = $collection->insertOne([
                'username'  => $email,
                'email' => $email,
                'name' => $name,
                'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
                'address' => '',
                'phone' => '',
                'purchaseHistory' => [],
                'cart' => [],
                'gender' => '',
                
            ]);

            if ($result->getInsertedCount() === 1) {
                echo "<script>alert('Registration successful! Redirecting to login page.');</script>";
                header("Location: Login.php");
                exit;
            } else {
                echo "<script>alert('Failed to create account. Please try again later.');</script>";
            }
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
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <!-- Using PHP to check if name is set to pre-fill the input field -->
                    <input type="text" id="name" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-Type Password" required>
                    <p id="password-error" style="display: none; color: red;">Passwords do not match</p>
                </div>
                <script>
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm_password');
                    const passwordError = document.getElementById('password-error');
                    const submitButton = document.querySelector('button[type="submit"]');

                    confirmPassword.addEventListener('input', () => {
                        if (password.value !== confirmPassword.value) {
                            passwordError.style.display = 'block';
                            submitButton.disabled = true;
                        } else {
                            passwordError.style.display = 'none';
                            submitButton.disabled = false;
                        }
                    });
                </script>
                <button type="submit">
                    <p>SIGN-UP</p>
                </button>
                <div class="sign-up">
                    <p>Already have an account?</p>
                    <a href="Login.php">
                        <p>Log-in</p>
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
