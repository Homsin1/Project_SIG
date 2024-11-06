<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'marker');
if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize the input

    $query = "DELETE FROM lokasi WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
} else {
    echo 'error';
}

$mysqli->close();
