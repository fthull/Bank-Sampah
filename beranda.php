<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "bank_sampah";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data user dari database
$query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE id='$user_id' LIMIT 1");
$data_user = mysqli_fetch_assoc($query);

$nama_lengkap = $data_user['nama_lengkap'] ?? '';

// Tentukan nama yang ditampilkan
$nama_tampil = !empty($nama_lengkap) ? $nama_lengkap : $username;


// === BAGIAN YANG DIUBAH UNTUK MENGAMBIL SALDO DARI TABEL 'saldo' ===
// Ambil data saldo langsung dari tabel `saldo`
$sql = "SELECT total_saldo FROM saldo WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_saldo_raw = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_saldo_raw = $row['total_saldo'];
}
// ===================================================================

$stmt->close();
$conn->close();

// Logika untuk menampilkan alert dari URL
$message = '';
$message_type = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    
    switch ($status) {
        case 'success':
            $message = isset($_GET['amount']) ? "Penarikan sebesar Rp " . number_format($_GET['amount'], 2, ',', '.') . " berhasil diajukan." : "Transaksi berhasil.";
            $message_type = "success";
            break;
        case 'insufficient_balance':
            $message = 'Maaf, saldo Anda tidak cukup.';
            $message_type = "error";
            break;
        case 'error':
            $message = 'Terjadi kesalahan saat melakukan transaksi. Silakan coba lagi.';
            $message_type = "error";
            break;
        case 'success_setor':
            $message = 'Setor sampah berhasil!';
            $message_type = "success";
            break;
        default:
            $message = 'Transaksi berhasil.';
            $message_type = "success";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Bank Sampah</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .container {
            max-width: 960px;
            margin: auto;
            padding: 20px 15px;
        }

        /* Desktop Navbar */
        .desktop-navbar {
            background: var(--gradient-main);
            box-shadow: 0 2px 10px var(--shadow-medium);
            position: fixed; /* Membuat navbar tetap di tempat */
            top: 0; /* Menempatkan navbar di bagian atas */
            width: 100%; /* Memastikan navbar penuh lebar */
            z-index: 1000; /* Menempatkan navbar di atas elemen lain */
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
            display: none;
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
        
        /* Header / Hero Section */
        .header {
            background: var(--gradient-main);
            color: var(--white);
            padding: 70px 20px 100px;
            text-align: center;
            border-bottom-left-radius: 80px;
            border-bottom-right-radius: 80px;
            position: relative;
            z-index: 0;
            box-shadow: 0 8px 25px var(--shadow-medium);
            overflow: hidden;
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
            animation: fadeInDown 1s ease-out;
        }
        .header p {
            font-size: 1.3rem;
            font-weight: 400;
            margin: 15px auto 0;
            max-width: 600px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
        }
        
        /* Saldo Card */
        .saldo-card {
            background: var(--card-background);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 15px 40px var(--shadow-medium);
            text-align: center;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            margin-top: -70px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(0,0,0,0.05);
            background: linear-gradient(145deg, var(--card-background), #f8f8f8);
        }
        .saldo-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 60px var(--shadow-medium);
        }
        .saldo-card h2 {
            margin: 0 0 15px 0;
            color: var(--text-light);
            font-size: 1.3rem;
            font-weight: 500;
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
            content: 'Rp';
            font-size: 0.4em;
            font-weight: 600;
            position: relative;
            margin-right: 8px;
            line-height: 1;
            color: var(--secondary-green);
            transform: translateY(-8px);
        }

        /* Action Buttons */
        .action-buttons-container {
            margin-top: 30px;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }
        .action-buttons a {
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, #ffd700, var(--accent-yellow));
            color: var(--text-dark);
            text-decoration: none;
            border: none;
            padding: 16px 35px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }
        .action-buttons a:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.6);
            color: var(--text-dark);
        }
        .action-buttons a i {
            font-size: 1.2em;
            color: rgba(0,0,0,0.3);
            transition: color 0.3s;
        }
        .action-buttons a:hover i {
            color: var(--text-dark);
        }

        /* Call to Action */
        .call-to-action {
            margin-top: 40px;
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
            color: var(--white);
            padding: 45px;
            border-radius: 30px;
            text-align: center;
            box-shadow: 0 12px 30px var(--shadow-medium);
            position: relative;
            overflow: hidden;
            animation: fadeIn 1.2s ease-out;
        }
        .call-to-action::before {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(-30deg);
        }
        .call-to-action h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }
        .call-to-action p {
            font-size: 1.2rem;
            line-height: 1.7;
            font-weight: 300;
            max-width: 700px;
            margin: 15px auto 0;
        }

        /* Fact & Education Sections */
        .fact-section, .edu-section {
            margin-top: 40px;
            padding: 45px;
            background: var(--card-background);
            border-radius: 30px;
            box-shadow: 0 12px 30px var(--shadow-light);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .fact-section:hover, .edu-section:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 45px var(--shadow-medium);
        }
        .fact-section h3, .edu-section h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-green);
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
            text-align: center;
        }
        .fact-section h3::after, .edu-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-yellow);
            border-radius: 2px;
        }
        .fact-card {
            background-color: var(--bg-light);
            padding: 30px;
            border-radius: 25px;
            text-align: center;
            font-weight: 600;
            color: var(--primary-green);
            font-size: 1.1rem;
            line-height: 1.8;
            border: 1px dashed rgba(46, 204, 113, 0.4);
        }
        .fact-card p {
            margin: 0;
            color: var(--text-dark);
        }

        /* Education Grid */
        .edu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .edu-card {
            background-color: var(--bg-light);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px var(--shadow-light);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-top: 5px solid var(--primary-green);
        }
        .edu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px var(--shadow-medium);
        }
        .edu-card .edu-icon {
            font-size: 3rem;
            color: var(--primary-green);
            margin: 10px 0 20px;
        }
        .edu-card .content h4 {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1.5;
            color: var(--primary-green);
            margin-bottom: 10px;
        }
        .edu-card .content p {
            font-size: 0.95rem;
            color: var(--text-light);
            margin: 0;
        }
        
        /* Gaya untuk chart */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
.footer {
            background: #fafffaff;
            color: #333;
            padding: 55px 20px 30px;
            /* height: 5px; */
            text-align: center;
        }
        .footer-top {
            margin-bottom: 40px;
        }
        .footer-logo {
            width: 110px;
            margin-bottom: 10px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        .footer h3 {
            font-size: 28px;
            margin: 5px 0 10px;
            font-family: 'Montserrat', sans-serif;
            color: #1f6e2f;
        }
        .footer-desc {
            font-size: 15px;
            color: #555;
            max-width: 550px;
            margin: auto;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 50px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer-col {
            flex: 1;
            min-width: 250px;
            text-align: left;
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .footer-col:hover {
            transform: translateY(-5px);
        }
        .footer h4 {
            font-size: 20px;
            margin-bottom: 25px;
            color: #28a745;
            position: relative;
        }
        .footer h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 40px;
            height: 3px;
            background: #28a745;
            border-radius: 2px;
        }
        .footer ul {
            list-style: none;
            padding: 0;
        }
        .footer ul li {
            margin-bottom: 15px;
        }
        .footer ul li a {
            text-decoration: none;
            color: #333;
            font-size: 15px;
            transition: color 0.3s;
            display: inline-block;
        }
        .footer ul li a:hover {
            color: #28a745;
        }
        .footer p {
            font-size: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer .socials {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .footer .socials a {
            width: 40px;
            height: 40px;
            background: #28a745;
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        .footer .socials a:hover {
            background: #1e7e34;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
                .footer-bottom {
            border-top: 1px solid #dcdcdc;
            padding-top: 20px;
            font-size: 14px;
            color: #888;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .footer-bottom p {
            margin: 0;
        }

        /* Animasi */
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

        /* Media Queries untuk Responsif */
        @media (max-width: 768px) {
            .desktop-navbar { display: none; }
            .mobile-bottom-nav { display: flex; }

            .header {
                padding: 60px 15px 80px;
                border-bottom-left-radius: 50px;
                border-bottom-right-radius: 50px;
            }
            .header h1 { font-size: 2.2rem; }
            .header p { font-size: 1rem; margin-top: 10px; }

            .saldo-card {
                margin-top: -60px;
                padding: 30px;
                border-radius: 20px;
            }
            .saldo-card .total-amount { font-size: 3.5rem; }
            .saldo-card .total-amount::before {
                font-size: 0.35em;
                margin-right: 3px;
                transform: translateY(-3px);
            }
            .saldo-card h2 { font-size: 1.1rem; }

            .action-buttons {
                gap: 15px;
            }
            .action-buttons a {
                padding: 12px 25px;
                font-size: 1rem;
            }
            .action-buttons a i {
                font-size: 1em;
            }

            .call-to-action {
                padding: 30px;
                border-radius: 20px;
            }
            .call-to-action h3 { font-size: 1.8rem; }
            .call-to-action p { font-size: 1rem; }

            .fact-section, .edu-section {
                padding: 30px;
                border-radius: 20px;
            }
            .fact-section h3, .edu-section h3 { font-size: 1.6rem; margin-bottom: 25px; }
            .fact-card { font-size: 1rem; padding: 25px; }

            .edu-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .edu-card { padding: 20px; }
            .edu-card .edu-icon { font-size: 2.5rem; }
            .edu-card .content h4 { font-size: 1.1rem; }
            .edu-card .content p { font-size: 0.9rem; }

            .footer-col {
                /* Perbaikan: Mengatur min-width agar card footer lebih besar */
                min-width: 80%;
                text-align: center;
            }
            .footer-col h4::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .footer p, .footer .socials {
                justify-content: center;
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
        <h1>Selamat Datang Kembali, <?php echo htmlspecialchars($nama_tampil); ?>!</h1>
        <p>Mari jadikan sampahmu sebagai tabungan masa depan di Bank Sampah Banguntapan.</p>
    </div>

    <div class="container">
        <div class="saldo-card">
            <h2>Saldo Anda Saat Ini</h2>
            <p class="total-amount"><?php echo number_format($total_saldo_raw, 2, ',', '.'); ?></p>
            <div class="action-buttons-container">
                <div class="action-buttons">
                    <a href="saldo.php"><i class="fas fa-hand-holding-usd"></i> Tarik Saldo</a>
                    <a href="harga.php"><i class="fas fa-box"></i> Setor Sampah</a>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <div class="card p-4 shadow-sm" style="border-radius: 20px;">
                        <h4 class="card-title text-center" style="color: var(--primary-green); font-weight: 700;">Statistik Harian</h4>
                        <div class="chart-container">
                            <canvas id="dailyStatsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="call-to-action">
            <h3>Sampah Bukan Masalah, Tapi Berkah!</h3>
            <p>
                Di Bank Sampah kami di Banguntapan, setiap upaya kecilmu untuk memilah dan menyetor sampah berarti besar. Sampah plastik dan kaleng bukan sekadar barang bekas, tapi potensi rupiah yang menanti untuk kamu tukarkan.
                Mulai tabung sampahmu hari ini, lihat saldo bertumbuh, dan rasakan kepuasan berkontribusi pada lingkungan yang lebih bersih dan masa depan finansial yang lebih baik!
            </p>
        </div>
        
        <div class="fact-section">
            <h3>Tahukah Kamu?🧐</h3>
            <div class="fact-card">
                <p>Mendaur ulang satu botol plastik dapat menghemat energi yang cukup untuk menyalakan lampu 60 watt selama 6 jam. Sedangkan daur ulang kaleng aluminium bisa menghemat energi hingga 95% dibandingkan membuat kaleng baru! Jadi, setiap kaleng yang kamu setor punya dampak luar biasa!</p>
            </div>
        </div>

        <div class="edu-section">
            <h3>Tips Mengelola Sampah Dengan Baik💡</h3>
            <div class="edu-grid">
                <div class="edu-card">
                    <i class="fas fa-recycle edu-icon"></i>
                    <div class="content">
                        <h4>Mulai Daur Ulang di Rumah</h4>
                        <p>Pisahkan sampah organik dan anorganik. Hal ini memudahkan proses daur ulang dan pengolahan lebih lanjut.</p>
                    </div>
                </div>
                <div class="edu-card">
                    <i class="fas fa-seedling edu-icon"></i>
                    <div class="content">
                        <h4>Mencoba Membuat Kompos Sendiri</h4>
                        <p>Ubah sisa sampah dapur menjadi pupuk yang bermanfaat untuk tanaman Anda, kurangi sampah organik ke TPA.</p>
                    </div>
                </div>
                <div class="edu-card">
                    <i class="fas fa-tags edu-icon"></i>
                    <div class="content">
                        <h4>Ketahui Jenis & Harga Sampah</h4>
                        <p>Pahami jenis-jenis sampah yang bisa ditukarkan menjadi saldo, serta nilai ekonomisnya di Bank Sampah.</p>
                    </div>
                </div>
                <div class="edu-card">
                    <i class="fas fa-shopping-bag edu-icon"></i>
                    <div class="content">
                        <h4>Kurangi Sampah Plastik</h4>
                        <p>Gunakan tas belanja ramah lingkungan, botol minum isi ulang, dan wadah makanan sendiri untuk mengurangi jejak plastikmu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="footer-top">
            <img src="aset/logop.png" alt="Logo Bank Sampah" class="footer-logo">
            <h3>Bank Sampah Indonesia</h3>
            <p class="footer-desc">
                Mengelola sampah jadi lebih bernilai, menuju Indonesia yang hijau dan bersih.
            </p>
        </div>

        <div class="footer-container">
            <div class="footer-col">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#">Beranda</a></li>
                    <li><a href="register.php">Pendaftaran</a></li>
                    <li><a href="#education">Edukasi</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-col" id="contact">
                <h4>Kontak</h4>
                <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
                <p><i class="fas fa-envelope"></i> info@banksampah.id</p>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Merdeka No. 123, Jakarta</p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 Wabi Teknologi Indonesia</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Logika untuk menampilkan SweetAlert
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

    // Logika untuk menampilkan grafik
    document.addEventListener('DOMContentLoaded', function() {
        fetch('datagrafik.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Log data untuk debugging
                console.log("Data diterima:", data);
                if (data.length === 0) {
                    console.log("Tidak ada data untuk ditampilkan.");
                    return;
                }

                // Mengubah tanggal menjadi format hari, tanggal, bulan, dan tahun
                const labels = data.map(item => {
                    const date = new Date(item.tanggal + 'T00:00:00'); 
                    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    return date.toLocaleDateString('id-ID', options);
                });

                const pemasukanData = data.map(item => item.pemasukan);
                const pengeluaranData = data.map(item => item.pengeluaran);

                const ctx = document.getElementById('dailyStatsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pemasukan (Rp)',
                                data: pemasukanData,
                                backgroundColor: '#3cb371'
                            },
                            {
                                label: 'Pengeluaran (Rp)',
                                data: pengeluaranData,
                                backgroundColor: '#ffc107'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 14,
                                        family: 'Montserrat'
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });
</script>
</body>
</html>