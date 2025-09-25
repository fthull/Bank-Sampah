<?php
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data dari POST
$total_bottle = intval($_POST['total_bottle'] ?? 0);
$total_kaleng = intval($_POST['total_kaleng'] ?? 0);

// Harga per item
$harga_bottle = 200;  // harga per botol
$harga_kaleng = 500;  // harga per kaleng

// Hitung total uang
$jumlah_uang = ($total_bottle * $harga_bottle) + ($total_kaleng * $harga_kaleng);

// 🚨 Cek kalau jumlah uang = 0, jangan simpan transaksi
if ($jumlah_uang <= 0) {
    echo "NO_DATA"; // respon khusus
    exit();
}

// Buat deskripsi dinamis
$deskripsiParts = [];
if ($total_bottle > 0) {
    $deskripsiParts[] = $total_bottle . " botol";
}
if ($total_kaleng > 0) {
    $deskripsiParts[] = $total_kaleng . " kaleng";
}
$deskripsi = "Setor " . implode(" + ", $deskripsiParts);

// Simpan ke tabel transaksi_2
$now = date("Y-m-d H:i:s");

// Pakai prepared statement biar aman dari SQL Injection
$sql = "INSERT INTO transaksi_2 
        (user_id, jenis, deskripsi, jumlah, metode, status, created_at, updated_at) 
        VALUES (?, 'setor', ?, ?, 'tunai', 'berhasil', ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isiss", $user_id, $deskripsi, $jumlah_uang, $now, $now);

if (mysqli_stmt_execute($stmt)) {
    echo "OK";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
