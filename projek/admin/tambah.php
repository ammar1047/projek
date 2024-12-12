<?php
session_start();
include('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php'); // Redirect ke login jika bukan admin
    exit;
}

$admin = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $plate_number = $_POST['plate_number'];
    $price_per_day = $_POST['price_per_day'];
    $price_per_12_hours = $_POST['price_per_12_hours'];
    $image_url = null; // Default image URL jika tidak ada file yang diunggah

    // Proses file gambar jika ada
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../pages/assets/images/";
        $fileName = basename($_FILES['image_url']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Pindahkan file ke folder server
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $targetFilePath)) {
            $image_url = $fileName; // Simpan nama file gambar
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Menambahkan data mobil ke database
    if ($admin->addCar($make, $model, $year, $plate_number, $price_per_day, $price_per_12_hours, $image_url)) {
        header('Location: ../pages/dashboard_admin.php'); // Redirect setelah tambah
        exit;
    } else {
        echo "Failed to add car.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mobil</title>
    <link rel="stylesheet" href="../pages/css/edit.css"> <!-- Menggunakan CSS yang sama dengan edit.php -->
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Tambah Mobil</h2>
        <label>Make:</label>
        <input type="text" name="make" placeholder="Make" required><br>

        <label>Model:</label>
        <input type="text" name="model" placeholder="Model" required><br>

        <label>Year:</label>
        <input type="number" name="year" placeholder="Year" required><br>

        <label>Plate Number:</label>
        <input type="text" name="plate_number" placeholder="Plate Number" required><br>

        <label>Price Per Day:</label>
        <input type="number" name="price_per_day" step="0.01" placeholder="Price Per Day" required><br>

        <label>Price Per 12 Hours:</label>
        <input type="number" name="price_per_12_hours" step="0.01" placeholder="Price Per 12 Hours" required><br>

        <label>Image:</label>
        <input type="file" name="image_url"><br>

        <button type="submit">Tambah Mobil</button>
    </form>
</body>
</html>
