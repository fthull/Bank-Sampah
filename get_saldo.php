<?php
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['saldo' => 0]);
    exit();
}

$user_id = $_SESSION['user_id'];
$q = mysqli_query($conn, "SELECT total_saldo FROM saldo WHERE user_id='$user_id' LIMIT 1");
$data = mysqli_fetch_assoc($q);
$saldo = $data['total_saldo'] ?? 0;

echo json_encode(['saldo' => $saldo]);
