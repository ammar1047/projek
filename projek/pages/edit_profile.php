<?php
session_start();
include('../includes/Database.php');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$db = new Database();
$conn = $db->getConnection();

// Ambil data pengguna dari database
$query = "SELECT name, email, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Proses update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $profile_picture = $user['profile_picture']; // Default to existing picture

    // Jika ada file yang diunggah
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../pages/assets/images/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = basename($_FILES['profile_picture']['name']);
        } else {
            $error_message = "Failed to upload profile picture.";
        }
    }

    // Update data ke database
    $update_query = "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->execute([$name, $email, $profile_picture, $_SESSION['user_id']]);

    // Redirect jika sukses
    if ($update_stmt->rowCount() > 0) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "No changes were made.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
            <img src="../assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
