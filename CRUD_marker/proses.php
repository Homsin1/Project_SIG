<?php
$mysqli = new mysqli("localhost", "root", "", "marker");

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

// Ambil data dari form
$latlong = $_POST['latlong'];
$nama_tempat = $_POST['nama_tempat'];
$keterangan = $_POST['keterangan'];
$foto = $_FILES['foto']['name'];

// Upload foto
$target_dir = "uploads/";
$target_file = $target_dir . basename($foto);
move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);

// Query untuk memasukkan data
$query = "INSERT INTO lokasi (lat_long, nama_tempat, keterangan, foto) VALUES ('$latlong', '$nama_tempat', '$keterangan', '$foto')";

if ($mysqli->query($query) === TRUE) {
    echo "Data lokasi berhasil ditambahkan.";
} else {
    echo "Error: " . $query . "<br>" . $mysqli->error;
}

$mysqli->close();
header("Location: index.php"); // Redirect kembali ke halaman utama setelah berhasil
?>
