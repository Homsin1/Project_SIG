<?php
$mysqli = mysqli_connect('localhost', 'root', '', 'marker');
if (!$mysqli) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
if (!$tampil) {
    die("Query gagal: " . mysqli_error($mysqli));
}

$output = '';
while ($hasil = mysqli_fetch_array($tampil)) {
    $latlong = str_replace(['LatLng(', ')'], '', $hasil['lat_long']);
    $coordinates = explode(',', $latlong);
    $lat = trim($coordinates[0]);
    $lng = trim($coordinates[1]);
    
    $output .= '<a href="#" class="list-group-item list-group-item-action" data-lat="' . $lat . '" data-lng="' . $lng . '">';
    $output .= '<strong>' . htmlspecialchars($hasil['nama_tempat']) . '</strong><br>';
    $output .= htmlspecialchars($hasil['keterangan']);
    $output .= '</a>';
}
if (empty($output)) {
    $output = '<div class="alert alert-info">Tidak ada lokasi yang ditandai.</div>';
}

echo $output; // Pastikan output bisa dikirim ke AJAX
mysqli_close($mysqli);

