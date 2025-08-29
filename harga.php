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

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      padding-bottom: 60px; /* beri jarak agar konten tidak ketutup bottom navbar */
    }
    /* Bottom Navbar khusus mobile */
    .mobile-bottom-nav {
      display: none;
    }
    @media (max-width: 768px) {
      .desktop-navbar {
        display: none;
      }
      .mobile-bottom-nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: #198754; /* hijau Bootstrap */
        color: white;
        z-index: 9999;
        border-top: 1px solid rgba(255,255,255,0.2);
      }
      .mobile-bottom-nav a {
        color: white;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
        flex-grow: 1;
      }
      .mobile-bottom-nav i {
        display: block;
        font-size: 18px;
      }
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
        #count-display-bottle ,
        #count-display-lakban{
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            padding: 5px;
            background-color: #e0e0ff;
            border-radius: 5px;
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
  </style>
</head>
<body>

<!-- Navbar Desktop -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success desktop-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Bank Sampah</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
        <li class="nav-item"><a class="nav-link" href="harga.php">Setor Sampah</a></li>
        <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
        <li class="nav-item"><a class="btn btn-light btn-sm ms-2" href="login.php">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Bottom Navbar Mobile -->
<div class="mobile-bottom-nav">
  <a href="index.php"><i class="fas fa-home"></i><span>Home</span></a>
  <a href="history.php"><i class="fas fa-history"></i><span>History</span></a>
  <a href="harga.php"><i class="fas fa-recycle"></i><span>Setor</span></a>
  <a href="kontak.php"><i class="fas fa-phone"></i><span>Kontak</span></a>
  <a href="login.php"><i class="fas fa-user"></i><span>Login</span></a>
</div>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Setor Sampah</a>
      <div class="d-flex">
        <span class="navbar-text text-white me-3">
          Halo, <?= $user['nama']; ?>
        </span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="row">
      <!-- Info Saldo -->
      <div class="col-md-4">
  <div class="card shadow-sm border-success">
    <div class="card-body">
      <h5 class="card-title text-success">Saldo Anda</h5>
<h3 id="saldo-text">
  <?= "Rp " . number_format($saldo, 2, ",", "."); ?>
</h3>
    </div>
  </div>
</div>

<div>
    <div id="video-container">
        <video id="video" width="640" height="480" autoplay muted></video>
        <div id="overlay">Status: Menunggu inisialisasi...</div>
    </div>
    

        <div id="count-display-bottle">Total Botol: 0</div>
        <div id="count-display-lakban">Total Lakban: 0</div>
    <div>
        <button id="startBtn">Mulai Menghitung</button>
        <button id="resetBtn">Reset Hitungan</button>
    </div>
    
    <div id="log">
        <p>Log deteksi akan muncul di sini...</p>
    </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
        // ===================== KONFIGURASI =====================
        const CONFIG = {
            modelUrl: 'https://teachablemachine.withgoogle.com/models/aZOI9yE9A/model.json',
            // modelUrl: 'https://teachablemachine.withgoogle.com/models/SF0K0U939/model.json',
            // modelUrl: 'https://teachablemachine.withgoogle.com/models/GKEIbonIo/model.json',
            // modelUrl: 'https://teachablemachine.withgoogle.com/models/aZOI9yE9A/model.json',
            detectionThreshold: 0.8,    // Minimal confidence 80%
            detectionInterval: 4000,    // Interval minimal deteksi (2 detik)
            classIndexKosong: 2,
            classIndexBottle: 0,        // indeks kelas Botol
            classIndexLakban: 1,
            wemosBase: 'http://172.17.91.201' // <-- GANTI ke IP Wemos Anda
        };

        // ===================== VARIABEL APLIKASI =====================
        let appState = {
            model: null,
            video: null,
            isDetecting: false,
            totalBottles: 0,
            totalLakban: 0,
            lastDetectionTimeBottle: 0,
            lastDetectionTimeLakban: 0,
            lastDetectionTimeKosong: 0
        };

        // ===================== INISIALISASI ELEMEN UI =====================
        const UI = {
            startBtn: document.getElementById('startBtn'),
            resetBtn: document.getElementById('resetBtn'),
            countDisplayBottle: document.getElementById('count-display-bottle'),
            countDisplayLakban: document.getElementById('count-display-lakban'),
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
        if (UI.countDisplayBottle) {
            UI.countDisplayBottle.textContent = `Total Botol: ${appState.totalBottles}`;
        }
        if (UI.countDisplayLakban) {
            UI.countDisplayLakban.textContent = `Total Lakban: ${appState.totalLakban}`;
        }
    },

    updateSaldoServerBottle: () => {
        const tambahSaldo = 200;

        fetch("update_saldo.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `total_saldo=${tambahSaldo}`
        })
        .then(res => res.text())
        .then(data => {
            utils.addLog(`Saldo bertambah Rp ${tambahSaldo} (${data})`);

            // Ambil saldo terbaru dari server
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

    updateSaldoServerLakban: () => {
        const tambahSaldo = 500;

        fetch("update_saldo.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `total_saldo=${tambahSaldo}`
        })
        .then(res => res.text())
        .then(data => {
            utils.addLog(`Saldo bertambah Rp ${tambahSaldo} (${data})`);

            // Ambil saldo terbaru dari server
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
};

         // ===================== FUNGSI SERVO =====================
        //  const wemos = {
        // servo: async (pos) => {
        //     const url = `${CONFIG.wemosBase}/servo?pos=${pos}`;
        //     await fetch(url, { mode: 'cors' });
        //     }
        // };

        // // helper kecil untuk jeda
        // const sleep = (ms) => new Promise(r => setTimeout(r, ms));
const wemos = {
    servo: async (pos) => {
        try {
            const url = `${CONFIG.wemosBase}/servo?pos=${pos}`;
            await fetch(url, { mode: 'cors' });
            console.log(`Servo digerakkan ke posisi ${pos}°`);
        } catch (e) {
            console.error("Gagal mengirim perintah ke Wemos:", e);
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
    await sleep(2000); // jeda 1 detik
    console.log("Servo kembali ke posisi default (90°)");
    await wemos.servo(90);
}


async function moveLeft() {
    console.log("Servo → Kiri (0°)");
    await wemos.servo(0); // kiri = 0 derajat

    // jeda 1 detik
    await new Promise(resolve => setTimeout(resolve, 2000));

    console.log("Servo kembali ke posisi default (90°)");
    await wemos.servo(90); // kembali ke default
}


async function servoSleep() {
    console.log("Servo → Diam (Tengah)");
    await wemos.servo(90); // misalnya diam = tengah 90 derajat
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
const bottleConfidence = predictions[CONFIG.classIndexBottle];
const lakbanConfidence = predictions[CONFIG.classIndexLakban];
const kosongConfidence = predictions[CONFIG.classIndexKosong];

UI.overlay.textContent = 
  `Confidence Botol: ${(bottleConfidence * 100).toFixed(1)}% | ` +
  `Confidence Lakban: ${(lakbanConfidence * 100).toFixed(1)}% | ` +
  `Confidence Kosong: ${(kosongConfidence * 100).toFixed(1)}%`;

const currentTime = Date.now();

// ✅ Logika teratur: hanya satu jalan tiap frame
if (
  bottleConfidence > CONFIG.detectionThreshold && 
  (currentTime - appState.lastDetectionTimeBottle) > CONFIG.detectionInterval
) {
    appState.totalBottles++;
    appState.lastDetectionTime = currentTime;
    utils.updateUI();
    utils.addLog(
        `Botol terdeteksi! Total: ${appState.totalBottles} (${(bottleConfidence * 100).toFixed(1)}% confidence)`
    );

    utils.updateSaldoServerBottle();
    await moveRight(); // ke kanan
}

else if (
  lakbanConfidence > CONFIG.detectionThreshold && 
  (currentTime - appState.lastDetectionTimeLakban) > CONFIG.detectionInterval
) {
    appState.totalLakban++;
    appState.lastDetectionTimeLakban = currentTime;
    utils.updateUI();
    utils.addLog(
        `Lakban terdeteksi! Total: ${appState.totalLakban} (${(lakbanConfidence * 100).toFixed(1)}% confidence)`
    );

    utils.updateSaldoServerLakban();
    await moveRight(); // ke kanan
}

else if (
  kosongConfidence > CONFIG.detectionThreshold && 
  (currentTime - appState.lastDetectionTimeKosong) > CONFIG.detectionInterval
) {
    appState.lastDetectionTimeKosong = currentTime;
    utils.addLog(`Kosong terdeteksi (${(kosongConfidence * 100).toFixed(1)}%)`);
    await servoSleep(); // diam, jangan gerak
}

// kalau tidak ada yang terdeteksi dengan threshold, jangan gerakkan servo
else if (
  bottleConfidence < CONFIG.detectionThreshold &&
  lakbanConfidence < CONFIG.detectionThreshold &&
  kosongConfidence < CONFIG.detectionThreshold
) {
    utils.addLog("Gambar tidak jelas, servo tetap diam");
    await moveLeft(); // tetap diam
}

// kalau model mendeteksi kelas lain (misalnya salah prediksi)
else {
    await moveLeft(); // ke kiri
}

        
        // Cleanup tensor
        tf.dispose([img, resized, tensor, normalized]);
        
        // ⏩ selalu lanjut loop lagi
        requestAnimationFrame(model.predict);
    } catch (error) {
        utils.addLog(`Error saat prediksi: ${error.message}`);
        console.error(error);
        controls.stopDetection();
    }
}
        }
        // ===================== FUNGSI KONTROL =====================
        const controls = {
            startDetection: () => {
                appState.isDetecting = true;
                UI.startBtn.textContent = "Hentikan Deteksi";
                UI.overlay.textContent = "Status: Sedang mendeteksi...";
                utils.addLog("Memulai deteksi botol");
                model.predict();
            },
            
            stopDetection: () => {
                appState.isDetecting = false;
                UI.startBtn.textContent = "Mulai Deteksi";
                UI.overlay.textContent = "Status: Deteksi dihentikan";
                utils.addLog("Deteksi dihentikan oleh pengguna");
            },
            
            resetCounter: () => {
                appState.totalBottles = 0;
                utils.updateUI();
                utils.addLog("Hitungan botol direset ke 0");
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
</body>
</html>
