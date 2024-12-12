<?php
session_start();
include('../includes/Database.php');
include('../includes/Car.php');
include('../includes/Booking.php');

// Mengecek jika user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$car = new Car($conn);  
$booking = new Booking($conn);  

// Mengambil data mobil yang tersedia
$cars = $car->getAvailableCars();  

// Mengambil data pengguna dari database
$query = "SELECT name AS username, email, profile_picture FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rental Car</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <nav>
        <div class="brand">Oto Track</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="booking_status.php">Status Pesanan</a></li>
        </ul>
        <div class="profile" onclick="openModal()">
            <img src="assets/images/<?php echo $user['profile_picture']; ?>" alt="Profile Picture">
            <span><?php echo $user['username']; ?></span>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>

    <div class="container">
        <h1>Selamat Datang, <?php echo $user['username']; ?>!</h1>
        <h2>Mobil Yang Teresedia</h2>
        <div class="car-list">
            <?php if ($cars): ?>
                <?php foreach ($cars as $car): ?>
                    <div class="car-item">
                        <img src="assets/images/<?php echo $car['image_url']; ?>" alt="Car Image">
                        <h3><?php echo $car['make'] . ' ' . $car['model']; ?></h3>
                        <p><strong>Tahun:</strong> <?php echo $car['year']; ?></p>
                        <p><strong>Harga Per Hari:</strong> Rp<?php echo number_format($car['price_per_day'], 2); ?></p>
                        <p><strong>Harga 12 Jam:</strong> Rp<?php echo number_format($car['price_per_12_hours'], 2); ?></p>
                        <a href="book_car.php?car_id=<?php echo $car['id']; ?>" class="btn">Book Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No cars available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for profile details -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img src="assets/images/<?php echo $user['profile_picture']; ?>" alt="Profile Picture">
            <h2><?php echo $user['username']; ?></h2>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>
    </div>

    <script>
        // Open the modal when profile is clicked
        function openModal() {
            document.getElementById("profileModal").style.display = "block";
        }

        // Close the modal when the close button is clicked
        function closeModal() {
            document.getElementById("profileModal").style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target == document.getElementById("profileModal")) {
                document.getElementById("profileModal").style.display = "none";
            }
        }
    </script>
</body>
</html>
