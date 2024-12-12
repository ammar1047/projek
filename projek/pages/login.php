<?php
session_start();
include('../includes/Database.php');
include('../includes/User.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->getConnection();
    $user = new User($conn);

    // Cek apakah email terdaftar
    $user_data = $user->getUserByEmail($email);

    if ($user_data && password_verify($password, $user_data['password'])) {
        // Set session dan login berhasil
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['user_name'] = $user_data['name'];
        $_SESSION['role'] = $user_data['role']; // Menyimpan role (admin/user)

        // Jika admin, arahkan ke dashboard admin
        if ($user_data['role'] === 'admin') {
            header("Location: dashboard_admin.php"); // Arahkan ke dashboard admin
        } else {
            header("Location: dashboard.php"); // Arahkan ke dashboard user
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <!-- Container for the login form -->
    <div class="container">
        <h1>Login</h1>

        <?php if (isset($error)) { echo "<p class='error-message'>$error</p>"; } ?>

        <!-- Login Form -->
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>
