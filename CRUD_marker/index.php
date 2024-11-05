<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan Membuat Peta</title>

    <!-- leaflet css -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- bootstrap cdn -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">SISTEM INFORMASI GEOGRAFIS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Button to trigger the modal for locations -->
        <button class="btn btn-info ml-auto" data-toggle="modal" data-target="#locationsModal" type="button">
            Lihat Lokasi
        </button>
        <!-- Button to trigger the full-page modal for location data -->
        <button class="btn btn-success ml-2" data-toggle="modal" data-target="#fullPageModal" type="button">
            Data Lokasi
        </button>
    </nav>

    <div class="row">
        <div class="col-4">
            <!-- Form untuk menambahkan lokasi baru -->
            <div class="jumbotron">
                <h2 class="text-center mt-4">Tambah Lokasi</h2>
                <div id="customAlert" class="custom-alert"></div>
                <hr>
                <form action="proses.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="latlong">Latitude, Longitude</label>
                        <input type="text" class="form-control" id="latlong" name="latlong" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_tempat">Nama Tempat</label>
                        <input type="text" class="form-control" name="nama_tempat" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" cols="30" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="foto">Upload Foto</label>
                        <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-8">
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari lokasi...">
                <button id="searchButton" class="btn btn-primary mt-1">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
            <div id="mapid" class="map-frame"></div>

            <!-- leaflet js -->
            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

            <script>
                var mymap = L.map('mapid').setView([-8.1667, 113.6925], 13);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                    maxZoom: 20
                }).addTo(mymap);

                var popup = L.popup();
                var currentLocationId;

                function onMapClick(e) {
                    popup.setLatLng(e.latlng)
                        .setContent("Koordinat: " + e.latlng.toString())
                        .openOn(mymap);
                    document.getElementById('latlong').value = e.latlng.lat + ',' + e.latlng.lng;
                }

                mymap.on('click', onMapClick);

                function addMarker(lat, lng, id, name, description, photoPath) {
                    var marker = L.marker([lat, lng]).addTo(mymap);
                    marker.bindPopup(
                        '<div style="text-align: center;">' +
                        '<b style="display: block; margin-bottom: 5px;">' + name + '</b>' +
                        '<img src="' + photoPath + '" alt="Foto" style="width: 100px; height: auto; margin: 5px 0;">' +
                        '<p>' + description + '</p>' +
                        '<button class="btn btn-primary" onclick="editLocation(' + id + ')">Edit</button> ' +
                        '<button class="btn btn-danger" onclick="deleteLocation(' + id + ', \'' + name + '\')">Delete</button>' +
                        '</div>'
                    ).openPopup();

                    marker.on('click', function () {
                        mymap.setView([lat, lng], 15);
                    });
                }

                var geocoder = L.Control.Geocoder.nominatim();

                document.getElementById('searchButton').addEventListener('click', function () {
                    var input = document.getElementById('searchInput').value;
                    geocoder.geocode(input, function (results) {
                        if (results.length) {
                            var latlng = results[0].center;
                            mymap.setView(latlng, 13);
                            addMarker(latlng.lat, latlng.lng);
                            // Set the latitude and longitude into the form
                            document.getElementById('latlong').value = latlng.lat + ',' + latlng.lng; // Set coordinates in the form
                        } else {
                            alert('Lokasi tidak ditemukan');
                        }
                    });
                });

                <?php
                $mysqli = mysqli_connect('localhost', 'root', '', 'marker');
                if (!$mysqli) {
                    die("Koneksi gagal: " . mysqli_connect_error());
                }

                $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
                $locations = [];
                while ($hasil = mysqli_fetch_array($tampil)) {
                    $latlong = str_replace(['LatLng(', ')'], '', $hasil['lat_long']);
                    $coordinates = explode(',', $latlong);
                    if (count($coordinates) == 2) {
                        $fotoPath = 'uploads/' . $hasil['foto'];
                        echo "addMarker(" . trim($coordinates[0]) . ", " . trim($coordinates[1]) . ", " . $hasil['id'] . ", '" . addslashes($hasil['nama_tempat']) . "', '" . addslashes($hasil['keterangan']) . "', '" . addslashes($fotoPath) . "');";
                        $locations[] = $hasil;
                    }
                }
                mysqli_close($mysqli);
                ?>

                function focusOnLocation(lat, lng) {
                    mymap.setView([lat, lng], 15);
                    // Set the latitude and longitude into the form
                    document.getElementById('latlong').value = lat + ',' + lng; // Set coordinates in the form
                }

                function editLocation(id) {
                    window.location.href = 'edit.php?id=' + id;
                }

                function deleteLocation(id, name) {
                    if (confirm('Apakah Anda yakin ingin menghapus lokasi: ' + name + '?')) {
                        // AJAX request to delete.php
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'delete.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                if (xhr.status === 200) {
                                    // Check if deletion was successful
                                    if (xhr.responseText === 'success') {
                                        alert('Lokasi berhasil dihapus');
                                        location.reload(); // Refresh the page to update the map
                                    } else {
                                        alert('Gagal menghapus lokasi. Pastikan delete.php berfungsi dengan benar.');
                                    }
                                } else {
                                    alert('Terjadi kesalahan saat menghapus data. Status: ' + xhr.status);
                                }
                            }
                        };
                        xhr.send('id=' + id); // Send the location ID to be deleted
                    }
                }

            </script>

            <!-- Modal untuk Lihat Lokasi -->
            <div class="modal fade" id="locationsModal" tabindex="-1" role="dialog" aria-labelledby="locationsModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="locationsModalLabel">Daftar Lokasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul id="locationsList" class="list-group">
                                <!-- Daftar lokasi akan dimasukkan di sini melalui PHP -->
                                <?php foreach ($locations as $location) : ?>
                                    <li class="list-group-item" onclick="focusOnLocation(<?= $location['lat_long'] ?>)">
                                        <?= $location['nama_tempat'] ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

                  <!-- Modal untuk Data Lokasi -->
                  <div class="modal fade" id="fullPageModal" tabindex="-1" role="dialog" aria-labelledby="fullPageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fullPageModalLabel">Data Lokasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Tempat</th>
                                        <th>Keterangan</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $location): ?>
                                        <tr>
                                            <td><?= $location['id'] ?></td>
                                            <td><?= htmlspecialchars($location['nama_tempat']) ?></td>
                                            <td><?= htmlspecialchars($location['keterangan']) ?></td>
                                            <td><img src="uploads/<?= htmlspecialchars($location['foto']) ?>" alt="Foto" style="width: 100px; height: auto;"></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="editLocation(<?= $location['id'] ?>)">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteLocation(<?= $location['id'] ?>, '<?= addslashes($location['nama_tempat']) ?>')">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
