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
                        <label for="kategori">Kategori Lokasi</label>
                        <select class="form-control" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Restoran">Restoran</option>
                            <option value="Tempat Wisata">Tempat Wisata</option>
                            <option value="Toko">Toko</option>
                            <option value="Sekolah">Sekolah</option>
                            <option value="Rumah Sakit">Rumah Sakit</option>
                        </select>
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
            <!-- Buttons for Filtering Categories -->
            <button class="btn btn-primary" onclick="filterCategory('Restoran')">Restoran</button>
            <button class="btn btn-secondary" onclick="filterCategory('Tempat Wisata')">Tempat Wisata</button>
            <button class="btn btn-success" onclick="filterCategory('Toko')">Toko</button>
            <button class="btn btn-warning" onclick="filterCategory('Sekolah')">Sekolah</button>
            <button class="btn btn-danger" onclick="filterCategory('Rumah Sakit')">Rumah Sakit</button>
            <button class="btn btn-dark" onclick="filterCategory('all')">Semua</button>

            <!-- Search Location Feature -->
            <div class="search-container mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari lokasi...">
                <button id="searchButton" class="btn btn-primary mt-1">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <div id="mapid" class="map-frame" style="height: 500px;"></div>

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

            <script>
                var mymap = L.map('mapid').setView([-8.126034756129023, 113.62198509768088], 14);
                // Menambahkan tile layer untuk tampilan peta dasar
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                    maxZoom: 20
                }).addTo(mymap);

                // Array untuk menyimpan marker dengan kategori
                var markers = [];

                // Menambahkan marker dari data lokasi
                <?php foreach ($locations as $location): ?>
                    var coords = <?= json_encode(explode(',', str_replace(['LatLng(', ')'], '', $location['lat_long']))) ?>;
                    var category = <?= json_encode($location['kategori']) ?>;
                    var name = <?= json_encode($location['nama_tempat']) ?>;
                    var description = <?= json_encode($location['keterangan']) ?>;
                    var photoPath = 'uploads/<?= $location['foto'] ?>';
                    var marker = L.marker([parseFloat(coords[0]), parseFloat(coords[1])]).addTo(mymap);
                    marker.category = category;  // Menyimpan kategori di marker
                    marker.bindPopup('<b>' + name + '</b><br>' + description + '<br><img src="' + photoPath + '" style="width: 100px; height: auto;">');
                    markers.push(marker);
                <?php endforeach; ?>

                // Fungsi untuk memfilter marker berdasarkan kategori
                function filterCategory(category) {
                    markers.forEach(marker => {
                        const shouldShow = (category === 'all' || marker.category === category);
                        if (shouldShow && !mymap.hasLayer(marker)) {
                            marker.addTo(mymap);
                        } else if (!shouldShow && mymap.hasLayer(marker)) {
                            mymap.removeLayer(marker);
                        }
                    });
                }

                // Fungsi untuk mencari lokasi berdasarkan input pengguna
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

                // Menambahkan marker baru ke peta
                function addMarker(latlng, name, category, description, photoPath) {
                    var marker = L.marker(latlng).addTo(mymap);
                    marker.bindPopup('<b>' + name + '</b><br>' + description + '<br><img src="' + photoPath + '" style="width: 100px; height: auto;">');
                }

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

                // Fungsi untuk fokus ke lokasi tertentu berdasarkan item yang dipilih
                function focusOnLocation(element) {
                    var lat = element.getAttribute('data-lat');
                    var lng = element.getAttribute('data-lng');

                    // Memusatkan peta pada koordinat yang diberikan
                    var latlng = [parseFloat(lat), parseFloat(lng)];
                    mymap.setView(latlng, 14);  // Peta akan fokus pada koordinat ini
                    mymap.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            // Cek apakah marker berada di lokasi ini dan tampilkan popup-nya
                            if (layer.getLatLng().equals(latlng)) {
                                layer.openPopup();  // Membuka popup marker yang sesuai
                            }
                        }
                    });
                }
            </script>
        </div>
    </div>

    <!-- Modal for Locations List -->
    <div class="modal fade" id="locationsModal" tabindex="-1" role="dialog" aria-labelledby="locationsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationsModalLabel">Daftar Lokasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php foreach ($locations as $location): ?>
                            <?php
                            $coords = explode(',', str_replace(['LatLng(', ')'], '', $location['lat_long']));
                            $lat = $coords[0];
                            $lng = $coords[1];
                            ?>
                            <li class="list-group-item" data-lat="<?= $lat ?>" data-lng="<?= $lng ?>"
                                onclick="focusOnLocation(this)">
                                <?= $location['nama_tempat'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Full Page Location Data -->
    <div class="modal fade" id="fullPageModal" tabindex="-1" role="dialog" aria-labelledby="fullPageModalLabel"
        aria-hidden="true">
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
                                <th>Kategori</th>
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
                                    <td><?= htmlspecialchars($location['kategori']) ?></td>
                                    <td><?= htmlspecialchars($location['keterangan']) ?></td>
                                    <td><img src="uploads/<?= htmlspecialchars($location['foto']) ?>" style="width: 100px;">
                                    </td>
                                    <td>
                                        <button class="btn btn-primary"
                                            onclick="editLocation(<?= $location['id'] ?>)">Edit</button>
                                        <button class="btn btn-danger"
                                            onclick="deleteLocation(<?= $location['id'] ?>, '<?= addslashes($location['nama_tempat']) ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, jQuery, and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>

</body>

</html>