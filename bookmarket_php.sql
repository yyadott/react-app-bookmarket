-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table bookmarket_php.kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `namakategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.kategori: ~3 rows (approximately)
DELETE FROM `kategori`;
INSERT INTO `kategori` (`id`, `namakategori`) VALUES
	(4, 'Pendidikan'),
	(5, 'Novel'),
	(7, 'Teknologi');

-- Dumping structure for table bookmarket_php.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint DEFAULT NULL,
  `atasnama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buktibayar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `jumlah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.pembayaran: ~0 rows (approximately)
DELETE FROM `pembayaran`;
INSERT INTO `pembayaran` (`id`, `transaksi_id`, `atasnama`, `bank`, `buktibayar`, `jumlah`, `tanggal`) VALUES
	(23, 31, 'Fahrul Adib', 'BCA', '1779176310_mtk-kelas-12.png', '43000', '2026-05-19 14:38:30');

-- Dumping structure for table bookmarket_php.produk
CREATE TABLE IF NOT EXISTS `produk` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint NOT NULL DEFAULT '0',
  `namaproduk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `harga` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stok` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.produk: ~6 rows (approximately)
DELETE FROM `produk`;
INSERT INTO `produk` (`id`, `kategori_id`, `namaproduk`, `deskripsi`, `harga`, `foto`, `stok`) VALUES
	(11, 5, 'Hujan dan Kenangan', 'Kisah romantis penuh harapan dan kenangan masa lalu.', '10000', '1779162943_Buku-Antara-Kenangan-dan-Hujan.jpg', '100'),
	(13, 5, 'Senja di Ujung Kota', 'Novel drama tentang perjalanan hidup dan persahabatan.', '10000', '1779162868_BK118858.webp', '100'),
	(14, 7, 'Belajar PHP Dasar', 'Panduan dasar pemrograman PHP untuk pemula.', '10000', '1779162995_gamp4h-ol-.jpg', '100'),
	(15, 7, 'Mastering JavaScript', 'Pembelajaran JavaScript modern untuk pengembangan web.', '10000', '1779163049_thumbnail.webp', '100'),
	(16, 4, 'Matematika SMA Kelas 12', 'Buku pembelajaran matematika tingkat SMA kelas 12.', '10000', '1779163108_mtk-kelas-12.png', '98'),
	(17, 4, 'Bahasa Inggris Praktis', 'Panduan belajar bahasa Inggris sehari-hari.', '10000', '1779163157_Buku_Praktis_Belajar_Bahasa_Inggris_P93NxhL.jpg', '98');

-- Dumping structure for table bookmarket_php.transaksi
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
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
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.transaksi: ~1 rows (approximately)
DELETE FROM `transaksi`;
INSERT INTO `transaksi` (`id`, `customer_id`, `tanggal`, `deskripsi`, `nama`, `nohp`, `email`, `alamat`, `provinsi`, `kota`, `kodepos`, `kurir`, `ongkir`, `grandtotal`, `metodebayar`, `status`) VALUES
	(31, 6, '2026-05-19', 'dadasdsa', 'Fahrul Adib', '082273647747', 'fahruladib9@gmail.com', 'Banyuasin', 'SUMATERA SELATAN', 'PALEMBANG', '12312', 'jne', '13000', '43000', 'Transfer', 'Diterima');

-- Dumping structure for table bookmarket_php.transaksidetail
CREATE TABLE IF NOT EXISTS `transaksidetail` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `transaksi_id` bigint DEFAULT NULL,
  `produk_id` bigint DEFAULT NULL,
  `jumlah` bigint DEFAULT NULL,
  `subtotal` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.transaksidetail: ~4 rows (approximately)
DELETE FROM `transaksidetail`;
INSERT INTO `transaksidetail` (`id`, `transaksi_id`, `produk_id`, `jumlah`, `subtotal`) VALUES
	(40, 31, 17, 1, 10000),
	(41, 31, 16, 2, 20000);

-- Dumping structure for table bookmarket_php.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jeniskelamin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nohp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bookmarket_php.users: ~2 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `jeniskelamin`, `nohp`, `alamat`, `role`) VALUES
	(1, 'Administrator', 'admin@gmail.com', '$2y$12$zqMABi5dpOLIa2YsK4/aze44fGsbTMPfnX6RwC8xK1FvsPtkBo.1e', 'Laki-laki', '082273647746', 'Banyuasin', 'Admin'),
	(6, 'Fahrul Adib', 'fahruladib9@gmail.com', '$2y$10$GQzIFxLJwSQOYLOHhjbPZ.4PyzOgckBmk.o2oXHyu7Bj9RcRaFUKG', 'Laki-laki', '082273647747', 'Banyuasin', 'User');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
