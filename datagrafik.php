<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Koneksi ke database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "bank_sampah";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Mengambil data transaksi dari 7 hari terakhir dari tabel `transaksi_2`
$sql = "SELECT
            DATE(created_at) AS tanggal,
            SUM(CASE WHEN jenis = 'setor' THEN jumlah ELSE 0 END) AS pemasukan,
            SUM(CASE WHEN jenis = 'tarik' THEN jumlah ELSE 0 END) AS pengeluaran
        FROM
            transaksi_2
        WHERE
            user_id = ? AND created_at >= CURDATE() - INTERVAL 7 DAY
        GROUP BY
            tanggal
        ORDER BY
            tanggal ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>