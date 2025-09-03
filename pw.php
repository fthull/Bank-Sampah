<?php
session_start();
include 'conn.php';

// Redirect ke halaman login jika user belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? '';
$message_status = ''; // Variabel untuk status SweetAlert

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ganti_password'])) {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Ambil hash password lama dari database
    $stmt = $conn->prepare("SELECT password FROM users WHERE nama = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($password_hash_db);
    $stmt->fetch();
    $stmt->close();
    
    // Validasi sebelum proses verifikasi
    if ($password_baru !== $konfirmasi_password) {
        $message_status = 'new_password_mismatch';
    } elseif (password_verify($password_baru, $password_hash_db)) {
        $message_status = 'new_password_same_as_old';
    } elseif (password_verify($password_lama, $password_hash_db)) {
        // Enkripsi password baru
        $hashed_baru = password_hash($password_baru, PASSWORD_DEFAULT);
        
        // Perbarui password di database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE nama = ?");
        $stmt->bind_param("ss", $hashed_baru, $username);
        
        if ($stmt->execute()) {
            $message_status = 'success';
        } else {
            $message_status = 'error';
        }
        $stmt->close();
    } else {
        $message_status = 'old_password_mismatch';
    }

    // Redirect ke halaman yang sama dengan parameter status untuk menampilkan alert
    header("Location: pw.php?status=" . $message_status);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <div class="card mx-auto text-center p-5">
        <h4 class="mb-4">🔐 Ubah Password</h4>
        <form method="POST">
            <input type="hidden" name="ganti_password" value="1">
            <div class="form-group">
                <input type="password" class="form-control" name="password_lama" placeholder="Password Lama" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password_baru" placeholder="Password Baru" required>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="konfirmasi_password" placeholder="Konfirmasi Password Baru" required>
            </div>
            <button type="submit" class="btn btn-primary w-90">Simpan Perubahan</button>
            <a href="profile.php" class="btn btn-danger mt-3 w-90">Kembali ke Profil</a>
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
                    text = 'Password berhasil diubah.';
                    icon = 'success';
                    redirectUrl = 'profile.php';
                    break;
                case 'old_password_mismatch':
                    title = 'Gagal!';
                    text = 'Password lama tidak cocok.';
                    icon = 'error';
                    break;
                case 'new_password_mismatch':
                    title = 'Gagal!';
                    text = 'Konfirmasi password baru tidak cocok.';
                    icon = 'error';
                    break;
                case 'new_password_same_as_old':
                    title = 'Gagal!';
                    text = 'Password baru tidak boleh sama dengan password lama.';
                    icon = 'error';
                    break;
                case 'error':
                    title = 'Gagal!';
                    text = 'Terjadi kesalahan saat mengubah password.';
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
                    // Hapus parameter URL setelah alert ditampilkan
                    history.replaceState(null, '', 'pw.php');
                }
            });
        }
    });
</script>
</body>
</html>