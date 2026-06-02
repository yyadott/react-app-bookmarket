-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 02, 2026 at 02:05 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookmarket_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint NOT NULL,
  `namakategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `namakategori`) VALUES
(4, 'Pendidikan'),
(5, 'Novel'),
(7, 'Teknologi');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint NOT NULL,
  `transaksi_id` bigint DEFAULT NULL,
  `atasnama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buktibayar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `transaksi_id`, `atasnama`, `bank`, `buktibayar`, `jumlah`, `tanggal`) VALUES
(23, 31, 'Fahrul Adib', 'BCA', '1779176310_mtk-kelas-12.png', '43000', '2026-05-19 14:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` bigint NOT NULL,
  `kategori_id` bigint NOT NULL DEFAULT '0',
  `namaproduk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerbit` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dimensi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `berat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_seri` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kontak_penulis` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `kategori_id`, `namaproduk`, `deskripsi`, `harga`, `foto`, `stok`, `penerbit`, `dimensi`, `berat`, `nomor_seri`, `kontak_penulis`) VALUES
(11, 5, 'Hujan dan Kenangan', 'Kisah romantis penuh harapan dan kenangan masa lalu.', '10000', '1779162943_Buku-Antara-Kenangan-dan-Hujan.jpg', '100', NULL, NULL, NULL, NULL, NULL),
(13, 5, 'Senja di Ujung Kota', 'Novel drama tentang perjalanan hidup dan persahabatan.', '10000', '1779162868_BK118858.webp', '100', NULL, NULL, NULL, NULL, NULL),
(14, 7, 'Belajar PHP Dasar', 'Panduan dasar pemrograman PHP untuk pemula.', '10000', '1779162995_gamp4h-ol-.jpg', '100', NULL, NULL, NULL, NULL, NULL),
(15, 7, 'Mastering JavaScript', 'Pembelajaran JavaScript modern untuk pengembangan web.', '10000', '1779163049_thumbnail.webp', '100', NULL, NULL, NULL, NULL, NULL),
(16, 4, 'Matematika SMA Kelas 12', 'Buku pembelajaran matematika tingkat SMA kelas 12.', '10000', '1779163108_mtk-kelas-12.png', '98', NULL, NULL, NULL, NULL, NULL),
(17, 4, 'Bahasa Inggris Praktis', 'Panduan belajar bahasa Inggris sehari-hari.', '10000', '1779163157_Buku_Praktis_Belajar_Bahasa_Inggris_P93NxhL.jpg', '98', 'Gramedia Pustaka Utama', '20cm x 15 cm x 5cm', '500 gram', '123-456-098-4321', 'Irfan Maulana');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint NOT NULL,
  `customer_id` bigint DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nohp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `provinsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `kota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `kodepos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `kurir` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ongkir` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `grandtotal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `metodebayar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `customer_id`, `tanggal`, `deskripsi`, `nama`, `nohp`, `email`, `alamat`, `provinsi`, `kota`, `kodepos`, `kurir`, `ongkir`, `grandtotal`, `metodebayar`, `status`) VALUES
(31, 6, '2026-05-19', 'dadasdsa', 'Fahrul Adib', '082273647747', 'fahruladib9@gmail.com', 'Banyuasin', 'SUMATERA SELATAN', 'PALEMBANG', '12312', 'jne', '13000', '43000', 'Transfer', 'Diterima'),
(32, 10, '2026-05-22', '', 'fanz', '12345678908', 'fanz@gmail.com', 'Cimahi\r\n', 'DKI JAKARTA', 'JAKARTA TIMUR', '1234', 'jne', '167000', '177000', 'Tunai', 'Sudah Bayar');

-- --------------------------------------------------------

--
-- Table structure for table `transaksidetail`
--

CREATE TABLE `transaksidetail` (
  `id` bigint NOT NULL,
  `transaksi_id` bigint DEFAULT NULL,
  `produk_id` bigint DEFAULT NULL,
  `jumlah` bigint DEFAULT NULL,
  `subtotal` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksidetail`
--

INSERT INTO `transaksidetail` (`id`, `transaksi_id`, `produk_id`, `jumlah`, `subtotal`) VALUES
(40, 31, 17, 1, 10000),
(41, 31, 16, 2, 20000),
(42, 32, 17, 1, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jeniskelamin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nohp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `jeniskelamin`, `nohp`, `alamat`, `role`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$hifyQcvYskAhYOdrhEYPJ.NrmpKgaJmmZNqFHZPTAGAWeqUh5yCWq', 'Laki-laki', '082273647746', 'Cimahi', 'Admin'),
(6, 'Fahrul Adib', 'fahruladib9@gmail.com', '$2y$10$GQzIFxLJwSQOYLOHhjbPZ.4PyzOgckBmk.o2oXHyu7Bj9RcRaFUKG', 'Laki-laki', '082273647747', 'Banyuasin', 'User'),
(10, 'fanz', 'fanz@gmail.com', '$2y$10$FOz9O90Hn1d6Rl2XSp7P/eB8wEGIPevH79I/LclHCSFWdpRWygGii', 'Laki-laki', '12345678908', 'Cimahi\r\n', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksidetail`
--
ALTER TABLE `transaksidetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `transaksidetail`
--
ALTER TABLE `transaksidetail`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
