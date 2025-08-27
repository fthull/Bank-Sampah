-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Agu 2025 pada 06.14
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `detail_transaksi`
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
-- Struktur dari tabel `jenis_sampah`
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
-- Struktur dari tabel `laporan_penjualan`
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
-- Struktur dari tabel `penjemputan`
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
-- Struktur dari tabel `saldo`
--

CREATE TABLE `saldo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_saldo` decimal(12,2) DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `saldo`
--

INSERT INTO `saldo` (`id`, `user_id`, `total_saldo`, `updated_at`) VALUES
(1, 6, 6200.00, '2025-08-27 04:00:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
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
-- Struktur dari tabel `transaksi_nasabah`
--

CREATE TABLE `transaksi_nasabah` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `jenis` enum('setor','tarik') DEFAULT NULL,
  `jumlah` decimal(12,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('nasabah','admin','petugas') DEFAULT 'nasabah',
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `alamat`, `role`, `created_at`) VALUES
(1, 'adong', '', '$2y$10$fRIZxgebD84p.7901LydeujqjXAorW5Dqj.0QfVJg9S4NyJeYpINe', '', NULL, 'admin', '2025-08-25'),
(3, 'Fathul', 'fth@gmail.com', '$2y$10$uRRcs4FJJ0SZbltGEmHlRO2TLxJCCDXDyScfwx1dKpSpJmzaDbYQ2', '082134431425', NULL, 'nasabah', '2025-08-25'),
(5, 'saiful', '082134431425', '$2y$10$rkAkYv0GZGhHWed6gjW49uTx04DznDf66RJR.JJSp6j4D9WHepCmC', 'saiful@gmail.com', NULL, 'petugas', '2025-08-25'),
(6, 'iyad', 'iyd@gmail.com', '$2y$10$64aoKJet7r5yOVUuK3JNpOww1qsNGaltNye8akl.2Bz/i0MhSNzZu', '08992193321', NULL, 'nasabah', '2025-08-26');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `jenis_sampah_id` (`jenis_sampah_id`);

--
-- Indeks untuk tabel `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_sampah_id` (`jenis_sampah_id`);

--
-- Indeks untuk tabel `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `saldo`
--
ALTER TABLE `saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `transaksi_nasabah`
--
ALTER TABLE `transaksi_nasabah`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jenis_sampah`
--
ALTER TABLE `jenis_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penjemputan`
--
ALTER TABLE `penjemputan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `saldo`
--
ALTER TABLE `saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaksi_nasabah`
--
ALTER TABLE `transaksi_nasabah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`jenis_sampah_id`) REFERENCES `jenis_sampah` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan_penjualan`
--
ALTER TABLE `laporan_penjualan`
  ADD CONSTRAINT `laporan_penjualan_ibfk_1` FOREIGN KEY (`jenis_sampah_id`) REFERENCES `jenis_sampah` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penjemputan`
--
ALTER TABLE `penjemputan`
  ADD CONSTRAINT `penjemputan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `saldo`
--
ALTER TABLE `saldo`
  ADD CONSTRAINT `saldo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
