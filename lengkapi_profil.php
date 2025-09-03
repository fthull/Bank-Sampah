<?php
session_start();
include 'conn.php';

// Redirect ke halaman login jika user belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$oldNama = $_SESSION['username'];
$message_status = '';

// Ambil data user yang ada dari database
$stmt = $conn->prepare("SELECT nama, nama_lengkap, gender FROM users WHERE nama = ?");
$stmt->bind_param("s", $oldNama);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data yang dikirim dari form
    $newNama = trim($_POST['nama'] ?? '');
    $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
    $gender = $_POST['gender'] ?? '';

    // Validasi input
    if (empty($newNama)) {
        $message_status = 'error_empty';
    } else {
        // Cek apakah nama pengguna baru sudah ada di database (kecuali nama pengguna saat ini)
        $stmt = $conn->prepare("SELECT id FROM users WHERE nama = ? AND nama != ?");
        $stmt->bind_param("ss", $newNama, $oldNama);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $message_status = 'error_duplicate_username';
        } else {
            // Perbarui data di database
            $sql = "UPDATE users SET nama = ?, nama_lengkap = ?, gender = ? WHERE nama = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $newNama, $namaLengkap, $gender, $oldNama);

            if ($stmt->execute()) {
                // Perbarui username di session jika berhasil
                $_SESSION['username'] = $newNama;
                $message_status = "success";
            } else {
                $message_status = "error";
            }
        }
        $stmt->close();
    }
    
    // Redirect dengan status
    header("Location: lengkapi_profil.php?status=" . $message_status);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Profil</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-green: #28a745; /* Hijau Tua */
            --primary-green-dark: #218838; /* Hijau lebih gelap */
            --light-green: #f2f9f2; /* Hijau yang sangat pucat */
            --medium-gray: #6c757d;
            --text-dark: #343a40;
            --bg-gradient-start: #d4edda;
            --bg-gradient-end: #c3e6cb;
            --card-gradient-start: #e9f5e9;
            --card-gradient-end: #d4edda;
            --profile-label-color: #555; 
        }

        html { 
            background: #f3fff8ff;
            min-height: 100vh;
        }
        body { 
            background-color: transparent; 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px; 
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }
        .card { 
            border-radius: 25px; 
            background: #cbf2dcff; 
            padding: 35px; 
            box-shadow: 0 18px 50px rgba(0,0,0,0.15); 
            border: none;
            overflow: hidden; 
            position: relative;
            z-index: 1;
            max-width: 550px;
        }
        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at top left, rgba(40, 167, 69, 0.04) 10%, transparent 40%),
                        radial-gradient(circle at bottom right, rgba(195, 230, 203, 0.06) 10%, transparent 40%);
            transform: rotate(15deg);
            z-index: -1;
            opacity: 0.8;
        }
        .btn {
            border-radius: 12px; 
            font-weight: 700; 
            padding: 14px 28px; 
            transition: all 0.3s ease-in-out;
            letter-spacing: 0.7px; 
            text-transform: uppercase;
        }
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-primary:hover, .btn-primary:active {
            background-color: var(--primary-green-dark);
            border-color: var(--primary-green-dark);
            /* Animasi tombol mengecil */
            transform: scale(0.98); 
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.5);
        }
        .btn-primary:active {
            transform: scale(0.95);
        }
        .btn-secondary {
            background-color: var(--medium-gray);
            border-color: var(--medium-gray);
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.3);
        }
        .btn-secondary:hover, .btn-secondary:active {
            background-color: #5a6268;
            border-color: #5a6268;
            /* Animasi tombol mengecil */
            transform: scale(0.98);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.4);
        }
        .btn-secondary:active {
            transform: scale(0.95);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 15px;
            border: 1px solid var(--primary-green);
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
            border-color: var(--primary-green);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .card {
                padding: 25px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
            .btn {
                padding: 12px 20px;
                font-size: 0.85em;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card mx-auto p-5 text-center">
        <h4 class="mb-4">✏️ Ubah Profil</h4>
        <form method="POST">
            <div class="form-group text-start">
                <label for="nama">Nama Pengguna</label>
                <input type="text" class="form-control" name="nama" id="nama" value="<?= htmlspecialchars($userData['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group text-start">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" value="<?= htmlspecialchars($userData['nama_lengkap'] ?? '') ?>">
            </div>
            <div class="form-group text-start">
                <label for="gender">Jenis Kelamin</label>
                <select class="form-control" name="gender" id="gender">
                    <option value="" <?= empty($userData['gender']) ? 'selected' : '' ?>>-- Pilih --</option>
                    <option value="Pria" <?= ($userData['gender'] ?? '') === 'Pria' ? 'selected' : '' ?>>Pria</option>
                    <option value="Wanita" <?= ($userData['gender'] ?? '') === 'Wanita' ? 'selected' : '' ?>>Wanita</option>
                </select>
            </div>
            <div> 
                <button type="submit" class="btn btn-primary w-80">Simpan Perubahan</button>
                <a href="profile.php" class="btn btn-danger mt-3 w-80">Kembali Ke Profil</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status) {
            let title, text, icon, redirectUrl;

            switch(status) {
                case 'success':
                    title = 'Berhasil!';
                    text = 'Profil berhasil diperbarui.';
                    icon = 'success';
                    redirectUrl = 'profile.php';
                    break;
                case 'error_duplicate_username':
                    title = 'Gagal!';
                    text = 'Nama pengguna sudah digunakan. Silakan pilih nama lain.';
                    icon = 'error';
                    break;
                case 'error_empty':
                    title = 'Gagal!';
                    text = 'Nama pengguna tidak boleh kosong.';
                    icon = 'error';
                    break;
                case 'error':
                    title = 'Gagal!';
                    text = 'Terjadi kesalahan saat memperbarui profil.';
                    icon = 'error';
                    break;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK'
            }).then(() => {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    // Clear the URL parameter after showing the alert
                    history.replaceState(null, '', 'lengkapi_profil.php');
                }
            });
        }
    });
</script>
</body>
</html>