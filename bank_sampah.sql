-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 04:31 AM
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
-- Database: `bank_sampah`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `jenis_sampah_id` int(11) NOT NULL,
  `berat` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_sampah`
--

CREATE TABLE `jenis_sampah` (
  `id` int(11) NOT NULL,
  `nama_sampah` varchar(100) NOT NULL,
  `harga_perkg` decimal(10,2) NOT NULL,
  `satuan` varchar(20) DEFAULT 'kg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_penjualan`
--

CREATE TABLE `laporan_penjualan` (
  `id` int(11) NOT NULL,
  `jenis_sampah_id` int(11) NOT NULL,
  `berat` decimal(10,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjemputan`
--

CREATE TABLE `penjemputan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `alamat` text NOT NULL,
  `jadwal` date NOT NULL,
  `status` enum('diajukan','dijemput','selesai','batal') DEFAULT 'diajukan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_transaksi`
--

CREATE TABLE `riwayat_transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jenis_transaksi` varchar(50) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_transaksi`
--

INSERT INTO `riwayat_transaksi` (`id`, `user_id`, `jenis_transaksi`, `jumlah`, `keterangan`, `tanggal_transaksi`) VALUES
(1, 6, 'setor', 21000.00, 'Setor sampah', '2025-08-28 03:35:13'),
(2, 6, 'tarik', 3000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:44:02'),
(3, 6, 'tarik', 3000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:49:10'),
(4, 6, 'tarik', 1000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:49:37'),
(5, 6, 'tarik', 1000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:50:46'),
(6, 6, 'tarik', 1000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:50:59'),
(7, 6, 'tarik', 3000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:56:08'),
(8, 6, 'tarik', 1000.00, 'Penarikan ke Dana, No. Rek: 08992193321', '2025-08-28 07:59:07'),
(9, 6, 'tarik', 1000.00, 'Penarikan sebesar Rp 1.000,00 ke E-wallet: GoPay, No. Telp: 08992193321', '2025-09-02 03:35:13'),
(10, 6, 'tarik', 1000.00, 'Penarikan sebesar Rp 1.000,00 ke E-wallet: Dana, No. Telp: 08992193321', '2025-09-02 03:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `saldo`
--

CREATE TABLE `saldo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_saldo` decimal(12,2) DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saldo`
--

INSERT INTO `saldo` (`id`, `user_id`, `total_saldo`, `updated_at`) VALUES
(1, 6, 6000.00, '2025-09-02 03:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jenis` enum('setor','tarik') NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','selesai','ditolak') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('nasabah','admin','petugas') DEFAULT 'nasabah',
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `gender` enum('Pria','Wanita') DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `alamat`, `role`, `created_at`, `nama_lengkap`, `gender`, `foto`) VALUES
(1, 'adong', '', '$2y$10$fRIZxgebD84p.7901LydeujqjXAorW5Dqj.0QfVJg9S4NyJeYpINe', '', NULL, 'admin', '2025-08-25', NULL, NULL, NULL),
(3, 'Fathul', 'fth@gmail.com', '$2y$10$uRRcs4FJJ0SZbltGEmHlRO2TLxJCCDXDyScfwx1dKpSpJmzaDbYQ2', '082134431425', NULL, 'nasabah', '2025-08-25', NULL, NULL, NULL),
(5, 'saiful', '082134431425', '$2y$10$rkAkYv0GZGhHWed6gjW49uTx04DznDf66RJR.JJSp6j4D9WHepCmC', 'saiful@gmail.com', NULL, 'petugas', '2025-08-25', NULL, NULL, NULL),
(6, 'iyad', 'iyd@gmail.com', '$2y$10$64aoKJet7r5yOVUuK3JNpOww1qsNGaltNye8akl.2Bz/i0MhSNzZu', '08992193321', NULL, 'nasabah', '2025-08-26', 'fathul', 'Pria', '68b7a2c712dc5_8e73a4c7c790cf27.jpeg'),
(7, 'fadillah', 'nfadillahh74@gamil.com', '$2y$10$IC16hf6vVyIQShruHfL2cOWqPDXnXGrWMiji7f5mQL0Hm2ktjW0Ei', '082340633377', NULL, 'nasabah', '2025-08-27', NULL, NULL, '68b7a31d41b21_b11b617b73fa5b83.jpg'),
(8, 'fadillah', 'nfadillah74@gmail.com', '$2y$10$IJhPUgOhITkW/Yqo56cg3.XtynZnpW8u7CMJss4KBRG7p0J8g0eiS', '085333454686', NULL, 'nasabah', '2025-08-27', NULL, NULL, NULL),
(9, 'indah', 'nfadillah@gmail.com', '$2y$10$O9HfqrtAkFOwfJUQYVizwuVcvh/ymi78ITAOpdRn56eDvLymvxuNa', '085333454686', NULL, 'nasabah', '2025-08-27', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `jenis_sampah_id` (`jenis_sampah_id`);

--
-- Indexes for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_sampah_id` (`jenis_sampah_id`);

--
-- Indexes for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saldo`
--
ALTER TABLE `saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penjemputan`
--
ALTER TABLE `penjemputan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `saldo`
--
ALTER TABLE `saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`jenis_sampah_id`) REFERENCES `jenis_sampah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  ADD CONSTRAINT `laporan_penjualan_ibfk_1` FOREIGN KEY (`jenis_sampah_id`) REFERENCES `jenis_sampah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD CONSTRAINT `penjemputan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_transaksi`
--
ALTER TABLE `riwayat_transaksi`
  ADD CONSTRAINT `riwayat_transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saldo`
--
ALTER TABLE `saldo`
  ADD CONSTRAINT `saldo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
