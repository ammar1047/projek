<?php
session_start();
include('../includes/admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("ID mobil tidak ditemukan.");
}

$admin = new Admin();
$admin->deleteCar($_GET['id']);

header('Location: ../pages/dashboard_admin.php');
exit;
?>
