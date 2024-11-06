<?php
$host = 'localhost';
$db_name = 'marker'; // Nama database yang digunakan
$username = 'root';  // Username MySQL, default biasanya 'root'
$password = '';      // Password MySQL, kosong jika Anda tidak menggunakan password

try {
    // Membuat koneksi PDO ke database MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Mengatur mode error PDO menjadi Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Jika koneksi berhasil
    echo "Connected successfully"; 
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error
    die("Connection failed: " . $e->getMessage());
}


?>
