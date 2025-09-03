<?php
session_start();
include "conn.php"; // Koneksi database

$error = "";
$success = "";

// Jika form register disubmit
if (isset($_POST['register'])) {
    $nama     = trim($_POST['nama']);
    $no_hp    = trim($_POST['no_hp']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = "nasabah"; // default role

    // Menggunakan prepared statement untuk cek email
    $stmt_check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {
        // Menggunakan prepared statement untuk insert data
        $stmt_insert = $conn->prepare("INSERT INTO users (nama, no_hp, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssss", $nama, $no_hp, $email, $password, $role);

        if ($stmt_insert->execute()) {
            $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
        }
    }
    $stmt_check->close();
    if (isset($stmt_insert)) {
        $stmt_insert->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Bank Sampah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #246a41;
            --dark-green: #246a41;
            --soft-bg: #eef2f5;
            --soft-white: #ffffff;
            --text-color: #444;
            --input-bg: #f7f9fc;
            --border-color: #ddd;
            --shadow-color: rgba(0,0,0,0.1);
            --placeholder-color: #a0a0a0;
            --error-red: #dc3545;
            --error-bg: #fdeded;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--soft-bg) 0%, #d1f0d2ff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .register-box {
            background: #ffffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px var(--shadow-color);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-in-out;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header .logo-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            color: var(--primary-green);
            font-weight: 700;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .header .logo-title i {
            font-size: 2.8rem;
            margin-right: 10px;
        }
        .header p {
            font-size: 1.1rem;
            color: #6c757d;
            margin: 10px 0 30px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background-color: var(--input-bg);
            font-size: 1rem;
            color: var(--text-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input::placeholder {
            color: var(--placeholder-color);
            font-weight: 400;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(4, 210, 86, 0.15);
            background-color: var(--soft-white);
        }
        .form-group i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--placeholder-color);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        .form-group input:focus + i {
            color: var(--primary-green);
        }
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
        .alert-danger {
            background-color: var(--error-bg);
            color: var(--error-red);
            border: 1px solid #f5c6cb;
        }
        .btn-register {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-green);
            color: var(--soft-white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn-register:hover {
            background-color: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(4, 210, 86, 0.2);
        }
        .btn-register:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(4, 210, 86, 0.2);
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-color);
        }
        .footer-text a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .footer-text a:hover {
            color: var(--dark-green);
            text-decoration: underline;
        }

        /* --- Perubahan untuk Responsif (ditambahkan) --- */
        @media (max-width: 600px) {
            body {
                align-items: flex-start; /* Ubah agar konten dimulai dari atas */
            }
            .register-box {
                padding: 30px 20px;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: flex-start; /* Ubah agar konten dimulai dari atas */
                padding-top: 50px; /* Tambahkan padding atas agar tidak terlalu mepet */
            }

            .header p {
                margin: 5px 0 20px;
            }

            .header .logo-title h2 {
                font-size: 2rem;
            }

            .header .logo-title i {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="header">
            <h2 class="logo-title"><i class="fas fa-recycle"></i>Bank Sampah</h2>
            <p>Daftar akun baru untuk mulai mengelola sampah Anda.</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="form-group">
                <input type="text" id="no_hp" name="no_hp" placeholder="Nomor Handphone" required>
                <i class="fas fa-phone"></i>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" name="register" class="btn-register">Daftar Akun</button>
        </form>
        
     <div class="footer-text">
            Sudah punya akun? <a href="login.php"><strong>Masuk di sini</strong></a>
        </div>
        <div class="footer-text" style="margin-top: 10px;">
            <a href="index.php"><strong>Kembali ke Beranda</strong></a>
        </div>
    </div>
</body>
</html>
