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

        /* Model Status */
        .model-status {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .model-status.loading {
            background: #fff3e0;
            border-color: #ff9800;
        }
        .model-status.error {
            background: #ffebee;
            border-color: #f44336;
        }
        .model-status.success {
            background: #e8f5e9;
            border-color: #4caf50;
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

        /* Log container */
        #log {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid var(--primary-green);
            max-height: 150px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            display: none;
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
            
            <!-- Model Status -->
            <div id="model-status" class="model-status loading">
                <i class="fas fa-spinner fa-spin"></i> Memuat model AI...
            </div>

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
            
            <!-- Log container for debugging -->
            <div id="log"></div>
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

<!-- Load TensorFlow.js -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
<script>
// ===================== KONFIGURASI =====================
const CONFIG = {
    // PENTING: Ganti dengan URL model Anda
    modelUrl: './model/model.json',           // Path ke file model lokal
    metadataUrl: './model/metadata.json',     // Path ke metadata (opsional)
    detectionThreshold: 0.8,    
    detectionInterval: 500,    
    classIndexKosong: 2,
    classIndexBottle: 0,
    classIndexKaleng: 1,
    wemosBase: 'http://172.17.91.89' // Ganti dengan IP Wemos Anda
};

// ===================== VARIABEL APLIKASI =====================
let appState = {
    model: null,
    video: null,
    isDetecting: false,
    isModelLoaded: false, // Flag untuk mencegah loading berulang
    isCameraActive: false,
    totalBottles: 0,
    totalKaleng: 0,
    lastDetectionTimeBottle: 0,
    lastDetectionTimeKaleng: 0,
    lastDetectionTimeKosong: 0,
    stableFramesNeeded: 4,
    stableBottle: 0,
    stableKaleng: 0,
    stableKosong: 0,
    hargaBotol: 200,
    hargaKaleng: 500,
    classNames: [],
    detectionLoop: null // Untuk menyimpan referensi interval
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
    video: document.getElementById('video'),
    modelStatus: document.getElementById('model-status')
};

// ===================== FUNGSI UTILITAS =====================
const utils = {
    updateModelStatus: (message, type = 'loading') => {
        const statusEl = UI.modelStatus;
        statusEl.className = `model-status ${type}`;
        
        let icon = 'fas fa-spinner fa-spin';
        if (type === 'success') icon = 'fas fa-check-circle';
        else if (type === 'error') icon = 'fas fa-times-circle';
        
        statusEl.innerHTML = `<i class="${icon}"></i> ${message}`;
        
        // Hide status after success
        if (type === 'success') {
            setTimeout(() => {
                statusEl.style.display = 'none';
            }, 3000);
        }
    },

    addLog: (message) => {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const logEntry = document.createElement('p');
        logEntry.innerHTML = `<span class="text-primary">[${timeString}]</span> ${message}`;
        logEntry.style.margin = '5px 0';
        if (UI.logElement) {
            UI.logElement.insertBefore(logEntry, UI.logElement.firstChild);

            if (UI.logElement.children.length > 20) {
                UI.logElement.removeChild(UI.logElement.lastChild);
            }
            UI.logElement.style.display = 'block';
        }
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

    updateOverlay: (message) => {
        if (UI.overlay) {
            UI.overlay.textContent = `Status: ${message}`;
        }
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
            console.error('Error updating saldo:', err);
            utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error updating saldo: ${err.message}`);
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
            console.error('Error updating saldo:', err);
            utils.addLog(`<i class="fas fa-exclamation-triangle text-danger"></i> Error updating saldo: ${err.message}`);
        });
    }
};

// ===================== FUNGSI MODEL & KAMERA =====================
const modelManager = {
    async loadModel() {
        if (appState.isModelLoaded) {
            utils.addLog('Model sudah dimuat sebelumnya');
            return true;
        }

        try {
            utils.updateModelStatus('Memuat model AI...', 'loading');
            utils.addLog('Memulai proses loading model...');

            // Load model dengan timeout
            const modelPromise = tf.loadLayersModel(CONFIG.modelUrl);
            const timeoutPromise = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Model loading timeout (30s)')), 30000)
            );

            appState.model = await Promise.race([modelPromise, timeoutPromise]);
            
            // Load metadata jika ada
            try {
                if (CONFIG.metadataUrl) {
                    const metadataResponse = await fetch(CONFIG.metadataUrl);
                    const metadata = await metadataResponse.json();
                    appState.classNames = metadata.labels || [];
                    utils.addLog(`Metadata loaded: ${appState.classNames.join(', ')}`);
                }
            } catch (metaError) {
                console.warn('Metadata tidak dapat dimuat:', metaError);
                appState.classNames = ['Botol', 'Kaleng', 'Kosong'];
                utils.addLog('Menggunakan default class names');
            }

            appState.isModelLoaded = true;
            utils.updateModelStatus('Model berhasil dimuat! Siap untuk memulai kamera.', 'success');
            utils.addLog(`<i class="fas fa-check text-success"></i> Model loaded successfully`);
            
            // Enable start button
            if (UI.startBtn) {
                UI.startBtn.disabled = false;
            }

            return true;
        } catch (error) {
            console.error('Error loading model:', error);
            appState.isModelLoaded = false;
            
            let errorMessage = 'Gagal memuat model AI. ';
            if (error.message.includes('404')) {
                errorMessage += 'File model tidak ditemukan. Periksa path model.';
            } else if (error.message.includes('timeout')) {
                errorMessage += 'Proses loading terlalu lama. Periksa koneksi internet.';
            } else if (error.message.includes('CORS')) {
                errorMessage += 'Error CORS. Pastikan file model dapat diakses.';
            } else {
                errorMessage += error.message;
            }
            
            utils.updateModelStatus(errorMessage, 'error');
            utils.addLog(`<i class="fas fa-times text-danger"></i> Model loading failed: ${error.message}`);
            
            // Tambahkan troubleshooting info
            const troubleshootDiv = document.createElement('div');
            troubleshootDiv.innerHTML = `
                <div class="mt-3 p-3 border rounded">
                    <h6><i class="fas fa-wrench"></i> Troubleshooting:</h6>
                    <ul class="mb-0" style="font-size: 0.85rem;">
                        <li>Pastikan folder 'model' ada dan berisi file model.json</li>
                        <li>Periksa console browser (F12) untuk error detail</li>
                        <li>Pastikan server mendukung file .json</li>
                        <li>Coba refresh halaman</li>
                    </ul>
                </div>
            `;
            UI.modelStatus.appendChild(troubleshootDiv);
            
            return false;
        }
    }
};

const cameraManager = {
    async startCamera() {
        if (!appState.isModelLoaded) {
            utils.addLog('<i class="fas fa-exclamation-triangle text-warning"></i> Model belum dimuat!');
            return false;
        }

        if (appState.isCameraActive) {
            utils.addLog('Kamera sudah aktif');
            return true;
        }

        try {
            utils.updateOverlay('Memulai kamera...');
            utils.addLog('Meminta akses kamera...');

            // Stop existing stream if any
            if (appState.video && appState.video.srcObject) {
                const tracks = appState.video.srcObject.getTracks();
                tracks.forEach(track => track.stop());
            }

            // Request camera access
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'environment' // Gunakan kamera belakang jika ada
                },
                audio: false
            });

            appState.video = UI.video;
            appState.video.srcObject = stream;
            
            // Wait for video to be ready
            await new Promise((resolve) => {
                appState.video.onloadedmetadata = () => {
                    resolve();
                };
            });

            appState.isCameraActive = true;
            utils.updateOverlay('Kamera aktif - Siap untuk deteksi');
            utils.addLog('<i class="fas fa-video text-success"></i> Kamera berhasil dimulai');

            // Update UI buttons
            if (UI.startBtn) {
                UI.startBtn.innerHTML = '<i class="fas fa-stop me-2"></i>Stop & Proses';
                UI.startBtn.onclick = this.stopAndProcess;
            }
            if (UI.resetBtn) {
                UI.resetBtn.style.display = 'inline-block';
            }

            // Start detection loop
            this.startDetectionLoop();

            return true;
        } catch (error) {
            console.error('Error starting camera:', error);
            appState.isCameraActive = false;
            
            let errorMessage = 'Gagal memulai kamera. ';
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Izin kamera ditolak.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'Kamera tidak ditemukan.';
            } else if (error.name === 'NotReadableError') {
                errorMessage += 'Kamera sedang digunakan aplikasi lain.';
            } else {
                errorMessage += error.message;
            }
            
            utils.updateOverlay(errorMessage);
            utils.addLog(`<i class="fas fa-times text-danger"></i> Camera error: ${error.message}`);
            
            // Show camera permission instructions
            const instructionsDiv = document.createElement('div');
            instructionsDiv.innerHTML = `
                <div class="mt-3 p-3 border rounded bg-light">
                    <h6><i class="fas fa-info-circle"></i> Cara mengaktifkan kamera:</h6>
                    <ol style="font-size: 0.85rem;">
                        <li>Klik ikon kamera 🎥 di address bar</li>
                        <li>Pilih "Allow" atau "Izinkan"</li>
                        <li>Refresh halaman dan coba lagi</li>
                        <li>Pastikan tidak ada aplikasi lain yang menggunakan kamera</li>
                    </ol>
                </div>
            `;
            UI.modelStatus.style.display = 'block';
            UI.modelStatus.appendChild(instructionsDiv);
            
            return false;
        }
    },

    startDetectionLoop() {
        if (appState.detectionLoop) {
            clearInterval(appState.detectionLoop);
        }

        appState.detectionLoop = setInterval(async () => {
            if (appState.isCameraActive && appState.model && appState.video) {
                try {
                    await this.detectObjects();
                } catch (error) {
                    console.error('Detection error:', error);
                    utils.addLog(`<i class="fas fa-exclamation-triangle text-warning"></i> Detection error: ${error.message}`);
                }
            }
        }, CONFIG.detectionInterval);
    },

    async detectObjects() {
        if (!appState.video || !appState.model) return;

        try {
            // Prepare image tensor
            const tensor = tf.browser.fromPixels(appState.video)
                .resizeNearestNeighbor([224, 224]) // Sesuaikan dengan input size model Anda
                .expandDims(0)
                .div(255.0);

            // Make prediction
            const predictions = await appState.model.predict(tensor).data();
            
            // Cleanup tensor
            tensor.dispose();

            // Process predictions
            this.processPredictions(predictions);

        } catch (error) {
            console.error('Detection error:', error);
        }
    },

    processPredictions(predictions) {
        const maxIndex = predictions.indexOf(Math.max(...predictions));
        const confidence = predictions[maxIndex];
        
        if (confidence > CONFIG.detectionThreshold) {
            const now = Date.now();
            
            switch (maxIndex) {
                case CONFIG.classIndexBottle:
                    if (now - appState.lastDetectionTimeBottle > 2000) { // 2 detik cooldown
                        appState.totalBottles++;
                        appState.lastDetectionTimeBottle = now;
                        utils.addLog(`<i class="fas fa-wine-bottle text-primary"></i> Botol terdeteksi! (${(confidence * 100).toFixed(1)}%)`);
                        utils.updateSaldoServerBottle();
                        utils.updateUI();
                        utils.updateOverlay(`Botol terdeteksi! Total: ${appState.totalBottles}`);
                    }
                    break;
                    
                case CONFIG.classIndexKaleng:
                    if (now - appState.lastDetectionTimeKaleng > 2000) { // 2 detik cooldown
                        appState.totalKaleng++;
                        appState.lastDetectionTimeKaleng = now;
                        utils.addLog(`<i class="fas fa-box text-warning"></i> Kaleng terdeteksi! (${(confidence * 100).toFixed(1)}%)`);
                        utils.updateSaldoServerKaleng();
                        utils.updateUI();
                        utils.updateOverlay(`Kaleng terdeteksi! Total: ${appState.totalKaleng}`);
                    }
                    break;
                    
                case CONFIG.classIndexKosong:
                    if (now - appState.lastDetectionTimeKosong > 1000) {
                        appState.lastDetectionTimeKosong = now;
                        utils.updateOverlay(`Tidak ada objek terdeteksi (${(confidence * 100).toFixed(1)}%)`);
                    }
                    break;
            }
        } else {
            utils.updateOverlay(`Mendeteksi... (confidence: ${(confidence * 100).toFixed(1)}%)`);
        }
    },

    stopCamera() {
        if (appState.video && appState.video.srcObject) {
            const tracks = appState.video.srcObject.getTracks();
            tracks.forEach(track => track.stop());
            appState.video.srcObject = null;
        }

        if (appState.detectionLoop) {
            clearInterval(appState.detectionLoop);
            appState.detectionLoop = null;
        }

        appState.isCameraActive = false;
        utils.updateOverlay('Kamera dihentikan');
        utils.addLog('<i class="fas fa-stop text-info"></i> Kamera dihentikan');

        // Update UI buttons
        if (UI.startBtn) {
            UI.startBtn.innerHTML = '<i class="fas fa-play me-2"></i>Mulai Menghitung';
            UI.startBtn.onclick = this.startCamera.bind(this);
            UI.startBtn.disabled = !appState.isModelLoaded;
        }
        if (UI.resetBtn) {
            UI.resetBtn.style.display = 'none';
        }
    },

    async stopAndProcess() {
        cameraManager.stopCamera();
        
        if (appState.totalBottles > 0 || appState.totalKaleng > 0) {
            const totalHarga = (appState.totalBottles * appState.hargaBotol) + (appState.totalKaleng * appState.hargaKaleng);
            
            Swal.fire({
                title: 'Transaksi Selesai!',
                html: `
                    <div class="text-start">
                        <p><strong>Detail Transaksi:</strong></p>
                        <ul>
                            <li>Botol: ${appState.totalBottles} × Rp ${appState.hargaBotol.toLocaleString('id-ID')} = Rp ${(appState.totalBottles * appState.hargaBotol).toLocaleString('id-ID')}</li>
                            <li>Kaleng: ${appState.totalKaleng} × Rp ${appState.hargaKaleng.toLocaleString('id-ID')} = Rp ${(appState.totalKaleng * appState.hargaKaleng).toLocaleString('id-ID')}</li>
                        </ul>
                        <hr>
                        <h5><strong>Total: Rp ${totalHarga.toLocaleString('id-ID')}</strong></h5>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Mulai Transaksi Baru',
                confirmButtonColor: '#2e8b57'
            }).then(() => {
                appManager.resetCounting();
            });
        } else {
            Swal.fire({
                title: 'Tidak Ada Sampah',
                text: 'Tidak ada sampah yang terdeteksi dalam sesi ini.',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2e8b57'
            });
        }
    }
};

// ===================== APP MANAGER =====================
const appManager = {
    async init() {
        utils.addLog('<i class="fas fa-play text-info"></i> Memulai aplikasi...');
        
        // Load model
        const modelLoaded = await modelManager.loadModel();
        
        if (!modelLoaded) {
            utils.addLog('<i class="fas fa-times text-danger"></i> Gagal memuat model, aplikasi tidak dapat berfungsi');
            return;
        }

        // Setup event listeners
        this.setupEventListeners();
        
        utils.addLog('<i class="fas fa-check text-success"></i> Aplikasi siap digunakan!');
    },

    setupEventListeners() {
        // Start button
        if (UI.startBtn) {
            UI.startBtn.onclick = () => cameraManager.startCamera();
        }

        // Reset button
        if (UI.resetBtn) {
            UI.resetBtn.onclick = () => this.resetCounting();
        }

        // Page visibility change handler
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && appState.isCameraActive) {
                // Pause detection when tab is not visible
                if (appState.detectionLoop) {
                    clearInterval(appState.detectionLoop);
                }
            } else if (!document.hidden && appState.isCameraActive) {
                // Resume detection when tab becomes visible
                cameraManager.startDetectionLoop();
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            cameraManager.stopCamera();
        });
    },

    resetCounting() {
        Swal.fire({
            title: 'Reset Hitungan?',
            text: 'Semua hitungan akan direset. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                appState.totalBottles = 0;
                appState.totalKaleng = 0;
                appState.lastDetectionTimeBottle = 0;
                appState.lastDetectionTimeKaleng = 0;
                appState.lastDetectionTimeKosong = 0;
                
                utils.updateUI();
                utils.updateOverlay('Hitungan direset - Siap untuk deteksi baru');
                utils.addLog('<i class="fas fa-redo text-warning"></i> Hitungan direset');
                
                Swal.fire('Reset!', 'Hitungan telah direset.', 'success');
            }
        });
    }
};

// ===================== INISIALISASI APLIKASI =====================
document.addEventListener('DOMContentLoaded', () => {
    // Check TensorFlow.js availability
    if (typeof tf === 'undefined') {
        utils.updateModelStatus('TensorFlow.js tidak dapat dimuat!', 'error');
        utils.addLog('<i class="fas fa-times text-danger"></i> TensorFlow.js tidak tersedia');
        return;
    }

    // Check camera support
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        utils.updateModelStatus('Browser tidak mendukung kamera!', 'error');
        utils.addLog('<i class="fas fa-times text-danger"></i> Browser tidak mendukung getUserMedia');
        return;
    }

    // Initialize app
    appManager.init();
});
</script>

</body>
</html>