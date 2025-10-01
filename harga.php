<?php
session_start();
include "conn.php";

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

// Ambil saldo nasabah
$query_saldo = mysqli_query($conn, "SELECT total_saldo FROM saldo WHERE user_id='$user_id' LIMIT 1");
$data_saldo = mysqli_fetch_assoc($query_saldo);
$saldo = $data_saldo['total_saldo'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Sampah - Setor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-green: #2e8b57;
            --secondary-green: #3cb371;
            --accent-yellow: #ffc107;
            --bg-light: #f8fffe;
            --card-background: #ffffff;
            --text-dark: #333333;
            --text-light: #777777;
            --white: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --gradient-main: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            --gradient-card: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #e8f5e8 0%, #f0fff0 100%);
            color: var(--text-dark);
            padding-top: 60px;
            padding-bottom: 70px;
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .main-container {
            max-width: 1200px;
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

        /* Saldo Card */
        .saldo-card {
            background: var(--gradient-card);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px var(--shadow-light);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .saldo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-main);
        }
        .saldo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px var(--shadow-medium);
        }
        .saldo-card .card-body {
            padding: 25px;
        }
        .saldo-title {
            color: var(--primary-green);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .saldo-title i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        .saldo-amount {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
        }

        /* Main Layout */
        .layout-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        /* Kamera Container */
        .camera-container {
            background: var(--gradient-card);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px var(--shadow-light);
            position: relative;
            overflow: hidden;
        }
        .camera-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--accent-yellow));
        }
        .camera-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .camera-title i {
            margin-right: 10px;
            color: var(--primary-green);
        }

        #video-container {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background: #f8f9fa;
            border: 3px solid #e9ecef;
            margin-bottom: 20px;
        }
        #video {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }
        #overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Nota Container */
        .nota-container {
            background: var(--gradient-card);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px var(--shadow-light);
            position: relative;
            overflow: hidden;
            height: fit-content;
        }
        .nota-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-yellow), var(--primary-green));
        }
        .nota-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nota-title i {
            margin-right: 10px;
            color: var(--primary-green);
        }

        .nota-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .nota-table th {
            background: var(--gradient-main);
            color: white;
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nota-table td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #f1f3f4;
            font-weight: 500;
            background: white;
            transition: background-color 0.3s ease;
        }
        .nota-table tr:hover td {
            background: #f8f9fa;
        }
        .nota-table tr:last-child td {
            border-bottom: none;
        }

        .nota-total {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
            margin-top: 15px;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .custom-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 140px;
            position: relative;
            overflow: hidden;
        }
        .custom-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .custom-btn:hover::before {
            left: 100%;
        }
        .btn-primary-custom {
            background: var(--gradient-main);
            color: white;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.4);
        }
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--accent-yellow), #ffb300);
            color: var(--text-dark);
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }
        .btn-warning-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        }

        /* Log container (hidden by default) */
        #log {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary-green);
            max-height: 150px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .layout-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .desktop-navbar { display: none; }
            .mobile-bottom-nav { display: flex; }
            body { padding-top: 0; }
            
            .main-container {
                padding: 15px 10px;
            }
            
            .camera-container,
            .nota-container {
                padding: 20px;
            }
            
            #video {
                height: 250px;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: center;
            }
            .custom-btn {
                width: 100%;
                max-width: 280px;
            }
            
            .saldo-amount {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .camera-container,
            .nota-container {
                padding: 15px;
            }
            
            #video {
                height: 200px;
            }
            
            .nota-table th,
            .nota-table td {
                padding: 10px 6px;
                font-size: 0.85rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
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

<div class="container-fluid main-container">
    <!-- Saldo Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card saldo-card fade-in">
                <div class="card-body text-center">
                    <h5 class="saldo-title justify-content-center">
                        <i class="fas fa-wallet"></i>Saldo Anda
                    </h5>
                    <h2 class="saldo-amount" id="saldo-text">
                        <?= "Rp " . number_format($saldo, 2, ",", "."); ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="layout-container">
        <!-- Kamera Container -->
        <div class="camera-container fade-in">

            <div id="video-container">
                <video id="video" autoplay muted playsinline></video>
                <div id="overlay">Status: Menunggu inisialisasi...</div>
            </div>
            
            <div class="btn-container">
                <button id="startBtn" class="custom-btn btn-primary-custom" disabled>
                    <i class="fas fa-play me-2"></i>Mulai Menghitung
                </button>
                <button id="resetBtn" class="custom-btn btn-warning-custom" style="display: none;">
                    <i class="fas fa-redo me-2"></i>Reset Hitungan
                </button>
            </div>
            
            
        </div>

        <!-- Nota Container -->
        <div class="nota-container fade-in">
            <h4 class="nota-title">
                <i class="fas fa-receipt"></i>Nota Transaksi
            </h4>
            <table class="nota-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-tag me-1"></i>Nama</th>
                        <th><i class="fas fa-coins me-1"></i>Harga Satuan</th>
                        <th><i class="fas fa-sort-numeric-up me-1"></i>Jumlah</th>
                        <th><i class="fas fa-calculator me-1"></i>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-wine-bottle me-2 text-primary"></i>Botol</td>
                        <td>Rp 200</td>
                        <td><span id="jumlah-botol" class="badge bg-primary">0</span></td>
                        <td>Rp <span id="total-botol">0</span></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-box me-2 text-warning"></i>Kaleng</td>
                        <td>Rp 500</td>
                        <td><span id="jumlah-kaleng" class="badge bg-warning">0</span></td>
                        <td>Rp <span id="total-kaleng">0</span></td>
                    </tr>
                </tbody>
            </table>
            <div class="nota-total pulse">
                <i class="fas fa-money-check-alt me-2"></i>
                Grand Total: Rp<span id="grand-total">0</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8/dist/teachablemachine-image.min.js"></script>

<script>
// ===================== KONFIGURASI =====================
const CONFIG = {
<<<<<<< Updated upstream
    modelUrl: './model3/model.json',
    metadataUrl: './model3/metadata.json',    
=======
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/aZOI9yE9A/model.json',
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/W7rqkR7Lb/model.json',
    modelUrl: 'https://teachablemachine.withgoogle.com/models/g1ZyqsfqU/model.json',
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/qvcDkI6k6/model.json',
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/T0j-Kommf/model.json',
    //  modelUrl: 'https://teachablemachine.withgoogle.com/models/T0j-Kommf/model.json',
>>>>>>> Stashed changes
    detectionThreshold: 0.8,    // Minimal confidence 80%
    detectionInterval: 700,    // Interval minimal deteksi (4 detik)
    classIndexKosong: 2,
    classIndexBottle: 0,
    classIndexKaleng: 1,
    wemosBase: 'http://172.17.91.23' // <-- GANTI ke IP Wemos Anda

};

// ===================== VARIABEL APLIKASI =====================
let appState = {
    model: null,
    video: null,
    isDetecting: false,
    totalBottles: 0,
    totalKaleng: 0,
    lastDetectionTimeBottle: 0,
    lastDetectionTimeKaleng: 0,
    lastDetectionTimeKosong: 0,
    stableFramesNeeded: 2,
    stableBottle: 0,
    stableKaleng: 0,
    stableKosong: 0,
    hargaBotol: 200,
    hargaKaleng: 500
};

// ===================== INISIALISASI ELEMEN UI =====================
const UI = {
    startBtn: document.getElementById('startBtn'),
    resetBtn: document.getElementById('resetBtn'),
    jumlahBotol: document.getElementById('jumlah-botol'),
    jumlahKaleng: document.getElementById('jumlah-kaleng'),
    totalBotol: document.getElementById('total-botol'),
    totalKaleng: document.getElementById('total-kaleng'),
    grandTotal: document.getElementById('grand-total'),
    overlay: document.getElementById('overlay'),
    logElement: document.getElementById('log'),
    video: document.getElementById('video')
};

// ===================== FUNGSI UTILITAS =====================
const utils = {
    addLog: (message) => {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const logEntry = document.createElement('p');
        logEntry.innerHTML = `<span class="text-primary">[${timeString}]</span> ${message}`;
        logEntry.style.margin = '5px 0';
        UI.logElement.insertBefore(logEntry, UI.logElement.firstChild);

        if (UI.logElement.children.length > 20) {
            UI.logElement.removeChild(UI.logElement.lastChild);
        }
        
        // Show log container when there are messages
        UI.logElement.style.display = 'block';
    },

    updateUI: () => {
        const totalBotolHarga = appState.totalBottles * appState.hargaBotol;
        const totalKalengHarga = appState.totalKaleng * appState.hargaKaleng;
        const grandTotalHarga = totalBotolHarga + totalKalengHarga;

        if (UI.jumlahBotol) UI.jumlahBotol.textContent = appState.totalBottles;
        if (UI.jumlahKaleng) UI.jumlahKaleng.textContent = appState.totalKaleng;
        if (UI.totalBotol) UI.totalBotol.textContent = totalBotolHarga.toLocaleString('id-ID');
        if (UI.totalKaleng) UI.totalKaleng.textContent = totalKalengHarga.toLocaleString('id-ID');
        if (UI.grandTotal) UI.grandTotal.textContent = grandTotalHarga.toLocaleString('id-ID');

        // Add animation to updated elements
        [UI.jumlahBotol, UI.jumlahKaleng, UI.totalBotol, UI.totalKaleng, UI.grandTotal].forEach(el => {
            if (el) {
                el.style.transform = 'scale(1.1)';
                setTimeout(() => { el.style.transform = 'scale(1)'; }, 200);
            }
        });
    },

    updateSaldoServerBottle: () => {
        const tambahSaldo = appState.hargaBotol;
        fetch("update_saldo.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `total_saldo=${tambahSaldo}`
        })
        .then(res => res.text())
        .then(data => {
            utils.addLog(`<i class="fas fa-plus text-success"></i> Saldo bertambah Rp ${tambahSaldo.toLocaleString('id-ID')} (${data})`);
            return fetch("get_saldo.php");
        })
        .then(r => r.json())
        .then(json => {
            let saldoFormatted = new Intl.NumberFormat("id-ID", { 
                style: "currency", 
                currency: "IDR" 
            }).format(json.saldo);

            document.getElementById("saldo-text").textContent = saldoFormatted;
            document.querySelector('.saldo-card').classList.add('pulse');
            setTimeout(() => {
                document.querySelector('.saldo-card').classList.remove('pulse');
            }, 2000);
        })
        .catch(err => {
            // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error update saldo: ${err}`);
        });
    },

    updateSaldoServerKaleng: () => {
        const tambahSaldo = appState.hargaKaleng;
        fetch("update_saldo.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `total_saldo=${tambahSaldo}`
        })
        .then(res => res.text())
        .then(data => {
            utils.addLog(`<i class="fas fa-plus text-success"></i> Saldo bertambah Rp ${tambahSaldo.toLocaleString('id-ID')} (${data})`);
            return fetch("get_saldo.php");
        })
        .then(r => r.json())
        .then(json => {
            let saldoFormatted = new Intl.NumberFormat("id-ID", { 
                style: "currency", 
                currency: "IDR" 
            }).format(json.saldo);

            document.getElementById("saldo-text").textContent = saldoFormatted;
            document.querySelector('.saldo-card').classList.add('pulse');
            setTimeout(() => {
                document.querySelector('.saldo-card').classList.remove('pulse');
            }, 2000);
        })
        .catch(err => {
            // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error update saldo: ${err}`);
        });
    }
};

// ===================== FUNGSI SERVO / WEMOS =====================
const wemos = {
    servo: async (pos) => {
        try {
            const url = `${CONFIG.wemosBase}/servo?pos=${pos}`;
            await fetch(url, { mode: 'cors' });
            console.log(`Servo digerakkan ke posisi ${pos}°`);
        } catch (e) {
            console.error("Gagal mengirim perintah ke Wemos:", e);
            utils.addLog(`<i class="fas fa-exclamation-triangle text-warning"></i> Gagal mengirim perintah ke Wemos: ${e.message}`);
        }
    }
};

const sleep = (ms) => new Promise(r => setTimeout(r, ms));

(async () => {
    console.log("Inisialisasi: Servo default 90° (diam)");
    await wemos.servo(90);
})();

async function moveRight() {
    console.log("Servo → Kanan (180°)");
    await wemos.servo(180);
    await sleep(1000);
    console.log("Servo kembali ke posisi default (90°)");
    await wemos.servo(90);
}

async function moveLeft() {
    console.log("Servo → Kiri (0°)");
    await wemos.servo(0);
    await sleep(1000);
    console.log("Servo kembali ke posisi default (90°)");
    await wemos.servo(90);
}

async function servoSleep() {
    console.log("Servo → Diam (Tengah)");
    await wemos.servo(90);
}

// ===================== FUNGSI KAMERA =====================
const camera = {
    setup: async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'environment'
                } 
            });
            UI.video.srcObject = stream;
            return new Promise((resolve) => {
                UI.video.onloadedmetadata = () => {
                    resolve(UI.video);
                };
            });
        } catch (error) {
            UI.overlay.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Status: Gagal mengakses kamera';
            // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error: Gagal mengakses kamera - ${error.message}`);
            console.error(error);
            return null;
        }
    }
};

// ===================== FUNGSI MODEL =====================
const model = {
    load: async () => {
        UI.overlay.innerHTML = '<i class="fas fa-spinner fa-spin text-info"></i> Status: Memuat model...';
        // utils.addLog('<i class="fas fa-download text-info"></i> Memulai pemuatan model machine learning');
        try {
appState.model = await tmImage.load(CONFIG.modelUrl, CONFIG.metadataUrl);
            UI.overlay.innerHTML = '<i class="fas fa-check-circle text-success"></i> Status: Model siap. Klik "Mulai Menghitung"';
            // utils.addLog('<i class="fas fa-check text-success"></i> Model berhasil dimuat');
            UI.startBtn.disabled = false;
            UI.startBtn.classList.add('pulse');
        } catch (error) {
            UI.overlay.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Status: Gagal memuat model';
            // utils.addLog(`<i class="fas fa-times text-danger"></i> Error: Gagal memuat model - ${error.message}`);
            console.error(error);
        }
    },

  predict: async () => {
    if (!appState.isDetecting) return;
    if (!appState.model) return;

    try {
        // Ambil frame dari video
        const canvas = document.createElement('canvas');
        canvas.width = UI.video.videoWidth;
        canvas.height = UI.video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(UI.video, 0, 0, canvas.width, canvas.height);

        // Prediksi pakai tmImage
        const predictions = await appState.model.predict(canvas);

        // Debug log semua class
        predictions.forEach(p => {
            console.log(`${p.className}: ${(p.probability * 100).toFixed(2)}%`);
        });

        // Ambil confidence sesuai index di CONFIG
        const bottleConfidence = predictions[CONFIG.classIndexBottle].probability;
        const kalengConfidence = predictions[CONFIG.classIndexKaleng].probability;
        const kosongConfidence = predictions[CONFIG.classIndexKosong].probability;

        // Update overlay
        UI.overlay.innerHTML = 
          `<div class="d-flex justify-content-between">
            <span><i class="fas fa-wine-bottle text-primary"></i> Botol: ${(bottleConfidence * 100).toFixed(1)}%</span>
            <span><i class="fas fa-box text-warning"></i> Kaleng: ${(kalengConfidence * 100).toFixed(1)}%</span>
            <span><i class="fas fa-circle text-secondary"></i> Kosong: ${(kosongConfidence * 100).toFixed(1)}%</span>
          </div>`;

        // ====== LOGIKA DETEKSI ======
        const currentTime = Date.now();

        if (bottleConfidence >= CONFIG.detectionThreshold) {
            appState.stableBottle++;
            appState.stableKaleng = 0;
            appState.stableKosong = 0;

            if (
                appState.stableBottle >= appState.stableFramesNeeded &&
                (currentTime - appState.lastDetectionTimeBottle) > CONFIG.detectionInterval
            ) {
                appState.totalBottles++;
                appState.lastDetectionTimeBottle = currentTime;
                utils.updateUI();
                utils.updateSaldoServerBottle();
                await moveRight();
                appState.stableBottle = 0;
            }
        }
        else if (kalengConfidence >= CONFIG.detectionThreshold) {
            appState.stableKaleng++;
            appState.stableBottle = 0;
            appState.stableKosong = 0;

            if (
                appState.stableKaleng >= appState.stableFramesNeeded &&
                (currentTime - appState.lastDetectionTimeKaleng) > CONFIG.detectionInterval
            ) {
                appState.totalKaleng++;
                appState.lastDetectionTimeKaleng = currentTime;
                utils.updateUI();
                utils.updateSaldoServerKaleng();
                await moveRight();
                appState.stableKaleng = 0;
            }
        }
        else if (kosongConfidence >= CONFIG.detectionThreshold) {
            appState.stableKosong++;
            appState.stableBottle = 0;
            appState.stableKaleng = 0;

            if (
                appState.stableKosong >= appState.stableFramesNeeded &&
                (currentTime - appState.lastDetectionTimeKosong) > CONFIG.detectionInterval
            ) {
                appState.lastDetectionTimeKosong = currentTime;
                await servoSleep();
                appState.stableKosong = 0;
            }
        }
        else {
            // Unknown
            appState.stableBottle = 0;
            appState.stableKaleng = 0;
            appState.stableKosong = 0;

            if (!appState.stableUnknown) appState.stableUnknown = 0;
            appState.stableUnknown++;

            if (
                appState.stableUnknown >= appState.stableFramesNeeded &&
                (currentTime - (appState.lastDetectionTimeUnknown || 0)) > CONFIG.detectionInterval
            ) {
                appState.lastDetectionTimeUnknown = currentTime;
                await moveLeft();
                appState.stableUnknown = 0;
            }
        }

<<<<<<< Updated upstream
        // Loop deteksi
        if (appState.isDetecting) {
            setTimeout(() => model.predict(), 600);
=======
        try {
            const canvas = document.createElement('canvas');
            canvas.width = UI.video.videoWidth;
            canvas.height = UI.video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(UI.video, 0, 0, canvas.width, canvas.height);

            const img = tf.browser.fromPixels(canvas);
            const resized = tf.image.resizeBilinear(img, [224, 224]);
            const tensor = resized.expandDims(0);
            const normalized = tensor.div(255.0);

            const predictions = await appState.model.predict(normalized).data();
            const bottleConfidence = predictions[CONFIG.classIndexBottle] || 0;
            const kalengConfidence = predictions[CONFIG.classIndexKaleng] || 0;
            const kosongConfidence = predictions[CONFIG.classIndexKosong] || 0;

            UI.overlay.innerHTML = 
              `<div class="d-flex justify-content-between">
                <span><i class="fas fa-wine-bottle text-primary"></i> Botol: ${(bottleConfidence * 100).toFixed(1)}%</span>
                <span><i class="fas fa-box text-warning"></i> Kaleng: ${(kalengConfidence * 100).toFixed(1)}%</span>
                <span><i class="fas fa-circle text-secondary"></i> Kosong: ${(kosongConfidence * 100).toFixed(1)}%</span>
              </div>`;

            const currentTime = Date.now();

            if (bottleConfidence >= 0.8 && kosongConfidence <= 1.0) {
                appState.stableBottle++;
                appState.stableKaleng = 0;
                appState.stableKosong = 0;

                if (
                    appState.stableBottle >= appState.stableFramesNeeded &&
                    (currentTime - appState.lastDetectionTimeBottle) > CONFIG.detectionInterval
                ) {
                    appState.totalBottles++;
                    appState.lastDetectionTimeBottle = currentTime;
                    utils.updateUI();
                    // utils.addLog(
                    //     `<i class="fas fa-wine-bottle text-primary"></i> <strong>Botol terdeteksi!</strong> Total: ${appState.totalBottles} (${(bottleConfidence * 100).toFixed(1)}%)`
                    // );

                    utils.updateSaldoServerBottle();
                    await moveRight();
                    appState.stableBottle = 0;
                }
            }
            else if (kalengConfidence >= 0.82 && kosongConfidence <= 1.0) {
                appState.stableKaleng++;
                appState.stableBottle = 0;
                appState.stableKosong = 0;

                if (
                    appState.stableKaleng >= appState.stableFramesNeeded &&
                    (currentTime - appState.lastDetectionTimeKaleng) > CONFIG.detectionInterval
                ) {
                    appState.totalKaleng++;
                    appState.lastDetectionTimeKaleng = currentTime;
                    utils.updateUI();
                    // utils.addLog(
                    //     `<i class="fas fa-box text-warning"></i> <strong>Kaleng terdeteksi!</strong> Total: ${appState.totalKaleng} (${(kalengConfidence * 100).toFixed(1)}%)`
                    // );

                    utils.updateSaldoServerKaleng();
                    await moveRight();
                    appState.stableKaleng = 0;
                }
            }

            else if (kosongConfidence >= 0.85 && kosongConfidence <= 1.0) {
                appState.stableKosong++;
                appState.stableBottle = 0;
                appState.stableKaleng = 0;

                if (
                    appState.stableKosong >= appState.stableFramesNeeded &&
                    (currentTime - appState.lastDetectionTimeKosong) > CONFIG.detectionInterval
                ) {
                    appState.lastDetectionTimeKosong = currentTime;
                    // utils.addLog(`<i class="fas fa-circle text-secondary"></i> Kosong terdeteksi (${(kosongConfidence * 100).toFixed(1)}%)`);
                    await servoSleep();
                    appState.stableKosong = 0;
                }
            }
            else {
                appState.stableBottle = 0;
                appState.stableKaleng = 0;
                appState.stableKosong = 0;

                if (!appState.stableUnknown) appState.stableUnknown = 0;
                appState.stableUnknown++;

                if (
                    appState.stableUnknown >= appState.stableFramesNeeded &&
                    (currentTime - (appState.lastDetectionTimeUnknown || 0)) > CONFIG.detectionInterval
                ) {
                    appState.lastDetectionTimeUnknown = currentTime;
                    // utils.addLog('<i class="fas fa-question-circle text-info"></i> Gambar tidak jelas, servo ke kiri');
                    await moveLeft();
                    appState.stableUnknown = 0;
                }
            }

            tf.dispose([img, resized, tensor, normalized]);

            if (appState.isDetecting) {
                setTimeout(() => model.predict(), 600);
            }

        } catch (error) {
            // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error saat prediksi: ${error.message}`);
            console.error(error);
            controls.stopDetection();
>>>>>>> Stashed changes
        }

    } catch (error) {
        console.error("Error saat prediksi:", error);
        controls.stopDetection();
    }
}
}

// ===================== FUNGSI KONTROL =====================
const controls = {
    startDetection: () => {
        if (!appState.model) {
            // utils.addLog('<i class="fas fa-exclamation-triangle text-warning"></i> Model belum dimuat, tunggu hingga model siap');
            return;
        }
        appState.isDetecting = true;
        UI.startBtn.innerHTML = '<i class="fas fa-stop me-2"></i>Hentikan Deteksi';
        UI.startBtn.classList.remove('btn-primary-custom', 'pulse');
        UI.startBtn.classList.add('btn-warning-custom');
        UI.overlay.innerHTML = '<i class="fas fa-eye text-success"></i> Status: Sedang mendeteksi...';
        // utils.addLog('<i class="fas fa-play text-success"></i> <strong>Memulai deteksi</strong>');
        model.predict();
    },

    stopDetection: () => {
        appState.isDetecting = false;
        UI.startBtn.innerHTML = '<i class="fas fa-play me-2"></i>Mulai Menghitung';
        UI.startBtn.classList.remove('btn-warning-custom');
        UI.startBtn.classList.add('btn-primary-custom');
        UI.overlay.innerHTML = '<i class="fas fa-pause text-warning"></i> Status: Deteksi dihentikan';
        // utils.addLog('<i class="fas fa-stop text-warning"></i> <strong>Deteksi dihentikan oleh pengguna</strong>');

        // Simpan transaksi ke server
        if (appState.totalBottles > 0 || appState.totalKaleng > 0) {
            fetch("save_transaksi.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `total_bottle=${appState.totalBottles}&total_kaleng=${appState.totalKaleng}`
            })
            .then(res => res.text())
            .then(data => {
                if (data === "OK") {
                    // utils.addLog('<i class="fas fa-save text-success"></i> <strong>Transaksi tersimpan ke database</strong>');
                    // Show success animation
                    document.querySelector('.nota-container').classList.add('pulse');
                    setTimeout(() => {
                        document.querySelector('.nota-container').classList.remove('pulse');
                    }, 2000);
                } else if (data === "NO_DATA") {
                    // utils.addLog('<i class="fas fa-info-circle text-info"></i> Tidak ada setoran, transaksi tidak disimpan');
                } else {
                    // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Gagal menyimpan transaksi: ${data}`);
                }
            }).catch(err => {
                // utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Gagal menyimpan transaksi: ${err}`);
            });
        }
    },

    resetCounter: () => {
        appState.totalBottles = 0;
        appState.totalKaleng = 0;
        utils.updateUI();
        // utils.addLog('<i class="fas fa-redo text-info"></i> <strong>Hitungan direset ke 0</strong>');
        
        // Add reset animation
        document.querySelector('.nota-container').style.transform = 'scale(0.95)';
        setTimeout(() => {
            document.querySelector('.nota-container').style.transform = 'scale(1)';
        }, 150);
    }
};

// ===================== EVENT LISTENERS =====================
UI.startBtn.addEventListener('click', () => {
    if (appState.isDetecting) {
        controls.stopDetection();
    } else {
        controls.startDetection();
    }
});

UI.resetBtn.addEventListener('click', () => {
    if (confirm('Apakah Anda yakin ingin mereset hitungan?')) {
        controls.resetCounter();
    }
});

// ===================== INISIALISASI APLIKASI =====================
const initApp = async () => {
    UI.startBtn.disabled = true;
    UI.resetBtn.disabled = false;

    // utils.addLog('<i class="fas fa-rocket text-primary"></i> <strong>Inisialisasi aplikasi...</strong>');
    
    await camera.setup();
    await model.load();

    if (UI.video.srcObject) {
        UI.video.play();
        UI.overlay.innerHTML = '<i class="fas fa-check-circle text-success"></i> Status: Kamera siap. Klik "Mulai Menghitung"';
        // utils.addLog('<i class="fas fa-check text-success"></i> <strong>Kamera berhasil diinisialisasi</strong>');
    }
};

// Add smooth transitions
document.addEventListener('DOMContentLoaded', () => {
    // Add fade-in animation to elements
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
    });
});

window.onload = initApp;
</script>
</body>
</html>