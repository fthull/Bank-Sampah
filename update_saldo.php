<?php
include "conn.php";
session_start();

// Anggap user login punya id_nasabah
$id_nasabah = $_SESSION['user_id']; 
$jumlah = intval($_POST['total_saldo']);

// Update saldo nasabah
$sql = "UPDATE saldo SET total_saldo = total_saldo + $jumlah WHERE user_id = '$id_nasabah'";
if ($conn->query($sql)) {
    echo "Saldo berhasil ditambahkan";
} else {
    echo "Gagal update saldo: " . $conn->error;
}
