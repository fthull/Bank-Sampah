-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Agu 2025 pada 09.49
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
-- Struktur dari tabel `transaksi_2`
--

CREATE TABLE `transaksi_2` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `jenis` enum('setor','tarik') NOT NULL DEFAULT 'setor',
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metode` varchar(50) NOT NULL DEFAULT 'tunai',
  `status` enum('pending','berhasil','gagal') DEFAULT 'berhasil',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_2`
--

INSERT INTO `transaksi_2` (`id`, `user_id`, `jenis`, `deskripsi`, `jumlah`, `metode`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'setor', 'Setor Botol Plastik 2kg', 5000.00, 'tunai', 'berhasil', '2025-08-28 04:19:14', '2025-08-28 04:19:14'),
(2, 1, 'setor', 'Setor Kaleng Minuman 1kg', 4000.00, 'tunai', 'berhasil', '2025-08-28 04:19:14', '2025-08-28 04:19:14'),
(3, 1, 'tarik', 'Tarik Saldo ke GoPay', 7000.00, 'gopay', 'berhasil', '2025-08-28 04:19:14', '2025-08-28 04:19:14'),
(4, 1, 'setor', 'Setor Botol Plastik 1.5kg', 3750.00, 'tunai', 'pending', '2025-08-28 04:19:14', '2025-08-28 04:19:14'),
(5, 1, 'tarik', 'Tarik Saldo ke OVO', 2000.00, 'ovo', 'berhasil', '2025-08-28 04:19:14', '2025-08-28 04:19:14');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `transaksi_2`
--
ALTER TABLE `transaksi_2`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `transaksi_2`
--
ALTER TABLE `transaksi_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
