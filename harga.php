<?php
session_start();
include "conn.php";


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
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Nasabah - Bank Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
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
        #count-display {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
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
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Bank Sampah</a>
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
    
    <div id="count-display">Total Botol: 0</div>
    
    <div>
        <button id="startBtn">Mulai Deteksi</button>
        <button id="resetBtn">Reset Hitungan</button>
    </div>
    
    <div id="log">
        <p>Log deteksi akan muncul di sini...</p>
    </div>
</div>
    <!-- Histori Transaksi -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            Histori Transaksi
          </div>
          <div class="card-body">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Tipe</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $q_transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 10");
                while ($row = mysqli_fetch_assoc($q_transaksi)) {
                  echo "<tr>
                    <td>".$no++."</td>
                    <td>".$row['created_at']."</td>
                    <td>".ucfirst($row['tipe'])."</td>
                    <td>Rp ".number_format($row['jumlah'],0,",",".")."</td>
                  </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
  
    <script>
        // ===================== KONFIGURASI =====================
        const CONFIG = {
            modelUrl: 'https://teachablemachine.withgoogle.com/models/-mORRwfBPR/model.json',
            detectionThreshold: 0.8,    // Minimal confidence 80%
            detectionInterval: 2000,    // Interval minimal deteksi (2 detik)
            classIndex: 0,               // Indeks kelas Bottle (asumsi: [0]Bottle, [1]Nothing)
            wemosBase: 'http://172.17.91.180' // <-- GANTI ke IP Wemos Anda
        };

        // ===================== VARIABEL APLIKASI =====================
        let appState = {
            model: null,
            video: null,
            isDetecting: false,
            totalBottles: 0,
            lastDetectionTime: 0
        };

        // ===================== INISIALISASI ELEMEN UI =====================
        const UI = {
            startBtn: document.getElementById('startBtn'),
            resetBtn: document.getElementById('resetBtn'),
            countDisplay: document.getElementById('count-display'),
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
        UI.countDisplay.textContent = `Total Botol: ${appState.totalBottles}`;
    },

    updateSaldoServer: () => {
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
            fetch("get_saldo.php")
            .then(r => r.json())
            .then(json => {
                let saldoFormatted = new Intl.NumberFormat("id-ID", { 
                    style: "currency", 
                    currency: "IDR" 
                }).format(json.saldo);

                document.getElementById("saldo-text").textContent = saldoFormatted;
            });
        })
        .catch(err => {
            utils.addLog("Error update saldo: " + err);
        });
    }
};

         // ===================== FUNGSI SERVO =====================
         const wemos = {
        servo: async (pos) => {
            const url = `${CONFIG.wemosBase}/servo?pos=${pos}`;
            await fetch(url, { mode: 'cors' });
            }
        };

        // helper kecil untuk jeda
        const sleep = (ms) => new Promise(r => setTimeout(r, ms));


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
        
        // Prediksi
        const predictions = await appState.model.predict(normalized).data();
        const bottleConfidence = predictions[CONFIG.classIndex];
        
        UI.overlay.textContent = `Status: Confidence Botol: ${(bottleConfidence * 100).toFixed(1)}%`;
        
        // Deteksi botol
        const currentTime = Date.now();
        if (
            bottleConfidence > CONFIG.detectionThreshold && 
            (currentTime - appState.lastDetectionTime) > CONFIG.detectionInterval
        ) {
            appState.totalBottles++;
            appState.lastDetectionTime = currentTime;
            utils.updateUI();
            utils.addLog(
                `Botol terdeteksi! Total: ${appState.totalBottles} (${(bottleConfidence * 100).toFixed(1)}% confidence)`
            );

            // update saldo async (tidak blok loop)
            utils.updateSaldoServer();

            // servo jalan async
            wemos.servo(180).then(() => sleep(1000)).then(() => wemos.servo(90));
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
