<?php
session_start();
require_once('../includes/admin.php');

$admin = new Admin();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Dapatkan bulan dan tahun dari parameter GET, atau gunakan bulan dan tahun sekarang
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Ambil data statistik pendapatan dari Admin
$statistics = $admin->getRevenueStatistics($month, $year);

$totalRevenue = 0; // Inisialisasi total pendapatan
foreach ($statistics as $stat) {
    $totalRevenue += $stat['total_price']; // Hitung total pendapatan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Pendapatan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Pendapatan Bulanan</h1>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mb-3">
            <a href="../pages/dashboard_admin.php" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>

        <!-- Form Pilihan Bulan dan Tahun -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="month" class="form-label">Bulan</label>
                <select name="month" id="month" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="year" class="form-label">Tahun</label>
                <select name="year" id="year" class="form-select">
                    <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">Tampilkan</button>
            </div>
        </form>

        <!-- Total Pendapatan -->
        <div class="mb-4">
            <h3>Total Pendapatan: Rp <?= number_format($totalRevenue, 2, ',', '.') ?></h3>
        </div>

        <!-- Tabel Statistik -->
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID Booking</th>
                    <th>Mobil</th>
                    <th>Total Pendapatan (Rp)</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($statistics) > 0): ?>
                    <?php foreach ($statistics as $index => $stat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($stat['booking_id']) ?></td>
                            <td><?= htmlspecialchars($stat['car_make'] . " " . $stat['car_model']) ?></td>
                            <td><?= number_format($stat['total_price'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($stat['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data transaksi untuk bulan dan tahun ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
