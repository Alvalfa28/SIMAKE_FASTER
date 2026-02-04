-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 03:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simake`
--

-- --------------------------------------------------------

--
-- Table structure for table `bukti_pembayaran`
--

CREATE TABLE `bukti_pembayaran` (
  `id_bukti_pembayaran` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `npm` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keterangan` text NOT NULL,
  `lampiran` varchar(255) NOT NULL,
  `statuz` varchar(20) NOT NULL DEFAULT 'Diajukan',
  `komentar_revisi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_keuangan`
--

CREATE TABLE `laporan_keuangan` (
  `id_laporan_keuangan` int(11) NOT NULL,
  `periode` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `lampiran` varchar(255) NOT NULL,
  `statuz` varchar(20) NOT NULL DEFAULT 'Diajukan',
  `komentar_revisi_l_k` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rapbf`
--

CREATE TABLE `rapbf` (
  `id_rapbf` int(11) NOT NULL,
  `id_prodi` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `periode` varchar(10) NOT NULL,
  `total_anggaran` decimal(15,2) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `keterangan` text NOT NULL,
  `lampiran` varchar(255) NOT NULL,
  `statuz` varchar(20) NOT NULL,
  `komentar_rapbf` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_keluar`
--

CREATE TABLE `transaksi_keluar` (
  `id_transaksi_keluar` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `jenis_detail` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `lampiran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_masuk`
--

CREATE TABLE `transaksi_masuk` (
  `id_transaksi_masuk` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `lampiran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staf_keuangan','mahasiswa','prodi','wakil_dekan_dua') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'Admin', '80177534a0c99a7e3645b52f2027a48b', 'admin', '2025-11-04 03:39:59'),
(2, 'stafkeuangan', 'Staf Keuangan', '482ad9d7f7183511bd37f6b92dab9a1b', 'staf_keuangan', '2025-11-04 03:39:59'),
(13, 'wddua', 'Wakil Dekan II', '0d27346e1a9198d632f5a5babcea468e', 'wakil_dekan_dua', '2025-11-28 06:36:00'),
(17, '5520122028', 'Alvalfa Qayyala Mudrika', '245bac49027c049fe2cf21081da8adc7', 'mahasiswa', '2025-12-16 02:35:46'),
(18, 'prodiadministrasi', 'Prodi Administrasi Internasional', '6abe9ed939f7857d4aed0d14d81c0752', 'prodi', '2025-12-16 02:36:09'),
(21, 'prodiagro', 'Prodi Agroteknologi', '36348279c164416abf04a59e9ff482f5', 'prodi', '2025-12-16 06:03:03'),
(22, 'prodiagri', 'Prodi Agribisnis', '5616e91318e742b835aec623d6d0de36', 'prodi', '2025-12-16 06:03:19'),
(23, '5520122003', 'Gilang Wardiansyah', '6d8f8a1a4837f099459ec46a72660f30', 'mahasiswa', '2025-12-17 02:27:24'),
(24, '5520122004', 'Sharazeintya', '80ff21f0ef32d7941b3ce621a192238a', 'mahasiswa', '2025-12-17 02:28:10'),
(25, '5520122005', 'M Ari Rahman', 'f0ba8f9f389484af6f1a6ccc62a645d0', 'mahasiswa', '2025-12-17 02:29:01'),
(29, '5520122049 ', 'Riansyah Asmara', '26ed30f28908645239254ff4f88c1b75', 'mahasiswa', '2025-12-17 09:09:23'),
(31, '5520122056', 'Alwafaa Kuswandarsah', '8a8d4891d67cd054a1d0b0e53e60aa93', 'mahasiswa', '2025-12-18 06:52:48'),
(32, '5520122008', 'Muhammad Anggara ', '1fd5cd9766249f170035b7251e2c6b61', 'mahasiswa', '2025-12-18 08:53:56'),
(33, '5520122010', 'Tiara Renata', 'b26dac0d3e2928565b690897f0b4a8c9', 'mahasiswa', '2025-12-18 10:11:48'),
(34, 'stafkeuangan2', 'Asep Saepuloh', 'f3465a353436bbab3617815f64083c84', 'staf_keuangan', '2025-12-18 10:12:44'),
(35, '5420122036', 'Neng Rosalia', 'b37cb495badadda420604d692223aa6f', 'mahasiswa', '2026-01-04 13:39:31'),
(36, '5421122023', 'M Ari Juddin', '451d3eb1573c7ebb70c08dfee9766509', 'mahasiswa', '2026-01-04 13:40:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bukti_pembayaran`
--
ALTER TABLE `bukti_pembayaran`
  ADD PRIMARY KEY (`id_bukti_pembayaran`),
  ADD KEY `fk_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  ADD PRIMARY KEY (`id_laporan_keuangan`);

--
-- Indexes for table `rapbf`
--
ALTER TABLE `rapbf`
  ADD PRIMARY KEY (`id_rapbf`),
  ADD KEY `fk_prodi` (`id_prodi`);

--
-- Indexes for table `transaksi_keluar`
--
ALTER TABLE `transaksi_keluar`
  ADD PRIMARY KEY (`id_transaksi_keluar`);

--
-- Indexes for table `transaksi_masuk`
--
ALTER TABLE `transaksi_masuk`
  ADD PRIMARY KEY (`id_transaksi_masuk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bukti_pembayaran`
--
ALTER TABLE `bukti_pembayaran`
  MODIFY `id_bukti_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `laporan_keuangan`
--
ALTER TABLE `laporan_keuangan`
  MODIFY `id_laporan_keuangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `rapbf`
--
ALTER TABLE `rapbf`
  MODIFY `id_rapbf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaksi_keluar`
--
ALTER TABLE `transaksi_keluar`
  MODIFY `id_transaksi_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaksi_masuk`
--
ALTER TABLE `transaksi_masuk`
  MODIFY `id_transaksi_masuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bukti_pembayaran`
--
ALTER TABLE `bukti_pembayaran`
  ADD CONSTRAINT `fk_mahasiswa` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rapbf`
--
ALTER TABLE `rapbf`
  ADD CONSTRAINT `fk_prodi` FOREIGN KEY (`id_prodi`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
