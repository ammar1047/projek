<?php
session_start();
include('../includes/Database.php');
include('../includes/Car.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$car = new Car($conn);

// Mengambil ID mobil yang dipilih
$car_id = isset($_GET['car_id']) ? $_GET['car_id'] : 0;
$car_data = $car->getCarById($car_id);

if (!$car_data) {
    echo "Car not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car - Rental Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk menghitung harga
        function calculatePrice() {
            const rentalType = document.getElementById('rental_type').value;
            const pricePerDay = <?php echo $car_data['price_per_day']; ?>;
            const pricePer12Hours = <?php echo $car_data['price_per_12_hours']; ?>;

            let totalPrice = 0;
            if (rentalType === '12_hours') {
                const startDate = new Date(document.getElementById('start_date').value);
                const endDate = new Date(document.getElementById('end_date').value);
                const hours = (endDate - startDate) / (1000 * 60 * 60);

                if (hours > 12) {
                    alert("Durasi sewa 12 jam tidak boleh melebihi 12 jam.");
                    document.getElementById('end_date').value = '';
                    return;
                }
                totalPrice = pricePer12Hours;
            } else if (rentalType === 'daily') {
                const startDate = new Date(document.getElementById('start_date').value);
                const endDate = new Date(document.getElementById('end_date').value);
                const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

                if (days < 1) {
                    alert("Durasi sewa harian harus minimal 1 hari.");
                    document.getElementById('end_date').value = '';
                    return;
                }
                totalPrice = days * pricePerDay;
            }

            document.getElementById('total_price').value = totalPrice.toFixed(2);
        }

        // Fungsi untuk mengatur batasan tanggal selesai (maksimal 2 minggu)
        function setEndDateLimit() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const startDate = new Date(startDateInput.value);

            if (startDate) {
                const maxDate = new Date(startDate.getTime() + 14 * 24 * 60 * 60 * 1000); // Maksimal 2 minggu
                endDateInput.min = startDate.toISOString().slice(0, 16);
                endDateInput.max = maxDate.toISOString().slice(0, 16);
                endDateInput.value = ''; // Reset nilai end_date
            }
        }

        // Pemberitahuan jika mobil sudah dipesan pada tanggal yang sama
        function checkAvailability() {
            const carId = <?php echo $car_id; ?>;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Lakukan pemeriksaan ketersediaan mobil
            if (startDate && endDate) {
                const request = new XMLHttpRequest();
                request.open('POST', 'check_availability.php', true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                request.onload = function() {
                    if (this.responseText === 'unavailable') {
                        alert("Mobil ini sudah dipesan pada tanggal yang Anda pilih. Silakan pilih tanggal lain.");
                    }
                };
                request.send('car_id=' + carId + '&start_date=' + startDate + '&end_date=' + endDate);
            }
        }

        // Fungsi validasi sebelum form disubmit
        function validateForm() {
            const startDate = new Date(document.getElementById('start_date').value);
            const currentDate = new Date();

            if (startDate < currentDate) {
                alert("Tanggal mulai tidak boleh di tanggal yang sudah terlewat atau sebelumnya tanggal sekarang.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Oto Track</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking_status.php">Status Pesanan</a></li>
                </ul>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Sewa Mobil</h1>

        <div class="row">
            <div class="col-md-6">
                <img src="assets/images/<?php echo $car_data['image_url']; ?>" class="img-fluid rounded" alt="Car Image">
            </div>
            <div class="col-md-6">
                <h2><?php echo $car_data['make'] . ' ' . $car_data['model']; ?></h2>
                <p><strong>Tahun:</strong> <?php echo $car_data['year']; ?></p>
                <p><strong>Harga Per Hari:</strong> Rp<?php echo number_format($car_data['price_per_day'], 2); ?></p>
                <p><strong>Harga Sewa 12 Jam:</strong> Rp<?php echo number_format($car_data['price_per_12_hours'], 2); ?></p>
                <p><strong>Keterangan:</strong></p>
                <ul>
                    <li>Pengambilan dilakukan <strong>1 jam sebelum waktu mulai</strong>.</li>
                    <li>Pengembalian harus dilakukan <strong>sebelum waktu selesai</strong>, tidak boleh telat.</li>
                </ul>
            </div>
        </div>

        <div class="mt-5">
            <h3>Silahkan Isi Formulir Sewa</h3>
            <form action="process_booking.php" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="car_id" value="<?php echo $car_data['id']; ?>">

                <div class="mb-3">
                    <label for="rental_type" class="form-label">Tipe Rental</label>
                    <select id="rental_type" name="rental_type" class="form-select" onchange="calculatePrice()" required>
                        <option value="">Pilih Tipe Rental</option>
                        <option value="12_hours">12 Jam</option>
                        <option value="daily">Per Hari</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Tanggal & Waktu Mulai</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-control" onchange="setEndDateLimit(); calculatePrice();" required>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Tanggal & Waktu Selesai</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" onchange="calculatePrice(); checkAvailability();" required>
                </div>

                <div class="mb-3">
                    <label for="total_price" class="form-label">Total Harga (Rp)</label>
                    <input type="text" id="total_price" name="total_price" class="form-control" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>
        </div>
    </div>
</body>
</html>
