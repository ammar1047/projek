<?php
session_start();
include('../includes/Database.php');
include('../includes/Booking.php');

if (!isset($_POST['car_id']) || !isset($_POST['start_date']) || !isset($_POST['end_date'])) {
    echo 'invalid';
    exit();
}

$car_id = $_POST['car_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$db = new Database();
$conn = $db->getConnection();
$booking = new Booking($conn);

// Cek ketersediaan
$query = "SELECT * FROM bookings WHERE car_id = :car_id 
          AND ((start_date BETWEEN :start_date AND :end_date) 
          OR (end_date BETWEEN :start_date AND :end_date)) 
          AND status NOT IN ('Cancelled', 'Confirmed')";
$stmt = $conn->prepare($query);
$stmt->bindParam(':car_id', $car_id);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo 'unavailable';
} else {
    echo 'available';
}
?>
