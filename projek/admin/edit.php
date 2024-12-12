<?php
session_start();
include('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php'); // Redirect ke login jika bukan admin
    exit;
}

$admin = new Admin();
$id = $_GET['id'] ?? null; // Mendapatkan ID dari query string
$car = $admin->getCarById($id); // Mendapatkan data mobil berdasarkan ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $plate_number = $_POST['plate_number'];
    $price_per_day = $_POST['price_per_day'];
    $price_per_12_hours = $_POST['price_per_12_hours'];
    $image_url = $car['image_url']; // Default ke URL gambar lama jika tidak ada upload baru

    // Proses file gambar jika ada
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../pages/assets/images/";
        $fileName = basename($_FILES['image_url']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Pindahkan file ke folder server
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $targetFilePath)) {
            $image_url = $fileName; // Simpan path relatif gambar
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Memperbarui data mobil
    $admin->editCar($id, $make, $model, $year, $plate_number, $price_per_day, $price_per_12_hours, $image_url);
    header('Location: ../pages/dashboard_admin.php'); // Redirect setelah update
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mobil</title>
    <link rel="stylesheet" href="../pages/css/edit.css">
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Edit Mobil</h2>
        <label>Make:</label>
        <input type="text" name="make" value="<?= $car['make'] ?>" required><br>

        <label>Model:</label>
        <input type="text" name="model" value="<?= $car['model'] ?>" required><br>

        <label>Year:</label>
        <input type="number" name="year" value="<?= $car['year'] ?>" required><br>

        <label>Plate Number:</label>
        <input type="text" name="plate_number" value="<?= $car['plate_number'] ?>" required><br>

        <label>Price Per Day:</label>
        <input type="number" name="price_per_day" step="0.01" value="<?= $car['price_per_day'] ?>" required><br>

        <label>Price Per 12 Hours:</label>
        <input type="number" name="price_per_12_hours" step="0.01" value="<?= $car['price_per_12_hours'] ?>" required><br>

        <label>Image:</label>
        <input type="file" name="image_url"><br>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
