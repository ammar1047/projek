<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Menyimpan pemesanan
    public function createBooking($user_id, $car_id, $start_date, $end_date, $total_price, $status) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, car_id, start_date, end_date, total_price, status) 
                  VALUES (:user_id, :car_id, :start_date, :end_date, :total_price, :status)";
        
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':car_id', $car_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':status', $status);

        // Eksekusi query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Mendapatkan daftar pemesanan berdasarkan user_id
    public function getBookingsByUserId($user_id) {
        $query = "
            SELECT b.id, b.start_date, b.end_date, b.total_price, b.status, c.make, c.model
            FROM " . $this->table_name . " b
            JOIN cars c ON b.car_id = c.id
            WHERE b.user_id = :user_id
            ORDER BY b.start_date DESC
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Eksekusi query
        if ($stmt->execute()) {
            // Mengambil semua hasil query
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Jika query gagal
            return false;
        }
    }

    // Update status booking menjadi 'canceled'
    public function cancelBooking($booking_id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'cancelled' WHERE id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);

        // Eksekusi query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
