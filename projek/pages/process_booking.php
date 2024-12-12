<?php
session_start();
include('../includes/Database.php');
include('../includes/Car.php');
include('../includes/Booking.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$car = new Car($conn);
$booking = new Booking($conn);

// Menerima data dari form
$car_id = isset($_POST['car_id']) ? $_POST['car_id'] : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$rental_type = isset($_POST['rental_type']) ? $_POST['rental_type'] : '';

if (!$car_id || !$start_date || !$end_date || !$rental_type) {
    echo "<script>alert('All fields are required.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
    exit();
}

// Mengambil informasi mobil
$car_data = $car->getCarById($car_id);
if (!$car_data) {
    echo "<script>alert('Car not found.'); window.location.href = 'dashboard.php';</script>";
    exit();
}

// Validasi tanggal
$start_timestamp = strtotime($start_date);
$end_timestamp = strtotime($end_date);
$current_timestamp = time();

// Tidak boleh memilih tanggal yang sudah berlalu (start_date tidak boleh lebih kecil dari hari ini)
if ($start_timestamp < $current_timestamp) {
    echo "<script>alert('Start date must be in the future.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
    exit();
}

// Batas maksimal 2 minggu dari tanggal mulai
$two_weeks_later = strtotime('+2 weeks', $start_timestamp);
if ($end_timestamp > $two_weeks_later) {
    echo "<script>alert('End date cannot exceed 2 weeks from the start date.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
    exit();
}

// Cek apakah ada konflik tanggal booking
$query = "SELECT * FROM bookings WHERE car_id = :car_id 
          AND ((start_date BETWEEN :start_date AND :end_date) 
          OR (end_date BETWEEN :start_date AND :end_date))";
$stmt = $conn->prepare($query);
$stmt->bindParam(':car_id', $car_id);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "<script>alert('Mobil ini sudah tersewa di tanggal yang anda pilih. silahkan pilih tanggal yang lain😊'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
    exit();
}

// Menghitung total harga berdasarkan tipe rental
$total_price = 0;
if ($rental_type === '12_hours') {
    // Validasi untuk rental 12 jam
    $hours_diff = ($end_timestamp - $start_timestamp) / (60 * 60);
    if ($hours_diff > 12) {
        echo "<script>alert('Duration for 12-hour rental cannot exceed 12 hours.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
        exit();
    }
    $total_price = $car_data['price_per_12_hours'];
} elseif ($rental_type === 'daily') {
    // Validasi untuk rental harian
    $days_diff = ceil(($end_timestamp - $start_timestamp) / (60 * 60 * 24));
    if ($days_diff < 1) {
        echo "<script>alert('Duration for daily rental must be at least 1 day.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
        exit();
    }
    $total_price = $car_data['price_per_day'] * $days_diff;
} else {
    echo "<script>alert('Invalid rental type.'); window.location.href = 'book_car.php?car_id={$car_id}';</script>";
    exit();
}

// Membuat pemesanan
$user_id = $_SESSION['user_id'];
$status = 'Pending';
$booking->createBooking($user_id, $car_id, $start_date, $end_date, $total_price, $status);

// Konfirmasi Pemesanan
echo "<script>
alert('Your booking has been successfully confirmed!\\n\\nDetails:\\nCar: {$car_data['make']} {$car_data['model']}\\nStart Date: {$start_date}\\nEnd Date: {$end_date}\\nTotal Price: Rp" . number_format($total_price, 2) . "');
window.location.href = 'dashboard.php';
</script>";
?>
