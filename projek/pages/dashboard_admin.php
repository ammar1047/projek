<?php
session_start();
require_once('../includes/admin.php');

// Cek apakah pengguna sudah login dan berperan sebagai admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: pages/login.php'); // Redirect ke login jika bukan admin
    exit;
}

$admin = new Admin();
$cars = $admin->getCars(); // Mengambil data mobil
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Oto Track</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard_admin.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/tambah.php">Tambah Mobil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/statistic.php">Statistik Mobil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/pesanan.php">Daftar Pesanan</a>
                    </li>
                </ul>
                <a href="logout.php" class="btn btn-warning">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center">Selamat Datang, Admin!</h1>

        <!-- Data Mobil -->
        <h2 class="mt-5">Mobil yang Tersedia</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Tahun</th>
                        <th>Plate Nomor</th>
                        <th>Harga Per Hari</th>
                        <th>Harga Per 12 Jam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= htmlspecialchars($car['id']) ?></td>
                            <td><?= htmlspecialchars($car['make']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= htmlspecialchars($car['year']) ?></td>
                            <td><?= htmlspecialchars($car['plate_number']) ?></td>
                            <td><?= htmlspecialchars($car['price_per_day']) ?></td>
                            <td><?= htmlspecialchars($car['price_per_12_hours']) ?></td>
                            <td>
                                <a href="../admin/edit.php?id=<?= htmlspecialchars($car['id']) ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="../admin/hapus.php?id=<?= htmlspecialchars($car['id']) ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
