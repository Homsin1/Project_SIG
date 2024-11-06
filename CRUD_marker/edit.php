<?php
$host = 'localhost';
$username = 'root';  // Nama pengguna MySQL
$password = '';      // Password MySQL, kosong jika tidak menggunakan password
$database = 'marker'; // Nama database

$connect = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mengecek apakah parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($connect, $_GET['id']);
    $result = mysqli_query($connect, "SELECT * FROM lokasi WHERE id='$id'");
    $data = mysqli_fetch_array($result);
}

// Mengecek apakah tombol 'update' sudah di-submit
if (isset($_POST['update'])) {
    $lat_long = mysqli_real_escape_string($connect, $_POST['latlong']);
    $nama_tempat = mysqli_real_escape_string($connect, $_POST['nama_tempat']);
    $keterangan = mysqli_real_escape_string($connect, $_POST['keterangan']);
    $kategori = mysqli_real_escape_string($connect, $_POST['kategori']); 

    // Mengupdate data lokasi
    $update = mysqli_query($connect, "UPDATE lokasi SET lat_long='$lat_long', nama_tempat='$nama_tempat', keterangan='$keterangan', kategori='$kategori' WHERE id='$id'");

    if ($update) {
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Lokasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #4CAF50; /* Green color for header */
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4CAF50; /* Green color for labels */
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #4CAF50; /* Green border */
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea {
            height: 100px; /* Fixed height for textarea */
        }

        button {
            background-color: #4CAF50; /* Green button */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%; /* Full width button */
        }

        button:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <h1>Edit Lokasi</h1>
    <form action="" method="post">
        <div>
            <label>Latitude, Longitude</label>
            <input type="text" name="latlong" value="<?php echo isset($data['lat_long']) ? $data['lat_long'] : ''; ?>" required>
        </div>
        <div>
            <label>Nama Tempat</label>
            <input type="text" name="nama_tempat" value="<?php echo isset($data['nama_tempat']) ? $data['nama_tempat'] : ''; ?>" required>
        </div>
        <div>
        <div class="form-group">
        <label for="kategori">Kategori Lokasi</label>
        <select class="form-control" name="kategori" required>
            <option value="">Pilih Kategori</option>
            <option value="Restoran">Restoran</option>
            <option value="Tempat Wisata">Tempat Wisata</option>
            <option value="Toko">Toko</option>
            <option value="Sekolah">Sekolah</option>
            <option value="Rumah Sakit">Rumah Sakit</option>
            <!-- Tambahkan opsi lainnya sesuai kebutuhan -->
        </select>
    </div>
            <label>Keterangan</label>
            <textarea name="keterangan" required><?php echo isset($data['keterangan']) ? $data['keterangan'] : ''; ?></textarea>
        </div>
        <div>
            <button type="submit" name="update">Update</button>
        </div>
    </form>
</body>
</html>
