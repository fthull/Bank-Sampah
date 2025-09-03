<?php
// Pastikan file konfigurasi database sudah di-include
require_once 'conn.php';

// ... (Kode untuk mengambil data form dan memperbarui saldo di tabel `saldo` harus berada di sini) ...

// Ini adalah bagian kode yang telah diperbaiki untuk MENCATAT transaksi setor ke dalam riwayat.

// Ambil nilai-nilai yang diperlukan dari form atau proses sebelumnya
// Ganti ini dengan variabel yang menampung jumlah uang dari form Anda
$user_id = $_SESSION['user_id'];
$jumlah_setor = $_POST['jumlah']; 
$keterangan = "Setor sampah berhasil"; // Deskripsi transaksi

// Siapkan koneksi database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    // Tangani error jika koneksi gagal
    header("Location: beranda.php?status=error_db");
    exit();
}

// Siapkan query untuk memasukkan data ke tabel riwayat_transaksi
// Perhatikan nama tabel dan kolom yang sudah disesuaikan
$sql_insert = "INSERT INTO riwayat_transaksi (user_id, jenis_transaksi, jumlah, keterangan) VALUES (?, 'setor', ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);

// 'i' untuk integer (user_id), 'd' untuk decimal (jumlah), 's' untuk string (keterangan)
// Jenis transaksi 'setor' sudah ditulis langsung di query
$stmt_insert->bind_param("ids", $user_id, $jumlah_setor, $keterangan);

// Jalankan perintah INSERT
if ($stmt_insert->execute()) {
    // Berhasil mencatat riwayat, sekarang arahkan pengguna ke halaman sukses
    header("Location: beranda.php?status=success_setor");
    exit();
} else {
    // Gagal mencatat riwayat
    header("Location: beranda.php?status=error_setor");
    exit();
}

$stmt_insert->close();
$conn->close();
?>