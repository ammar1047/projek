<?php
session_start();
require_once('../includes/admin.php');

// Pastikan pengguna telah login dan memiliki peran admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$admin = new Admin();
$bookings = $admin->getBookings(); // Ambil data booking dari database
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
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
                        <a class="nav-link" href="../pages/dashboard_admin.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/tambah.php">Tambah Mobil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/statistic.php">Statistik Mobil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="../admin/pesanan.php">Daftar Pesanan</a>
                    </li>
                </ul>
                <a href="../pages/logout.php" class="btn btn-warning">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center">Daftar Pesanan</h1>

        <!-- Pesan Sukses -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Tabel Daftar Pesanan -->
        <div class="table-responsive mt-4">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Mobil</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $index => $booking): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                <td><?= htmlspecialchars($booking['car_make'] . " " . $booking['car_model']) ?></td>
                                <td><?= htmlspecialchars($booking['start_date']) ?></td>
                                <td><?= htmlspecialchars($booking['end_date']) ?></td>
                                <td><?= htmlspecialchars($booking['total_price']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $booking['status'] == 'confirmed' ? 'success' : ($booking['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                        <?= htmlspecialchars(ucfirst($booking['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] == 'pending'): ?>
                                        <a href="../admin/konfirmasi.php?id=<?= $booking['id'] ?>&action=confirm" class="btn btn-success btn-sm">Konfirmasi</a>
                                        <a href="../admin/konfirmasi.php?id=<?= $booking['id'] ?>&action=cancel" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')" class="btn btn-warning btn-sm">Batalkan</a>
                                    <?php endif; ?>
                                    <a href="../admin/hapus_booking.php?id=<?= $booking['id'] ?>" onclick="return confirm('Yakin ingin menghapus pesanan ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
