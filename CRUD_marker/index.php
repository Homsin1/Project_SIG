<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan Membuat Peta SIG</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">SISTEM INFORMASI GEOGRAFIS</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <button class="btn btn-info ml-auto" data-toggle="modal" data-target="#locationsModal">Lihat Lokasi</button>
    <button class="btn btn-success ml-2" data-toggle="modal" data-target="#fullPageModal">Data Lokasi</button>
</nav>

<div class="row">
    <div class="col-4">
        <!-- Form untuk menambahkan lokasi baru -->
        <div class="jumbotron">
    <h2 class="text-center mt-4">Tambah Lokasi</h2>
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
            <textarea class="form-control" name="keterangan" required></textarea>
        </div>
        <div class="form-group">
            <label for="foto">Upload Foto</label>
            <input type="file" class="form-control-file" name="foto" accept="image/*" required>
        </div>
      
        <button type="submit" class="btn btn-info">Add</button>
    </form>
</div>

    </div>
    <div class="col-8">
        <!-- Search Location Feature -->
        <div class="search-container mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari lokasi...">
            <button id="searchButton" class="btn btn-primary mt-1">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>
            <div id="mapid" class="map-frame"></div>

    <div class="col-8">
        <div id="mapid" ></div>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

        <?php
        $mysqli = mysqli_connect('localhost', 'root', '', 'marker');
        if (!$mysqli) {
            die("Koneksi gagal: " . mysqli_connect_error());
        }
        $tampil = mysqli_query($mysqli, "SELECT * FROM lokasi");
        $locations = [];
        while ($hasil = mysqli_fetch_array($tampil)) {
            $locations[] = $hasil;
        }
        mysqli_close($mysqli);
        ?>

        <!-- Modal for Locations List -->
        <div class="modal fade" id="locationsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Daftar Lokasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <ul id="locasi" class="list-group">
                            <?php foreach ($locations as $location) : ?>
                                <li class="list-group-item" onclick="focusOnLocation(<?= $location['lat_long'] ?>)">
                                    <?= $location['nama_tempat'] ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Page Modal for Location Data -->
        <div class="modal fade" id="fullPageModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Data Lokasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                                        <td><img src="uploads/<?= htmlspecialchars($location['foto']) ?>" style="width: 100px;"></td>
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

        <script>
            var mymap = L.map('mapid').setView([-8.1667, 113.6925], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: 'Map data &copy; OpenStreetMap', maxZoom: 20 }).addTo(mymap);
            var geocoder = L.Control.Geocoder.nominatim();

            <?php foreach ($locations as $location): ?>
                addMarker(<?= json_encode(explode(',', str_replace(['LatLng(', ')'], '', $location['lat_long']))) ?>, <?= $location['id'] ?>, <?= json_encode($location['nama_tempat']) ?>, <?= json_encode($location['keterangan']) ?>, 'uploads/<?= $location['foto'] ?>');
            <?php endforeach; ?>

            var popup = L.popup();
            var currentLocationId;

            function focusOnLocation(lat, lng) {
                    mymap.setView([lat, lng], 15);
                    // Set the latitude and longitude into the form
                    document.getElementById('latlong').value = lat + ',' + lng; // Set coordinates in the form
                }


            function onMapClick(e) {
            popup.setLatLng(e.latlng)
                .setContent("Koordinat: " + e.latlng.toString())
                .openOn(mymap);
            document.getElementById('latlong').value = e.latlng.lat + ',' + e.latlng.lng;
            }
            mymap.on('click', onMapClick);

            function addMarker(coords, id, name, description, photoPath) {
                var marker = L.marker([parseFloat(coords[0]), parseFloat(coords[1])]).addTo(mymap);
                marker.bindPopup(
            '<div style="text-align: center;">' +
            '<b style="display: block; margin-bottom: 5px; text-align: center;">' + name + '</b>' +
            '<img src="' + photoPath + '" alt="Foto" style="width: 100px; height: auto; margin: 5px 0;">' +
            '</div>' +
            '<p style="text-align: justify;">' + description + '</p>' +
            
             '</div>')};


            function deleteLocation(id, name) {
                if (confirm('Apakah Anda yakin ingin menghapus lokasi: ' + name + '?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.responseText === 'success') {
                            alert('Lokasi berhasil dihapus');
                            location.reload();
                        } else {
                            alert('Gagal menghapus lokasi');
                        }
                    };
                    xhr.send('id=' + id);
                }
            }

            function editLocation(id) {
                window.location.href = 'edit.php?id=' + id;
            }

            var geocoder = L.Control.Geocoder.nominatim();

            document.getElementById('searchButton').addEventListener('click', function () {
            var input = document.getElementById('searchInput').value;
            geocoder.geocode(input, function (results) {
                if (results.length) {
                    var latlng = results[0].center;
                    mymap.setView(latlng, 13); 

                    document.getElementById('latlong').value = latlng.lat + ',' + latlng.lng;

                    addMarker([latlng.lat, latlng.lng], null, null, null, null);
                } else {
                    alert('Lokasi tidak ditemukan');
                }
            });
        });

        </script>
        
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
<script src="main.js"></script>
</body>
</html>
