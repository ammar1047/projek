<?php
require_once 'database.php'; // Memanggil file Database

class Admin {
    private $conn; // Tambahkan properti koneksi

    public function __construct() {
        $db = new Database(); // Membuat instance dari class Database
        $this->conn = $db->getConnection(); // Menginisialisasi koneksi database
    }

    public function getCars() {
        $stmt = $this->conn->prepare("SELECT * FROM cars");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCarById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCar($make, $model, $year, $plate_number, $price_per_day, $price_per_12_hours, $image_url) {
        $stmt = $this->conn->prepare("INSERT INTO cars (make, model, year, plate_number, price_per_day, price_per_12_hours, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$make, $model, $year, $plate_number, $price_per_day, $price_per_12_hours, $image_url]);
    }

    public function editCar($id, $make, $model, $year, $plate_number, $price_per_day, $price_per_12_hours, $image_url) {
        $query = "UPDATE cars SET make = :make, model = :model, year = :year, plate_number = :plate_number, 
                  price_per_day = :price_per_day, price_per_12_hours = :price_per_12_hours, image_url = :image_url
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':make', $make);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':plate_number', $plate_number);
        $stmt->bindParam(':price_per_day', $price_per_day);
        $stmt->bindParam(':price_per_12_hours', $price_per_12_hours);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function deleteCar($id) {
        $stmt = $this->conn->prepare("DELETE FROM cars WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteBooking($id) {
        $stmt = $this->conn->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function confirmBooking($bookingId) {
        // Update status booking ke "confirmed"
        $stmt = $this->conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
        $stmt->execute([$bookingId]);

        // Ambil detail booking
        $stmt = $this->conn->prepare("SELECT car_id, total_price FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();

        // Masukkan ke tabel transactions
        $stmt = $this->conn->prepare("INSERT INTO transactions (booking_id, car_id, total_price) VALUES (?, ?, ?)");
        $stmt->execute([$bookingId, $booking['car_id'], $booking['total_price']]);
    }

    public function cancelBooking($id) {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getBookings() {
        $stmt = $this->conn->prepare("
            SELECT b.*, u.name as user_name, c.make as car_make, c.model as car_model 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN cars c ON b.car_id = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRentalStatistics($month, $year) {
        $stmt = $this->conn->prepare("
            SELECT c.make, c.model, c.plate_number, COUNT(b.id) as rental_count
            FROM bookings b
            JOIN cars c ON b.car_id = c.id
            WHERE MONTH(b.start_date) = :month AND YEAR(b.start_date) = :year
            GROUP BY c.id
            ORDER BY rental_count DESC
        ");
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyStatistics($month, $year) {
        $stmt = $this->conn->prepare("
            SELECT SUM(total_price) AS total_income 
            FROM transactions 
            WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?
        ");
        $stmt->execute([$month, $year]);
        return $stmt->fetch()['total_income'];
    }

    public function getYearlyStatistics($year) {
        $stmt = $this->conn->prepare("
            SELECT SUM(total_price) AS total_income 
            FROM transactions 
            WHERE YEAR(created_at) = ?
        ");
        $stmt->execute([$year]);
        return $stmt->fetch()['total_income'];
    }
    public function getRevenueStatistics($month, $year) {
        $stmt = $this->conn->prepare("
            SELECT 
                t.booking_id, 
                c.make as car_make, 
                c.model as car_model, 
                t.total_price, 
                t.created_at
            FROM transactions t
            JOIN cars c ON t.car_id = c.id
            WHERE MONTH(t.created_at) = :month AND YEAR(t.created_at) = :year
            ORDER BY t.created_at DESC
        ");
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
