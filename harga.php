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

$result = $conn->query("SELECT setting_value FROM settings WHERE setting_key='wemos_ip' LIMIT 1");
$row = $result->fetch_assoc();

// echo json_encode([
//     "wemosBase" => $row['setting_value']
// ]);
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
            padding-top: 60px; /* Ditambahkan untuk mengimbangi navbar yang fixed */
            padding-bottom: 70px; /* Space for mobile nav */
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

        #video-container {
            position: relative;
            margin: 20px 0;
        }
        #video {
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            border-radius: 8px;
            width: 100%;
            height: 100%;
        }
        #overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
  .nota-container {
    width: 100%;
    margin: 20px auto;
    background: #f8f8ff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }
  .nota-container h3 {
    text-align: center;
    margin-bottom: 15px;
  }
  .nota-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }
  .nota-table th, .nota-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
  }
  .nota-table th {
    background: #e6e6fa;
  }
  .nota-total {
    text-align: right;
    font-size: 1.1em;
  }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #log {
            text-align: left;
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            font-family: monospace;
        }

        @media (max-width: 768px) {
            .desktop-navbar { display: none; }
            .mobile-bottom-nav { display: flex; }
            body { padding-top: 0; }
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

<div class="container mt-4 main-container">
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Saldo Anda</h5>
                    <h3 id="saldo-text">
                        <?= "Rp " . number_format($saldo, 2, ",", "."); ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Setor Sampah</h5>
                </div>
                <div class="card-body">
                    <div id="video-container">
                        <video id="video" width="640" height="480" autoplay muted></video>
                        <div id="overlay">Status: Menunggu inisialisasi...</div>
                    </div>
                    <div class="nota-container">
  <h3>Nota Transaksi</h3>
  <table class="nota-table">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Harga Satuan</th>
        <th>Jumlah</th>
        <th>Total Harga</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Botol</td>
        <td>200</td>
        <td id="jumlah-botol">0</td>
        <td id="total-botol">0</td>
      </tr>
      <tr>
        <td>Kaleng</td>
        <td>500</td>
        <td id="jumlah-kaleng">0</td>
        <td id="total-kaleng">0</td>
      </tr>
    </tbody>
  </table>
  <div class="nota-total">
    <strong>Grand Total: Rp<span id="grand-total">0</span></strong>
  </div>
</div>

                    <div class="d-flex justify-content-center mt-3">
                        <button id="startBtn" class="btn btn-primary me-2">Mulai Menghitung</button>
                        <button id="resetBtn" class="btn btn-warning" style="display: none;">Reset Hitungan</button>
                    </div>
                    <!-- <h6 class="mt-4">Log Deteksi</h6> -->
                    <div id="log" class="border rounded p-2 bg-light" style="display: none;">
                        <p>Log deteksi akan muncul di sini...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
<script>
// ===================== KONFIGURASI =====================
const CONFIG = {
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/aZOI9yE9A/model.json',
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/W7rqkR7Lb/model.json',
    // modelUrl: 'https://teachablemachine.withgoogle.com/models/g1ZyqsfqU/model.json',
    modelUrl: 'https://teachablemachine.withgoogle.com/models/g1ZyqsfqU/model.json',
    detectionThreshold: 0.8,    // Minimal confidence 80%
    detectionInterval: 500,    // Interval minimal deteksi (4 detik)
    classIndexKosong: 2,
    classIndexBottle: 0,        // indeks kelas Botol
    classIndexKaleng: 1,
    wemosBase: 'http://172.17.91.233' // <-- GANTI ke IP Wemos Anda
};

// ===================== VARIABEL APLIKASI =====================
let appState = {
    // untuk deteksi
    model: null,
    video: null,
    isDetecting: false,
    totalBottles: 0,
    totalKaleng: 0,
    lastDetectionTimeBottle: 0,
    lastDetectionTimeKaleng: 0,
    lastDetectionTimeKosong: 0,
    stableFramesNeeded: 4, // butuh N frame berturut-turut untuk valid
    stableBottle: 0,
    stableKaleng: 0,
    stableKosong: 0,

    // untuk perhitungan harga
    hargaBotol: 200,
    hargaKaleng: 500
};

// ===================== INISIALISASI ELEMEN UI =====================
const UI = {
    startBtn: document.getElementById('startBtn'),
    resetBtn: document.getElementById('resetBtn'),
    // Sesuai ID baru di tabel nota
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
        logEntry.textContent = `[${timeString}] ${message}`;
        UI.logElement.insertBefore(logEntry, UI.logElement.firstChild);

        if (UI.logElement.children.length > 20) {
            UI.logElement.removeChild(UI.logElement.lastChild);
        }
    },

    updateUI: () => {
        // Hitung total harga
        const totalBotolHarga = appState.totalBottles * appState.hargaBotol;
        const totalKalengHarga = appState.totalKaleng * appState.hargaKaleng;
        const grandTotalHarga = totalBotolHarga + totalKalengHarga;

        // Update tampilan tabel nota
        if (UI.jumlahBotol) UI.jumlahBotol.textContent = appState.totalBottles;
        if (UI.jumlahKaleng) UI.jumlahKaleng.textContent = appState.totalKaleng;
        if (UI.totalBotol) UI.totalBotol.textContent = totalBotolHarga;
        if (UI.totalKaleng) UI.totalKaleng.textContent = totalKalengHarga;
        if (UI.grandTotal) UI.grandTotal.textContent = grandTotalHarga;
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
            utils.addLog(`Saldo bertambah Rp ${tambahSaldo} (${data})`);
            return fetch("get_saldo.php");
        })
        .then(r => r.json())
        .then(json => {
            let saldoFormatted = new Intl.NumberFormat("id-ID", { 
                style: "currency", 
                currency: "IDR" 
            }).format(json.saldo);

            document.getElementById("saldo-text").textContent = saldoFormatted;
        })
        .catch(err => {
            utils.addLog("Error update saldo: " + err);
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
            utils.addLog(`Saldo bertambah Rp ${tambahSaldo} (${data})`);
            return fetch("get_saldo.php");
        })
        .then(r => r.json())
        .then(json => {
            let saldoFormatted = new Intl.NumberFormat("id-ID", { 
                style: "currency", 
                currency: "IDR" 
            }).format(json.saldo);

            document.getElementById("saldo-text").textContent = saldoFormatted;
        })
        .catch(err => {
            utils.addLog("Error update saldo: " + err);
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
            utils.addLog("Gagal mengirim perintah ke Wemos: " + e.message);
        }
    }
};

// helper kecil untuk jeda
const sleep = (ms) => new Promise(r => setTimeout(r, ms));

// === Kontrol Servo ===
(async () => {
    console.log("Inisialisasi: Servo default 90° (diam)");
    await wemos.servo(90); // default = diam
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
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            UI.video.srcObject = stream;
            return new Promise((resolve) => {
                UI.video.onloadedmetadata = () => {
                    resolve(UI.video);
                };
            });
        } catch (error) {
            UI.overlay.textContent = "Status: Gagal mengakses kamera";
            utils.addLog(`Error: Gagal mengakses kamera - ${error.message}`);
            console.error(error);
            return null;
        }
    }
};

// ===================== FUNGSI MODEL =====================
const model = {
    load: async () => {
        UI.overlay.textContent = "Status: Memuat model...";
        utils.addLog("Memulai pemuatan model machine learning");
        try {
            appState.model = await tf.loadLayersModel(CONFIG.modelUrl);
            UI.overlay.textContent = "Status: Model siap. Klik 'Mulai Deteksi'";
            utils.addLog("Model berhasil dimuat");
            UI.startBtn.disabled = false;
        } catch (error) {
            UI.overlay.textContent = "Status: Gagal memuat model";
            utils.addLog(`Error: Gagal memuat model - ${error.message}`);
            console.error(error);
        }
    },

    predict: async () => {
        if (!appState.isDetecting) return;
        if (!appState.model) {
            utils.addLog('Model belum dimuat');
            return;
        }

        try {
            // Capture frame dari video
            const canvas = document.createElement('canvas');
            canvas.width = UI.video.videoWidth;
            canvas.height = UI.video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(UI.video, 0, 0, canvas.width, canvas.height);

            // Preprocess gambar
            const img = tf.browser.fromPixels(canvas);
            const resized = tf.image.resizeBilinear(img, [224, 224]);
            const tensor = resized.expandDims(0);
            const normalized = tensor.div(255.0);

            // ===================== PREDIKSI =====================
            const predictions = await appState.model.predict(normalized).data();
            const bottleConfidence = predictions[CONFIG.classIndexBottle] || 0;
            const kalengConfidence = predictions[CONFIG.classIndexKaleng] || 0;
            const kosongConfidence = predictions[CONFIG.classIndexKosong] || 0;

            UI.overlay.textContent = 
              `Confidence Botol: ${(bottleConfidence * 100).toFixed(1)}% | ` +
              `Confidence Kaleng: ${(kalengConfidence * 100).toFixed(1)}% | ` +
              `Confidence Kosong: ${(kosongConfidence * 100).toFixed(1)}%`;

            const currentTime = Date.now();

            // ===================== STABLE FRAME (jeda beberapa frame) =====================
            // Reset behavior: hanya satu klasifikasi yang naik counter per frame
            if (bottleConfidence >= 0.9 && kosongConfidence <= 1.0) {
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
                    utils.addLog(
                        `Botol terdeteksi! Total: ${appState.totalBottles} (${(bottleConfidence * 100).toFixed(1)}%)`
                    );

                    utils.updateSaldoServerBottle();
                    await moveRight();
                    appState.stableBottle = 0; // reset counter
                }
            }
            else if (kalengConfidence >= 0.6 && kosongConfidence <= 1.0) {
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
                    utils.addLog(
                        `Kaleng terdeteksi! Total: ${appState.totalKaleng} (${(kalengConfidence * 100).toFixed(1)}%)`
                    );

                    utils.updateSaldoServerKaleng();
                    await moveRight();
                    appState.stableKaleng = 0;
                }
            }
            // jika kosong confidence di antara 60% - 100% -> diam
            else if (kosongConfidence >= 0.7 && kosongConfidence <= 1.0) {
                appState.stableKosong++;
                appState.stableBottle = 0;
                appState.stableKaleng = 0;

                if (
                    appState.stableKosong >= appState.stableFramesNeeded &&
                    (currentTime - appState.lastDetectionTimeKosong) > CONFIG.detectionInterval
                ) {
                    appState.lastDetectionTimeKosong = currentTime;
                    utils.addLog(`Kosong terdeteksi (${(kosongConfidence * 100).toFixed(1)}%)`);
                    await servoSleep();
                    appState.stableKosong = 0;
                }
            }
           else {
    // Tambahkan logika stabil juga
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
        utils.addLog("Gambar tidak jelas, servo ke kiri");
        await moveLeft();
        appState.stableUnknown = 0; // reset counter
    }
}


            // Cleanup tensor
            tf.dispose([img, resized, tensor, normalized]);

            // schedule next predict (jika masih mendeteksi)
           if (appState.isDetecting) {
    setTimeout(() => model.predict(), 400); // jalankan prediksi tiap 4 detik
}


        } catch (error) {
            utils.addLog(`Error saat prediksi: ${error.message}`);
            console.error(error);
            controls.stopDetection();
        }
    }
};

// ===================== FUNGSI KONTROL =====================
const controls = {
    startDetection: () => {
        if (!appState.model) {
            utils.addLog('Model belum dimuat, klik Mulai setelah model siap');
            return;
        }
        appState.isDetecting = true;
        UI.startBtn.textContent = "Hentikan Deteksi";
        UI.overlay.textContent = "Status: Sedang mendeteksi...";
        utils.addLog("Memulai deteksi");
        model.predict();
    },

    stopDetection: () => {
        appState.isDetecting = false;
        UI.startBtn.textContent = "Mulai Deteksi";
        UI.overlay.textContent = "Status: Deteksi dihentikan";
        utils.addLog("Deteksi dihentikan oleh pengguna");

        // Kirim hasil hitungan ke server
        fetch("save_transaksi.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `total_bottle=${appState.totalBottles}&total_kaleng=${appState.totalKaleng}`
        })
        .then(res => res.text())
        .then(data => {
            if (data === "OK") {
                utils.addLog("Transaksi tersimpan ke database");
            } else if (data === "NO_DATA") {
                utils.addLog("Tidak ada setoran, transaksi tidak disimpan");
            } else {
                utils.addLog("Gagal menyimpan transaksi: " + data);
            }
        }).catch(err => {
            utils.addLog('Gagal menyimpan transaksi: ' + err);
        });
    },

    resetCounter: () => {
        appState.totalBottles = 0;
        appState.totalKaleng = 0;
        utils.updateUI();
        utils.addLog("Hitungan direset ke 0");
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

UI.resetBtn.addEventListener('click', controls.resetCounter);

// ===================== INISIALISASI APLIKASI =====================
const initApp = async () => {
    UI.startBtn.disabled = true;
    UI.resetBtn.disabled = false;

    await camera.setup();
    await model.load();

    UI.video.play();
    UI.overlay.textContent = "Status: Kamera siap. Klik 'Mulai Deteksi'";
};

window.onload = initApp;
</script>
<script>
    let jumlahBotol = 0;
let jumlahKaleng = 0;
const hargaBotol = 200;
const hargaKaleng = 500;

function updateNota() {
  // Hitung total
  let totalBotol = jumlahBotol * hargaBotol;
  let totalKaleng = jumlahKaleng * hargaKaleng;
  let grandTotal = totalBotol + totalKaleng;

  // Update tampilan
  document.getElementById("jumlah-botol").textContent = jumlahBotol;
  document.getElementById("total-botol").textContent = totalBotol;
  document.getElementById("jumlah-kaleng").textContent = jumlahKaleng;
  document.getElementById("total-kaleng").textContent = totalKaleng;
  document.getElementById("grand-total").textContent = grandTotal;
}

// contoh simulasi update
function addBotol() {
  jumlahBotol++;
  updateNota();
}
function addKaleng() {
  jumlahKaleng++;
  updateNota();
}

</script>
</body>
</html>
