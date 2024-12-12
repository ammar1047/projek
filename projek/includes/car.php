<?php
class Car {
    private $conn;
    private $table_name = "cars";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mengambil mobil yang tersedia
    public function getAvailableCars() {
        // Query untuk memilih semua mobil yang tersedia beserta harga per hari dan per 12 jam
        $query = "SELECT id, make, model, year, price_per_day, price_per_12_hours, image_url FROM " . $this->table_name . " WHERE available = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Mengembalikan hasil dalam bentuk array
    }

    // Mengambil mobil berdasarkan ID
    public function getCarById($id) {
        $query = "SELECT id, make, model, year, price_per_day, price_per_12_hours, image_url FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Mengembalikan hasil dalam bentuk array
    }
}
?>
