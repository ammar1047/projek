<?php
session_start();
include('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

// Validasi parameter ID dan aksi
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    echo "DEBUG: Parameter yang diterima:";
    print_r($_GET); // Debug parameter yang diterima
    exit;
}

// Inisialisasi kelas Admin
$admin = new Admin();
$id = $_GET['id'];
$action = $_GET['action'];

// Logika untuk tindakan (confirm atau cancel)
if ($action === 'confirm') {
    $result = $admin->confirmBooking($id); // Konfirmasi booking
    if ($result) {
        $_SESSION['message'] = "Booking berhasil dikonfirmasi.";
    } else {
        $_SESSION['message'] = "Gagal mengkonfirmasi booking.";
    }
} elseif ($action === 'cancel') {
    $result = $admin->cancelBooking($id); // Batalkan booking
    if ($result) {
        $_SESSION['message'] = "Booking berhasil dibatalkan.";
    } else {
        $_SESSION['message'] = "Gagal membatalkan booking.";
    }
} else {
    $_SESSION['message'] = "Aksi tidak valid.";
}

// Redirect ke dashboard admin dengan pesan status
header('Location: ../pages/dashboard_admin.php');
exit;
?>
