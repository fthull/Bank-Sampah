<?php
$servername = "127.0.0.1";
$username = "root";
$password = ""; 
$dbname = "bank_sampah";

$conn = mysqli_connect($servername, $username, $password, $dbname);


// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>