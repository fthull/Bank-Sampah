<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conn.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil total saldo saat ini dari tabel saldo
$total_saldo = 0.00;
$sql_saldo = "SELECT total_saldo FROM saldo WHERE user_id = ?";
$stmt_saldo = $conn->prepare($sql_saldo);
$stmt_saldo->bind_param("i", $user_id);
$stmt_saldo->execute();
$result_saldo = $stmt_saldo->get_result();

if ($result_saldo->num_rows > 0) {
    $row_saldo = $result_saldo->fetch_assoc();
    $total_saldo = $row_saldo['total_saldo'] ?? 0.00;
}
$stmt_saldo->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah_tarik = floatval($_POST['jumlah_tarik']);
    $bank_tujuan = $_POST['bank_tujuan'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $nama_pemilik = $_POST['nama_pemilik'];

    if ($jumlah_tarik >= 1000) {
        // Ambil kembali saldo terbaru dari tabel saldo untuk memastikan tidak ada perubahan saat form diproses
        $stmt_check_saldo = $conn->prepare("SELECT total_saldo FROM saldo WHERE user_id = ?");
        $stmt_check_saldo->bind_param("i", $user_id);
        $stmt_check_saldo->execute();
        $result_check_saldo = $stmt_check_saldo->get_result();
        $row_check_saldo = $result_check_saldo->fetch_assoc();
        $current_balance = $row_check_saldo['total_saldo'] ?? 0.00;
        $stmt_check_saldo->close();

        if ($current_balance >= $jumlah_tarik) {
            // Saldo mencukupi, masukkan transaksi penarikan dan update saldo
            $conn->begin_transaction();
            try {
                // 1. Masukkan transaksi penarikan ke tabel transaksi_2
                $stmt_insert_transaksi = $conn->prepare("INSERT INTO transaksi_2 (user_id, jenis, jumlah, deskripsi) VALUES (?, ?, ?, ?)");
                
                $jenis = "tarik";
                $deskripsi = "Penarikan ke: " . $bank_tujuan . ", No. Telp: " . $nomor_rekening;

                $stmt_insert_transaksi->bind_param("isds", $user_id, $jenis, $jumlah_tarik, $deskripsi);
                $stmt_insert_transaksi->execute();

                if ($stmt_insert_transaksi->affected_rows > 0) {
                    // 2. Update saldo di tabel saldo
                    $stmt_update_saldo = $conn->prepare("UPDATE saldo SET total_saldo = total_saldo - ? WHERE user_id = ?");
                    $stmt_update_saldo->bind_param("di", $jumlah_tarik, $user_id);
                    $stmt_update_saldo->execute();

                    if ($stmt_update_saldo->affected_rows > 0) {
                        $conn->commit();
                        header("Location: saldo.php?status=success&amount=" . urlencode($jumlah_tarik));
                        exit();
                    } else {
                        $conn->rollback();
                        header("Location: saldo.php?status=error&message=" . urlencode("Terjadi kesalahan saat memperbarui saldo. Silakan coba lagi."));
                        exit();
                    }
                } else {
                    $conn->rollback();
                    header("Location: saldo.php?status=error&message=" . urlencode("Terjadi kesalahan saat menyimpan transaksi. Silakan coba lagi."));
                    exit();
                }
            } catch (Exception $e) {
                $conn->rollback();
                header("Location: saldo.php?status=error&message=" . urlencode("Terjadi kesalahan pada server: " . $e->getMessage()));
                exit();
            }
        } else {
            header("Location: saldo.php?status=error&message=" . urlencode("Maaf, saldo Anda tidak mencukupi untuk melakukan penarikan ini."));
            exit();
        }
    } else {
        header("Location: saldo.php?status=error&message=" . urlencode("Jumlah penarikan harus lebih dari Rp 1.000."));
        exit();
    }
}

$message = "";
$message_type = "";
$message_amount = 0;

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = "Penarikan sebesar Rp " . number_format($_GET['amount'], 2, ',', '.') . " berhasil diajukan.";
        $message_type = "success";
    } else if ($_GET['status'] == 'error') {
        $message = isset($_GET['message']) ? urldecode($_GET['message']) : "Terjadi kesalahan yang tidak diketahui.";
        $message_type = "error";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penarikan Saldo - Bank Sampah</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Definisi Variabel Warna */
        :root {
            --primary-green: #2e8b57; /* Hijau laut sedang */
            --secondary-green: #3cb371; /* Hijau musim semi sedang */
            --accent-yellow: #ffc107; /* Kuning cerah untuk aksen */
            --bg-light: #f3fff8; /* Latar belakang hijau sangat muda */
            --card-background: #ffffff; /* Putih */
            --text-dark: #333333; /* Teks gelap */
            --text-light: #777777; /* Teks abu-abu */
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
            padding-bottom: 70px; /* Space for mobile nav */
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
            box-shadow: 0 4px 20px var(--shadow-medium);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(10px);
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
            transition: all 0.3s ease;
            position: relative;
            padding: 8px 16px !important;
            border-radius: 25px;
            margin: 0 4px;
        }
        .desktop-navbar .nav-link:hover,
        .desktop-navbar .nav-link.active {
            color: var(--text-dark) !important;
            background: var(--accent-yellow);
            transform: translateY(-2px);
        }
        
        /* Mobile Navbar */
        .mobile-bottom-nav {
            display: none;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: var(--gradient-main);
            color: var(--white);
            z-index: 9999;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.2);
        }
        .mobile-bottom-nav a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            text-align: center;
            text-decoration: none;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
            padding: 8px 4px;
        }
        .mobile-bottom-nav a:hover,
        .mobile-bottom-nav a.active {
            color: var(--accent-yellow);
            transform: translateY(-3px);
        }
        .mobile-bottom-nav i {
            display: block;
            font-size: 18px;
            margin-bottom: 4px;
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
        .header h1 {
            font-size: 3.2rem; /* Larger title */
            font-weight: 800;
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            animation: fadeInDown 1s ease-out;
        }
        .header p {
            font-size: 1.3rem; /* Larger subtitle */
            font-weight: 400;
            margin-top: 15px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
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
        
        /* Info Card */
        .info-card {
            border-left: 5px solid var(--primary-green);
            background-color: #e6f7ef; /* Lighter green hint */
            padding: 35px;
            animation: fadeIn 1s ease-out;
        }
        .info-card h4 {
            font-weight: 700; /* Bolder title */
            color: var(--primary-green);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 1.8rem;
        }
        .info-card h4 i {
            margin-right: 15px;
            font-size: 1.5rem;
            color: var(--accent-yellow);
            animation: bounceIn 1s ease-out;
        }
        .info-card ul {
            padding-left: 25px;
            margin-bottom: 0;
            font-size: 1rem;
            color: var(--text-dark);
        }
        .info-card li {
            margin-bottom: 10px;
            position: relative;
        }
        .info-card li::before { /* Custom bullet point */
            content: "\f00c"; /* check-circle icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: var(--secondary-green);
            position: absolute;
            left: -20px;
            top: 2px;
            font-size: 0.8em;
        }
        
        /* Form Card */
        .form-card h4 {
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 30px;
            font-size: 2rem;
            text-align: center;
            position: relative;
        }
        .form-card h4::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--accent-yellow);
            border-radius: 2px;
        }
        .form-group label {
            font-weight: 600; /* Bolder label */
            color: var(--text-dark);
            margin-bottom: 10px;
            display: block;
        }
        .form-control, .form-select {
            border-radius: 15px; /* More rounded */
            padding: 14px 20px; /* Increased padding */
            border: 1px solid #dcdcdc;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); /* Subtle inset shadow */
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25), inset 0 1px 3px rgba(0,0,0,0.08);
            background-color: #fafffc;
        }
        .btn-submit {
            background: linear-gradient(45deg, var(--accent-yellow), #ffda47); /* Brighter yellow gradient */
            color: var(--text-dark);
            border: none;
            padding: 18px 30px; /* Larger button */
            border-radius: 50px;
            font-weight: 700; /* Bolder text */
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.5); /* Stronger shadow */
            letter-spacing: 0.5px;
            font-size: 1.1rem;
        }
        .btn-submit:hover {
            background: linear-gradient(45deg, #e6b800, #e6c500); /* Slightly darker on hover */
            transform: translateY(-5px); /* More lift */
            box-shadow: 0 12px 35px rgba(255, 193, 7, 0.7);
            color: var(--text-dark); /* Keep text dark for contrast */
        }
        
        /* Modal Customization */
        .modal-content {
            border-radius: 25px;
            box-shadow: 0 15px 45px var(--shadow-medium);
            border: none;
            padding: 20px;
        }
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modal-body .icon {
            font-size: 4rem; /* Larger icon */
            color: var(--primary-green);
            animation: popIn 0.5s ease-out;
        }
        .modal-title {
            font-weight: 700;
            color: var(--primary-green);
            font-size: 2rem;
            margin-top: 15px;
        }
        .modal-body p {
            color: var(--text-dark);
            font-size: 1rem;
        }
        .modal-body b {
            color: var(--primary-green);
        }
        .modal-footer {
            border-top: none;
            padding-top: 0;
            justify-content: center;
            gap: 15px;
        }
        .modal-footer .btn {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .modal-footer .btn-secondary {
            background-color: #ccc;
            border-color: #ccc;
            color: var(--text-dark);
        }
        .modal-footer .btn-secondary:hover {
            background-color: #b0b0b0;
            border-color: #b0b0b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .modal-footer .btn-success {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        .modal-footer .btn-success:hover {
            background-color: #28844e;
            border-color: #28844e;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(46, 139, 87, 0.4);
        }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes bounceIn {
            0% { transform: scale(0.1); opacity: 0; }
            60% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }
        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .desktop-navbar { display: none; }
            .mobile-bottom-nav { display: flex; }
            
            .header {
                padding: 60px 15px 40px;
                border-bottom-left-radius: 50px;
                border-bottom-right-radius: 50px;
                margin-bottom: -30px;
            }
            .header h1 { font-size: 2.5rem; }
            .header p { font-size: 1rem; }
            
            .main-container { padding: 10px; }
            
            .saldo-card { padding: 30px; }
            .saldo-card .total-amount { font-size: 3.8rem; }
            .saldo-card .total-amount::before {
                font-size: 0.38em;
                margin-right: 5px;
                transform: translateY(-5px);
            }
            .saldo-card h2 { font-size: 1rem; }
            
            .info-card, .form-card { padding: 25px; border-radius: 20px; }
            .info-card h4 { font-size: 1.5rem; }
            .info-card h4 i { font-size: 1.2rem; }
            .info-card ul { font-size: 0.9rem; }
            .info-card li::before { left: -18px; }

            .form-card h4 { font-size: 1.6rem; margin-bottom: 25px; }
            .form-group label { font-size: 0.95rem; }
            .form-control, .form-select { padding: 10px 15px; border-radius: 10px; }
            .btn-submit { padding: 14px 25px; font-size: 1rem; border-radius: 40px; }

            .modal-body .icon { font-size: 3rem; }
            .modal-title { font-size: 1.5rem; }
            .modal-footer .btn { padding: 10px 20px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark desktop-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-recycle me-2"></i>Bank Sampah</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'beranda.php' ? 'active' : ''); ?>" href="beranda.php"><i class="fas fa-home me-1"></i>Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'harga.php' ? 'active' : ''); ?>" href="harga.php"><i class="fas fa-recycle me-1"></i>Setor Sampah</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'saldo.php' ? 'active' : ''); ?>" href="saldo.php"><i class="fas fa-money-bill-wave me-1"></i>Penarikan</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'history.php' ? 'active' : ''); ?>" href="history.php"><i class="fas fa-history me-1"></i>History</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>" href="profile.php"><i class="fas fa-user me-1"></i>Akun</a></li>
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
    <h1>Tarik Saldo Anda</h1>
    <p>Cairkan hasil jerih payah Anda dalam menjaga lingkungan di Bank Sampah Banguntapan. Mudah, cepat, dan aman!</p>
</div>

<div class="main-container">
    <div class="main-content">
        <div class="saldo-card animate__animated animate__fadeInUp">
            <h2>Saldo Anda Saat Ini</h2>
            <p class="total-amount"><?php echo number_format($total_saldo, 2, ',', '.'); ?></p>
        </div>

        <div class="info-card animate__animated animate__fadeIn">
            <h4><i class="fas fa-exclamation-triangle"></i> Penting! Harap Perhatikan</h4>
            <ul>
                <li>Pastikan Nomor Telepon yang Anda masukkan terdaftar dan sesuai dengan akun e-wallet (GoPay/Dana) Anda.</li>
                <li>Jumlah minimal penarikan adalah Rp 1.000.</li>
                <li>Proses pencairan dana akan dilakukan dalam 1-3 hari kerja. Kami akan segera memprosesnya!</li>
                <li>Kami tidak bertanggung jawab atas kesalahan pengisian data (Nomor Telepon/Nama Pemilik) yang menyebabkan dana tidak sampai. Mohon periksa kembali!</li>
            </ul>
        </div>

        <div class="form-card animate__animated animate__fadeIn">
            <h4 class="mb-4">Formulir Penarikan Saldo</h4>
            <form action="saldo.php" method="POST" id="withdrawalForm">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="jumlah_tarik">Jumlah Penarikan (Rp)</label>
                            <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" required min="1000" placeholder="Contoh: 50000">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="bank_tujuan">Pilih E-wallet Tujuan</label>
                            <select class="form-select" id="bank_tujuan" name="bank_tujuan" required>
                                <option selected disabled value="">Pilih...</option>
                                <option value="Dana">Dana</option>
                                <option value="GoPay">GoPay</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="nomor_rekening">Nomor Telepon E-wallet</label>
                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" required placeholder="Cth: 08123456789">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="nama_pemilik">Nama Pemilik Akun E-wallet</label>
                            <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" required placeholder="Nama Lengkap Sesuai Akun">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-submit mt-5" id="submitBtn">Ajukan Penarikan Sekarang!</button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="icon-container mb-3">
                    <i class="fas fa-check-circle icon"></i>
                </div>
                <h5 class="modal-title mb-4" id="confirmModalLabel">Konfirmasi Penarikan Anda</h5>
                <p>Harap periksa kembali detail penarikan Anda:</p>
                <div class="text-start my-4" style="max-width: 300px; margin: auto;">
                    <p class="mb-2"><b>Jumlah Penarikan:</b> <span id="modal-jumlah"></span></p>
                    <p class="mb-2"><b>E-wallet Tujuan:</b> <span id="modal-ewallet"></span></p>
                    <p class="mb-2"><b>Nomor Telepon:</b> <span id="modal-rekening"></span></p>
                    <p class="mb-2"><b>Nama Pemilik:</b> <span id="modal-pemilik"></span></p>
                </div>
                <p class="mt-4">Apakah Anda yakin dengan semua informasi di atas?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-success" id="confirmWithdrawalBtn">Konfirmasi & Tarik</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('withdrawalForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit langsung

        // Ambil data form
        const jumlah = document.getElementById('jumlah_tarik').value;
        const bank = document.getElementById('bank_tujuan').value;
        const rekening = document.getElementById('nomor_rekening').value;
        const pemilik = document.getElementById('nama_pemilik').value;

        // Update konten modal
        document.getElementById('modal-jumlah').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(jumlah);
        document.getElementById('modal-ewallet').textContent = bank;
        document.getElementById('modal-rekening').textContent = rekening;
        document.getElementById('modal-pemilik').textContent = pemilik;

        // Tampilkan modal
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    });

    document.getElementById('confirmWithdrawalBtn').addEventListener('click', function() {
        document.getElementById('withdrawalForm').submit();
    });

    // Tampilkan SweetAlert setelah halaman dimuat jika ada pesan
    <?php if ($message): ?>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '<?php echo $message_type === "success" ? "Berhasil!" : "Gagal!"; ?>',
            text: '<?php echo $message; ?>',
            icon: '<?php echo $message_type; ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#2e8b57'
        });
    });
    <?php endif; ?>
</script>

</body>
</html>