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
$total_lakban = intval($_POST['total_lakban'] ?? 0);

// Harga per item
$harga_bottle = 200;
$harga_lakban = 500;

// Hitung total uang
$jumlah_uang = ($total_bottle * $harga_bottle) + ($total_lakban * $harga_lakban);

// Buat deskripsi
$deskripsi = "Setor ";
if ($total_bottle > 0) {
    $deskripsi .= $total_bottle . " botol";
}
if ($total_lakban > 0) {
    if ($total_bottle > 0) $deskripsi .= " + ";
    $deskripsi .= $total_lakban . " lakban";
}

// Simpan ke tabel transaksi_2
$now = date("Y-m-d H:i:s");
$sql = "INSERT INTO transaksi_2 
        (user_id, jenis, deskripsi, jumlah, metode, status, created_at, updated_at) 
        VALUES 
        ('$user_id', 'setor', '$deskripsi', '$jumlah_uang', 'tunai', 'berhasil', '$now', '$now')";

if (mysqli_query($conn, $sql)) {
    echo "OK";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>