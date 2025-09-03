<?php
// history.php

include "conn.php";
session_start();

// Periksa apakah pengguna sudah login. Jika tidak, arahkan kembali ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Gunakan user_id dari sesi untuk alasan keamanan.
$user_id = $_SESSION['user_id'];

// Ambil total saldo dari tabel `saldo` menggunakan Prepared Statement.
$total_saldo = 0;
$saldo_stmt = mysqli_prepare($conn, "SELECT total_saldo FROM saldo WHERE user_id = ?");
mysqli_stmt_bind_param($saldo_stmt, "i", $user_id);
mysqli_stmt_execute($saldo_stmt);
$saldo_result = mysqli_stmt_get_result($saldo_stmt);

if ($saldo_result && mysqli_num_rows($saldo_result) > 0) {
    $saldo_data = mysqli_fetch_assoc($saldo_result);
    $total_saldo = $saldo_data['total_saldo'] ?? 0;
}
mysqli_stmt_close($saldo_stmt);

// Tentukan batas jumlah transaksi yang akan ditampilkan
$limit = 10;

// Ambil semua transaksi user dari tabel `riwayat_transaksi` menggunakan Prepared Statement.
// Kolom disesuaikan dengan skema database riwayat_transaksi yang Anda berikan.
$transaksi_stmt = mysqli_prepare($conn, "SELECT id, jenis_transaksi, jumlah, keterangan, tanggal_transaksi FROM riwayat_transaksi WHERE user_id = ? ORDER BY tanggal_transaksi DESC LIMIT ?");
mysqli_stmt_bind_param($transaksi_stmt, "ii", $user_id, $limit);
mysqli_stmt_execute($transaksi_stmt);
$result = mysqli_stmt_get_result($transaksi_stmt);

// Cek kolom yang tersedia di tabel riwayat_transaksi untuk memastikan semuanya ada
$check_columns = mysqli_query($conn, "DESCRIBE riwayat_transaksi");
$columns = [];
if ($check_columns) {
    while ($col = mysqli_fetch_assoc($check_columns)) {
        $columns[] = $col['Field'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History Transaksi - Bank Sampah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Definisi Variabel Warna */
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
        
        .main-container {
            max-width: 960px;
            margin: auto;
            padding: 20px 15px;
        }
        
        /* Desktop Navbar */
        .desktop-navbar {
            background: var(--gradient-main);
            box-shadow: 0 2px 10px var(--shadow-medium);
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
            transform: translateY(-3px);
        }
        .mobile-bottom-nav a.active {
            color: var(--accent-yellow);
            font-weight: 600;
            transform: translateY(-3px);
        }
        .mobile-bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        /* Header Section */
        .header {
            background: var(--gradient-main);
            color: var(--white);
            padding: 80px 20px 60px; /* Increased padding */
            text-align: center;
            border-bottom-left-radius: 80px;
            border-bottom-right-radius: 80px;
            position: relative;
            z-index: 0;
            box-shadow: 0 8px 25px var(--shadow-medium);
            overflow: hidden;
            margin-bottom: -50px; /* Overlap with content below */
        }
        .header::before { /* Decorative circle */
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
        
        /* Custom Keyframes untuk pergerakan yang lebih singkat */
        @keyframes fadeInDownCustom {
            from {
                opacity: 0;
                transform: translate3d(0, -30px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        @keyframes fadeInUpCustom {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        .header h1 {
            font-size: 3.2rem; /* Larger title */
            font-weight: 800;
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            animation: fadeInDownCustom 1s ease-out; /* Menggunakan animasi kustom */
        }
        .header p {
            font-size: 1.3rem; /* Larger subtitle */
            font-weight: 400;
            margin-top: 15px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
            animation: fadeInUpCustom 1s ease-out; /* Menggunakan animasi kustom */
        }
        
        /* Main Content Cards */
        .main-content {
            position: relative;
            z-index: 1;
            padding: 20px;
        }
        
        .saldo-card, .info-card, .form-card {
            background: var(--card-background);
            border-radius: 25px; /* Slightly larger radius */
            box-shadow: 0 12px 30px var(--shadow-light);
            padding: 30px;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .saldo-card:hover, .info-card:hover, .form-card:hover {
            transform: translateY(-8px); /* More pronounced hover */
            box-shadow: 0 18px 45px var(--shadow-medium);
        }
        
        /* Saldo Display */
        .saldo-card {
            text-align: center;
            padding: 40px; /* Increased padding */
            background: linear-gradient(145deg, var(--card-background), #f8f8f8); /* Subtle gradient */
            border: 1px solid rgba(0,0,0,0.05);
        }
        .saldo-card .total-amount {
            font-size: 4.8rem; /* Larger amount */
            color: var(--primary-green);
            font-weight: 800; /* Bolder amount */
            margin: 0;
            letter-spacing: -2px;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            line-height: 1.1; /* Adjust line-height for better alignment */
        }
        .saldo-card .total-amount::before {
            content: 'Rp';
            font-size: 0.4em;
            font-weight: 600; /* Bolder 'Rp' */
            position: relative;
            margin-right: 8px; /* More space */
            line-height: 1;
            color: var(--secondary-green);
            transform: translateY(-8px); /* Adjusted vertical position */
        }
        .saldo-card h2 {
            font-size: 1.3rem; /* Larger title */
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 15px;
        }
        .transaction-item {
            border-radius: 12px;
            margin-bottom: 10px;
            background: var(--card-background);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 5px solid transparent;
        }
        .transaction-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        }
        .transaction-item.setor {
            border-left-color: #198754;
        }
        .transaction-item.tarik {
            border-left-color: #dc3545;
        }
        .transaction-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            transition: background-color 0.3s ease;
        }
        .transaction-icon.setor-icon {
            background-color: #198754;
        }
        .transaction-icon.tarik-icon {
            background-color: #dc3545;
        }
        .transaction-details {
            flex-grow: 1;
        }
        .transaction-amount {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .filter-tabs .nav-link {
            border-radius: 20px;
            margin: 0 5px;
            font-weight: 500;
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        .filter-tabs .nav-link.active {
            background: #198754;
            color: white;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }
        .modal-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        .modal-footer .btn {
            border-radius: 25px;
        }

        /* ========== CSS BARU UNTUK SCROLLING DAN BATAS ========== */
        .transaction-container {
            max-height: 400px; /* Batas tinggi, sesuaikan sesuai kebutuhan Anda */
            overflow-y: auto; /* Mengaktifkan scrolling vertikal */
            padding-right: 15px;
        }

        .transaction-container::-webkit-scrollbar {
            width: 8px;
        }

        .transaction-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .transaction-container::-webkit-scrollbar-thumb {
            background: var(--primary-green);
            border-radius: 10px;
        }

        /* Tampilan mobile, kurangi tinggi scrollable */
        @media (max-width: 767.98px) {
            .transaction-container {
                max-height: 300px; /* Batas tinggi yang lebih rendah untuk perangkat mobile */
            }
        }

        /* ========== MEDIA QUERIES UNTUK RESPONSIVITAS ========== */

        /* Desktop */
        @media (min-width: 992px) {
            .desktop-navbar {
                display: flex;
            }
            .mobile-bottom-nav {
                display: none;
            }
        }
        
        /* Tablet dan Mobile (di bawah 992px) */
        @media (max-width: 991.98px) {
            .desktop-navbar {
                display: none;
            }
            .mobile-bottom-nav {
                display: flex;
            }
        }

        /* Layar Kecil (Mobile) */
        @media (max-width: 767.98px) {
            .header {
                padding: 60px 15px 40px;
                border-bottom-left-radius: 40px;
                border-bottom-right-radius: 40px;
            }
            .header h1 {
                font-size: 2rem;
            }
            .header p {
                font-size: 1rem;
            }
            .main-container {
                padding: 10px;
            }
            .saldo-card {
                padding: 25px;
            }
            .saldo-card .total-amount {
                font-size: 3.5rem;
            }
            .saldo-card .total-amount::before {
                font-size: 0.45em;
                transform: translateY(-6px);
            }
            .saldo-card h2 {
                font-size: 1.1rem;
            }
            .transaction-item .d-flex {
                flex-direction: row;
                align-items: center;
            }
            .transaction-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            .transaction-details h6 {
                font-size: 1rem;
            }
            .transaction-amount {
                font-size: 1.1rem;
            }
            .filter-tabs .nav-link {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark desktop-navbar">
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
    <a href="profile.php" class="<?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>"><i class="fas fa-user"></i><span>Akun</span></a>
</div>

<div class="header">
    <h1>Riwayat Transaksi</h1>
    <p>Cek semua aktivitas setor dan penarikan sampahmu di sini, dijamin terstruktur dan aman!</p>
</div>

<div class="main-container">
    <div class="main-content">
        <div class="saldo-card">
            <h2>Saldo Anda Saat Ini</h2>
            <p class="total-amount"><?php echo number_format($total_saldo, 2, ',', '.'); ?></p>
        </div>

<div class="container" style="padding-top: 20px;"> <?php if (in_array('jenis_transaksi', $columns)): ?>
    <ul class="nav nav-pills filter-tabs justify-content-center mb-4" id="filterTabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-filter="all">Semua</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-filter="setor">Setor Sampah</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-filter="tarik">Tarik Saldo</a>
        </li>
    </ul>
    <?php endif; ?>

    <div id="transactionList" class="transaction-container">
        <?php
        $has_transactions = false;
        if ($result && mysqli_num_rows($result) > 0) {
            $has_transactions = true;
            while ($row = mysqli_fetch_assoc($result)):
                $jenis = $row['jenis_transaksi'] ?? 'setor';
                $deskripsi = htmlspecialchars($row['keterangan'] ?? 'Transaksi Bank Sampah');
                $amount = floatval($row['jumlah'] ?? 0);
        ?>
        <div class="card transaction-item <?= $jenis ?>"
            data-jenis="<?= $jenis ?>"
            data-bs-toggle="modal"
            data-bs-target="#detailModal<?= $row['id'] ?>">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="transaction-icon <?= $jenis=='setor' ? 'setor-icon' : 'tarik-icon' ?>">
                            <?php if($jenis == 'setor'): ?>
                                <i class="bi bi-arrow-down-circle-fill"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-grow-1 transaction-details">
                        <h6 class="mb-1 fw-bold"><?= $deskripsi ?></h6>
                        <div class="d-flex align-items-center text-muted small mt-1">
                            <i class="bi bi-calendar3 me-1"></i>
                            <span class="me-3"><?= date("d M Y", strtotime($row['tanggal_transaksi'])) ?></span>
                            <i class="bi bi-clock me-1"></i>
                            <span><?= date("H:i", strtotime($row['tanggal_transaksi'])) ?></span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <h5 class="mb-0 transaction-amount <?= $jenis=='setor' ? 'text-success' : 'text-danger' ?>">
                            <?= $jenis=='setor' ? '+' : '-' ?>Rp<?= number_format($amount, 0, ',', '.') ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header <?= $jenis=='setor' ? 'bg-success' : 'bg-danger' ?> text-white">
                        <h5 class="modal-title">
                            <i class="bi <?= $jenis=='setor' ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up-right' ?> me-2"></i>
                            Detail <?= ucfirst($jenis) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-4"><strong>ID Transaksi:</strong></div>
                            <div class="col-8">#<?= $row['id'] ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Deskripsi:</strong></div>
                            <div class="col-8"><?= $deskripsi ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Jumlah:</strong></div>
                            <div class="col-8">
                                <span class="<?= $jenis=='setor' ? 'text-success' : 'text-danger' ?>">
                                    <?= $jenis=='setor' ? '+' : '-' ?>Rp<?= number_format($amount, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><strong>Tanggal:</strong></div>
                            <div class="col-8"><?= date("d M Y • H:i", strtotime($row['tanggal_transaksi'])) ?></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile;
        } else { ?>
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

<?php if (in_array('jenis_transaksi', $columns)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTabs = document.querySelectorAll('#filterTabs .nav-link');
        const transactionItems = document.querySelectorAll('.transaction-item');

        filterTabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const filter = this.dataset.filter;
                transactionItems.forEach(item => {
                    if (filter === 'all' || item.dataset.jenis === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
<?php endif; ?>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>