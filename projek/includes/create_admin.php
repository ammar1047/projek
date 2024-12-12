<?php
// Pastikan Anda sudah meng-hash password dengan password_hash
$password = 'admin123'; // Password yang diinginkan
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Meng-hash password

// Menambahkan akun admin ke database
include('Database.php');
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) 
                        VALUES (:name, :email, :password, :role)");
$stmt->bindParam(':name', $name);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':role', $role);

$name = 'Admin';
$email = 'admin@example.com';
$role = 'admin'; // Role admin
$stmt->execute();

echo "Admin account created successfully!";
?>
