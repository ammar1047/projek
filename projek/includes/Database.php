<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'rental_car';  // Nama database yang baru
    private $username = 'root';
    private $password = '';
    private $port = 3307;  // Port yang baru (3307)
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Menambahkan port dalam string koneksi
            $this->conn = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
