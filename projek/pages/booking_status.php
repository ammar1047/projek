<?php
session_start();
include('../includes/Database.php');
include('../includes/Booking.php');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$booking = new Booking($conn);

// Mendapatkan ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Mengambil daftar pemesanan berdasarkan user_id
$bookings = $booking->getBookingsByUserId($user_id);

// Proses pembatalan pemesanan jika ada request
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];

    // Cek apakah pesanan yang ingin dibatalkan masih berstatus 'pending'
    $query = "SELECT status FROM bookings WHERE id = :booking_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':booking_id', $cancel_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $booking_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking_data && $booking_data['status'] == 'pending') {
        // Memastikan bahwa pemesanan yang ingin dibatalkan milik pengguna dan statusnya 'pending'
        if ($booking->cancelBooking($cancel_id)) {
            $success_message = "Pesanan berhasil dibatalkan.";
        } else {
            $error_message = "Gagal membatalkan pesanan.";
        }
    } else {
        // Jika statusnya bukan 'pending' atau pemesanan bukan milik pengguna
        $error_message = "Pesanan tidak bisa dibatalkan (mungkin sudah dikonfirmasi atau dibatalkan).";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status - Rental Car</title>
    <link rel="stylesheet" href="css/status.css">
    
    <!-- Add JavaScript Confirmation -->
    <script>
        // Function to confirm cancelation
        function confirmCancel(url) {
            if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="brand">Oto Track</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Your Booking Status</h1>

        <!-- Menampilkan pesan sukses atau error -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="booking-list">
            <?php if ($bookings): ?>
                <?php foreach ($bookings as $booking_data): ?>
                    <div class="booking-item">
                        <h3><?php echo $booking_data['make'] . ' ' . $booking_data['model']; ?></h3>
                        <p><strong>Tanggal mulai:</strong> <?php echo date("d-m-Y H:i", strtotime($booking_data['start_date'])); ?></p>
                        <p><strong>Tanggal selesai:</strong> <?php echo date("d-m-Y H:i", strtotime($booking_data['end_date'])); ?></p>
                        <p><strong>Total Harga:</strong> Rp<?php echo number_format($booking_data['total_price'], 2); ?></p>
                        <p class="status"><strong>Status:</strong> <?php echo ucfirst($booking_data['status']); ?></p>

                        <!-- Tombol Cancel dengan konfirmasi hanya jika statusnya 'pending' -->
                        <?php if ($booking_data['status'] == 'pending'): ?>
                            <a href="javascript:void(0);" onclick="confirmCancel('booking_status.php?cancel_id=<?php echo $booking_data['id']; ?>')" class="cancel-btn">Batalkan Pesanan</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Anda Belum Mensewa mobil.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
