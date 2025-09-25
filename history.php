<?php
// history.php

include "conn.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Gunakan user_id dari sesi untuk alasan keamanan.
$user_id = $_SESSION['user_id'];

// Ambil total saldo dari tabel `saldo` menggunakan Prepared Statement.
$total_saldo = 0;
$saldo_stmt = mysqli_prepare($conn, "SELECT total_saldo FROM saldo WHERE user_id = ?");
if ($saldo_stmt) {
    mysqli_stmt_bind_param($saldo_stmt, "i", $user_id);
    mysqli_stmt_execute($saldo_stmt);
    $saldo_result = mysqli_stmt_get_result($saldo_stmt);

    if ($saldo_result && mysqli_num_rows($saldo_result) > 0) {
        $saldo_data = mysqli_fetch_assoc($saldo_result);
        $total_saldo = $saldo_data['total_saldo'] ?? 0;
    }
    mysqli_stmt_close($saldo_stmt);
}


// Ambil semua transaksi user dari tabel `transaksi_2` menggunakan Prepared Statement.

// Perintah ini mengambil data berdasarkan 'user_id' dan mengurutkan berdasarkan tanggal terbaru.
// Tambahkan LIMIT 10 untuk membatasi jumlah transaksi yang ditampilkan.
=
$transaksi_stmt = mysqli_prepare($conn, "SELECT id, jenis, deskripsi, jumlah, metode, status, created_at FROM transaksi_2 WHERE user_id = ? ORDER BY created_at DESC");

if ($transaksi_stmt) {
    mysqli_stmt_bind_param($transaksi_stmt, "i", $user_id);
    mysqli_stmt_execute($transaksi_stmt);
    $transaksi_result = mysqli_stmt_get_result($transaksi_stmt);
    $transactions = mysqli_fetch_all($transaksi_result, MYSQLI_ASSOC);
    mysqli_stmt_close($transaksi_stmt);
}


// Tutup koneksi
mysqli_close($conn);

// Fungsi untuk mengonversi tanggal ke format Indonesia
function format_date($date_string) {
    $timestamp = strtotime($date_string);
    return date('d M Y H:i', $timestamp);
}

// Fungsi untuk format tanggal lengkap
function format_date_full($date_string) {
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $timestamp = strtotime($date_string);
    $day = date('d', $timestamp);
    $month = $months[date('m', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return $day . ' ' . $month . ' ' . $year . ' pukul ' . $time;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Bank Sampah</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Variabel Warna */
        :root {
            --primary-green: #2e8b57;
            --secondary-green: #3cb371;
            --accent-yellow: #ffc107;
            --bg-light: #f3fff8;
            --card-background: #ffffff;
            --text-dark: #333333;
            --text-light: #777777;
            --white: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --gradient-main: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        }

        /* Gaya Umum */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-bottom: 70px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Adjust body padding for fixed-top navbar on desktop */
        @media (min-width: 769px) {
            body {
                padding-top: 70px; /* Add padding to prevent content from being hidden by the fixed navbar */
            }
        }

        .main-container {
            max-width: 960px;
            margin: auto;
            padding: 20px 15px;
        }

        /* Desktop Navbar */
        .desktop-navbar {
            background: var(--gradient-main);
            box-shadow: 0 2px 10px var(--shadow-medium);
            z-index: 1000; /* Ensure it stays on top of other content */
        }
        .desktop-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--white);
            transition: transform 0.3s ease;
        }
        .desktop-navbar .navbar-brand:hover {
            transform: scale(1.05);
        }
        .desktop-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: color 0.3s, transform 0.3s;
            position: relative;
        }
        .desktop-navbar .nav-link:hover,
        .desktop-navbar .nav-link.active {
            color: var(--accent-yellow) !important;
            transform: translateY(-2px);
        }
        .desktop-navbar .nav-link.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background-color: var(--accent-yellow);
            border-radius: 2px;
        }

        /* Mobile Navbar */
        .mobile-bottom-nav {
            display: none; /* Hidden by default, shown on mobile */
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: var(--primary-green);
            color: var(--white);
            z-index: 9999;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.2);
        }
        .mobile-bottom-nav a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .mobile-bottom-nav a:hover {
            color: var(--accent-yellow);
            transform: translateY(-20px);
            padding-top: 12px;
            background: var(--primary-green);
            border-radius: 50%;

        }
        .mobile-bottom-nav a.active {
            color: var(--accent-yellow);
            font-weight: 600;
            transform: translateY(-20px);
            padding-top: 12px;
            background: var(--primary-green);
            border-radius: 50%;
        }
        .mobile-bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        /* Bagian Header */
        .header {
            background: var(--gradient-main);
            color: var(--white);
            padding: 80px 20px 60px;
            text-align: center;
            border-bottom-left-radius: 80px;
            border-bottom-right-radius: 80px;
            position: relative;
            z-index: 0;
            box-shadow: 0 8px 25px var(--shadow-medium);
            overflow: hidden;
            margin-bottom: -50px;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }
        .header h1 {
            font-size: 3.2rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
        }
        .header p {
            font-size: 1.3rem;
            font-weight: 400;
            margin-top: 15px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }

        /* Konten Utama */
        .main-content {
            position: relative;
            z-index: 1;
            padding: 20px;
        }

        .saldo-card {
            background: var(--card-background);
            border-radius: 25px;
            box-shadow: 0 12px 30px var(--shadow-light);
            padding: 40px;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            background: linear-gradient(145deg, var(--card-background), #f8f8f8);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .saldo-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 45px var(--shadow-medium);
        }
        .saldo-card h2 {
            font-size: 1.3rem;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 15px;
        }
        .saldo-card .total-amount {
            font-size: 4.8rem;
            color: var(--primary-green);
            font-weight: 800;
            margin: 0;
            letter-spacing: -2px;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            line-height: 1.1;
        }
        .saldo-card .total-amount::before {
            content: 'Rp ';
            font-size: 0.4em;
            font-weight: 600;
            position: relative;
            margin-right: 8px;
            line-height: 1;
            color: var(--secondary-green);
            transform: translateY(-8px);
        }

        /* Filter Tabs */
        .filter-tabs {
            margin-bottom: 20px;
            background: #fff;
            border-radius: 15px;
            padding: 8px;
            box-shadow: 0 4px 15px var(--shadow-light);
        }

        .filter-tabs .nav-link {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: var(--text-light);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .filter-tabs .nav-link.active {
            background-color: var(--primary-green);
            color: var(--white);
            box-shadow: 0 4px 10px rgba(46, 139, 87, 0.3);
        }

        /* Riwayat Transaksi */
        .transaction-list {
            max-height: 500px; /* Atur ketinggian maksimum */
            overflow-y: auto; /* Tambahkan scrollbar vertikal */
            padding-bottom: 20px; /* Tambahkan ruang di bagian bawah list */
        }

        .transaction-list::-webkit-scrollbar {
            width: 8px;
        }

        .transaction-list::-webkit-scrollbar-track {
            background: var(--bg-light);
            border-radius: 10px;
        }

        .transaction-list::-webkit-scrollbar-thumb {
            background-color: var(--secondary-green);
            border-radius: 10px;
            border: 2px solid var(--bg-light);
        }

        .transaction-item {
            background: var(--card-background);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px var(--shadow-light);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer; /* Tambahan untuk menunjukkan item dapat diklik */
        }
        .transaction-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px var(--shadow-medium);
        }

        .transaction-item .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
        }

        .transaction-item.setor .icon-box { background-color: var(--secondary-green); }
        .transaction-item.tarik .icon-box { background-color: #e67e22; }

        .transaction-item .details {
            flex-grow: 1;
        }
        .transaction-item .details h5 {
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }
        .transaction-item .details p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-light);
        }
        .transaction-item .amount {
            font-weight: 700;
            font-size: 1.1rem;
            margin-left: auto;
        }
        .transaction-item.setor .amount { color: var(--primary-green); }
        .transaction-item.tarik .amount { color: #e74c3c; }

        /* Modal Detail Transaksi */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: var(--gradient-main);
            color: var(--white);
            border-radius: 20px 20px 0 0;
            padding: 25px;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .modal-body {
            padding: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1rem;
        }

        .detail-value {
            color: var(--text-light);
            font-size: 1rem;
            text-align: right;
        }

        .detail-value.amount {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .detail-value.amount.setor {
            color: var(--primary-green);
        }

        .detail-value.amount.tarik {
            color: #e74c3c;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-berhasil {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-gagal {
            background-color: #f8d7da;
            color: #721c24;
        }

        .transaction-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--white);
            margin: 0 auto 20px;
        }

        .transaction-icon-large.setor {
            background: var(--secondary-green);
        }

        .transaction-icon-large.tarik {
            background: #e67e22;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-light);
        }
        .empty-state h5 {
            font-weight: 600;
            color: var(--text-dark);
            margin-top: 10px;
        }
        .empty-state .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(46, 139, 87, 0.3);
        }
        .empty-state .btn-success:hover {
            background-color: #28844e;
        }

        /* Media Queries untuk Responsif */
        @media (max-width: 768px) {
            .desktop-navbar {
                display: none;
            }
            .mobile-bottom-nav {
                display: flex;
            }
            body {
                padding-top: 0;
            }
            .header {
                padding: 60px 15px 40px;
                border-bottom-left-radius: 50px;
                border-bottom-right-radius: 50px;
                margin-bottom: -30px;
            }
            .header h1 {
                font-size: 2.5rem;
            }
            .header p {
                font-size: 1rem;
            }
            .main-container {
                padding: 10px;
            }
            .saldo-card {
                padding: 30px;
            }
            .saldo-card .total-amount {
                font-size: 3.8rem;
            }
            .saldo-card .total-amount::before {
                font-size: 0.38em;
                margin-right: 5px;
                transform: translateY(-5px);
            }
            .saldo-card h2 {
                font-size: 1rem;
            }
            .transaction-item {
                padding: 15px;
            }
            .transaction-item .icon-box {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
            .transaction-item .details h5 {
                font-size: 1rem;
            }
            .transaction-item .details p {
                font-size: 0.85rem;
            }
            .transaction-item .amount {
                font-size: 1rem;
            }
            .filter-tabs .nav-link {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
            .modal-body {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark desktop-navbar fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Bank Sampah</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'beranda.php' ? 'active' : ''); ?>" href="beranda.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'harga.php' ? 'active' : ''); ?>" href="harga.php">Setor Sampah</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'saldo.php' ? 'active' : ''); ?>" href="saldo.php">Penarikan</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'history.php' ? 'active' : ''); ?>" href="history.php">History</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>" href="profile.php">Akun</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="mobile-bottom-nav">
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <a href="beranda.php" class="<?php echo ($current_page == 'beranda.php' ? 'active' : ''); ?>"><i class="fas fa-home"></i><span>Home</span></a>
    <a href="harga.php" class="<?php echo ($current_page == 'harga.php' ? 'active' : ''); ?>"><i class="fas fa-recycle"></i><span>Setor</span></a>
    <a href="saldo.php" class="<?php echo ($current_page == 'saldo.php' ? 'active' : ''); ?>"><i class="fas fa-money-bill-wave"></i><span>Tarik</span></a>
    <a href="history.php" class="<?php echo ($current_page == 'history.php' ? 'active' : ''); ?>"><i class="fas fa-history"></i><span>History</span></a>
    <a href="profile.php" class="<?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>" href="profile.php"><i class="fas fa-user"></i><span>Akun</span></a>
</div>

<div class="header">
    <h1>Riwayat Transaksi</h1>
    <p>Lihat semua aktivitas setor dan penarikan saldo Anda di sini.</p>
</div>

<div class="main-container">
    <div class="main-content">
        <div class="saldo-card">
            <h2>Saldo Anda Saat Ini</h2>
            <p class="total-amount"><?php echo number_format($total_saldo, 2, ',', '.'); ?></p>
        </div>

        <?php if (!empty($transactions)) { ?>
        <div class="filter-tabs nav nav-pills" id="filterTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="all-tab" data-filter="all" href="#all" role="tab" aria-controls="all" aria-selected="true">Semua</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="setor-tab" data-filter="setor" href="#setor" role="tab" aria-controls="setor" aria-selected="false">Setor</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tarik-tab" data-filter="tarik" href="#tarik" role="tab" aria-controls="tarik" aria-selected="false">Tarik</a>
            </li>
        </div>
        <div class="transaction-list">
            <?php foreach ($transactions as $transaction) {
                $jenis = $transaction['jenis'];
                $jumlah = floatval($transaction['jumlah']);
                $deskripsi = $transaction['deskripsi'];
                $tanggal = format_date($transaction['created_at']);
                $icon = ($jenis == 'setor') ? 'fas fa-plus' : 'fas fa-minus';
                $sign = ($jenis == 'setor') ? '+' : '-';
                $class_jenis = ($jenis == 'setor') ? 'setor' : 'tarik';
                ?>
                <div class="transaction-item <?php echo $class_jenis; ?>" 
                     data-jenis="<?php echo $class_jenis; ?>"
                     data-bs-toggle="modal" 
                     data-bs-target="#transactionModal"
                     data-id="<?php echo $transaction['id']; ?>"
                     data-jenis-text="<?php echo ucfirst($jenis); ?>"
                     data-deskripsi="<?php echo htmlspecialchars($deskripsi); ?>"
                     data-jumlah="<?php echo $jumlah; ?>"
                     data-metode="<?php echo ucfirst($transaction['metode']); ?>"
                     data-status="<?php echo $transaction['status']; ?>"
                     data-tanggal="<?php echo format_date_full($transaction['created_at']); ?>"
                     data-tanggal-singkat="<?php echo $tanggal; ?>">
                    <div class="icon-box">
                        <i class="<?php echo $icon; ?>"></i>
                    </div>
                    <div class="details">
                        <h5><?php echo ucfirst($jenis); ?> Saldo</h5>
                        <p><?php echo $deskripsi; ?></p>
                        <p><small class="text-muted"><?php echo $tanggal; ?></small></p>
                    </div>
                    <div class="amount">

                        <span><?php echo $sign; ?>Rp <?php echo number_format($jumlah, 0, ',', '.'); ?></span>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-eye me-1"></i>Detail
                        </small>

                    </div>
                </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="empty-state">
            <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
            <h5>Belum Ada Transaksi</h5>
            <p>Mulai setor sampah untuk melihat riwayat transaksi Anda</p>
            <a href="harga.php" class="btn btn-success">
                <i class="bi bi-recycle me-1"></i>Setor Sampah
            </a>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">
                    <i class="fas fa-receipt me-2"></i>Detail Transaksi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="transaction-icon-large" id="modalIcon">
                        <i class="fas fa-plus" id="modalIconSymbol"></i>
                    </div>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">ID Transaksi</span>
                    <span class="detail-value" id="modalId">#TRX001</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Jenis Transaksi</span>
                    <span class="detail-value" id="modalJenis">Setor Saldo</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Deskripsi</span>
                    <span class="detail-value" id="modalDeskripsi">Setor 3 botol plastik</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Jumlah</span>
                    <span class="detail-value amount" id="modalJumlah">+Rp 600</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Metode</span>
                    <span class="detail-value" id="modalMetode">Tunai</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge" id="modalStatus">Berhasil</span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Tanggal & Waktu</span>
                    <span class="detail-value" id="modalTanggal">1 September 2025 pukul 21:22</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTabs = document.querySelectorAll('#filterTabs .nav-link');
        const transactionItems = document.querySelectorAll('.transaction-item');

        // Filter functionality
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const filter = this.dataset.filter;
                transactionItems.forEach(item => {
                    if (filter === 'all' || item.dataset.jenis === filter) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Modal functionality
        const transactionModal = document.getElementById('transactionModal');

        transactionModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            // Ambil data dari atribut data-*
            const id = button.getAttribute('data-id');
            const jenis = button.getAttribute('data-jenis');
            const jenisText = button.getAttribute('data-jenis-text');
            const deskripsi = button.getAttribute('data-deskripsi');
            const jumlah = parseFloat(button.getAttribute('data-jumlah'));
            const metode = button.getAttribute('data-metode');
            const status = button.getAttribute('data-status');
            const tanggal = button.getAttribute('data-tanggal');

            // Isi modal
            document.getElementById('modalId').textContent = "#TRX" + id;
            document.getElementById('modalJenis').textContent = jenisText + " Saldo";
            document.getElementById('modalDeskripsi').textContent = deskripsi;

            const jumlahFormatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(jumlah);

            document.getElementById('modalJumlah').textContent = 
                (jenis === 'setor' ? "+" : "-") + jumlahFormatted;
            document.getElementById('modalJumlah').className = "detail-value amount " + jenis;

            document.getElementById('modalMetode').textContent = metode;
            document.getElementById('modalTanggal').textContent = tanggal;

            // Status badge
            const statusBadge = document.getElementById('modalStatus');
            statusBadge.textContent = status;
            statusBadge.className = "status-badge";
            if (status.toLowerCase() === 'berhasil') {
                statusBadge.classList.add("status-berhasil");
            } else if (status.toLowerCase() === 'pending') {
                statusBadge.classList.add("status-pending");
            } else {
                statusBadge.classList.add("status-gagal");
            }

            // Icon besar di modal
            const modalIcon = document.getElementById('modalIcon');
            const modalIconSymbol = document.getElementById('modalIconSymbol');
            modalIcon.className = "transaction-icon-large " + jenis;
            modalIconSymbol.className = (jenis === 'setor') ? "fas fa-plus" : "fas fa-minus";
        });
    });
</script>


</body>
</html>