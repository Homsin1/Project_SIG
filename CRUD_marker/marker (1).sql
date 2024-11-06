-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2024 at 06:42 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marker`
--

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int NOT NULL,
  `lat_long` varchar(50) NOT NULL,
  `nama_tempat` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `lat_long`, `nama_tempat`, `keterangan`, `foto`, `kategori`) VALUES
(58, '-8.112965717626022,113.59085496038865', 'Wisata Kampung Durian', 'Wisata Kampung Durian Panti di Daerah Jember', 'img-20221205-050904-54d33970c0f6c84022d1c6917fe4fe7f-0b3628a02f4ccbefa68a83de4cf3af8a.jpg', 'Tempat Wisata'),
(59, '-8.117643277927211,113.61334443917585', 'Argowisata Sentool', 'Argo Wisata Perkebunan Sentol Jember', 'WhatsApp Image 2022-05-14 at 22.59.04.jpeg', 'Tempat Wisata'),
(60, '-8.101602,113.6280566', 'Argo Wisata Gunung Pasang', 'Tempat wisata ini hadir dengan menyajikan wisata alam yang indah dilengkapi berbagai kegiatan wisata yang seru', 'IMG_20180218_101614_HDR.jpg', ''),
(61, '-8.1163912,113.6364882', 'Kampung Kemiri', 'Kafe dengan nuansa alam', 'hq2.jpg', ''),
(62, '-8.11621087667744,113.63234984353582', 'Desa Wisata Kemiri', 'Sebuah destinasi wisata desa yang teretak di lereng gunung argopuro', 'desa-wisata-kemiri-jember-tehu-dom.jpg', ''),
(63, '-8.1323562,113.5570908', 'Wisata Argo Sumber Pelangi', 'Agrowisata dengan nuansa pemandangan alam dan kebun', '2023-06-29.jpg', ''),
(69, '-8.138769279181108,113.59747199460278', 'Desa Kantor Pakis', 'Desa Kantor Pakis', 'WhatsApp Image 2019-02-13 at 19.28.20.jpeg', ''),
(70, '-8.1071745,113.6353026', 'Viewpoint Boma Gunung Pasang', 'Viwpoint', '2023-10-15.jpg', ''),
(71, '-8.117543130689654,113.63610492428529', 'Pasar Kemiri', 'Pasar Kemiri Jember', 'img-20230222-wa0001-63f5e1384addee2e2d1eeb52.jpg', ''),
(72, '-8.1256497,113.6343229', 'Rimba Camp', 'Kampung Rimba Camp', 'tempat-camping-di-puncak_banner.jpg', ''),
(73, '-8.1256497,113.6343229', 'Pabrik Kopi Gunung Pasang', 'Pabrik Kopi Gunung Pasang', 'kopi-nasional--270717-1.jpg', ''),
(74, '-8.1692325,113.6323278', 'GRAND RESIDENCE PANTI', 'Perumahan Residence Panti', 'unnamed.jpg', ''),
(75, '-8.179991218909752,113.63106805994225', 'Banyumili Resto', 'Salah Satu Resto di daerah Kecamatan Panti', 'OIP.jpg', ''),
(76, '-8.150658485219342,113.62134347952377', 'SDN Panti 03', 'SDN 03 Panti', '2021-08-18.jpg', ''),
(77, '-8.11278256125766,113.63644838660919', 'JCC (Jember Coffee Centre)', 'Cafe Coffe Centre di Kecmatan Panti Jember', '2021-04-10.jpg', ''),
(80, '-7.9771308,112.6340265', 'malang', 'malang', 'unnamed.jpg', 'Restoran'),
(81, '-7.9771308,112.6340265', 'malang', 'zaXAASXAS', 'OIP.jpg', 'Tempat Wisata');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
