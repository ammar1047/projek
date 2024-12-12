<?php
session_start();
require_once('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("ID booking tidak ditemukan.");
}

$admin = new Admin();
if ($admin->deleteBooking($_GET['id'])) { // Method untuk menghapus booking
    $_SESSION['message'] = "Booking berhasil dihapus.";
} else {
    $_SESSION['message'] = "Gagal menghapus booking.";
}

header('Location: ../pages/dashboard_admin.php');
exit;
?>
