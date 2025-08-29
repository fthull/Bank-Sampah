<?php
session_start();
include "conn.php"; // koneksi database

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // cek user
    $query = $conn->prepare("SELECT * FROM users WHERE nama = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($password, $data['password'])) {
            // simpan session
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $data['id']; // PENTING: Tambahkan baris ini
            $_SESSION['username'] = $data['nama']; // PENTING: Ganti ke kolom 'nama'
            $_SESSION['role'] = $data['role']; // ambil role dari DB

            // cek role -> arahkan ke halaman sesuai role
            if ($data['role'] === 'admin') {
                header("Location: dashboard.php");
            } elseif ($data['role'] === 'nasabah') {
                header("Location: lp.php");
            } else {
                $error = "Role tidak dikenali!";
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Variabel CSS untuk kemudahan perubahan */
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
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .login-box {
            background: var(--soft-white);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 40px var(--shadow-color);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            z-index: 10;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-container h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            margin: 0 0 0 10px;
            color: var(--primary-green);
            font-weight: 700;
        }

        .logo-container i {
            font-size: 2.8rem;
            color: var(--primary-green);
        }
        
        .login-box > p {
            font-size: 1.1rem;
            color: #6c757d;
            margin: 10px 0 30px;
            line-height: 1.6;
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
            font-size: 1rem;
            background-color: var(--input-bg);
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

        .btn {
            width: 100%;
            padding: 15px;
            background: var(--primary-green);
            border: none;
            border-radius: 12px;
            color: var(--soft-white);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(4, 210, 86, 0.2);
        }
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(4, 210, 86, 0.2);
        }

        .error {
            color: var(--error-red);
            background-color: var(--error-bg);
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
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
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-container">
            <i class="fas fa-recycle"></i>
            <h2>Bank Sampah</h2>
        </div>
        
        <p>Selamat datang kembali di Bank Sampah Banguntapan! Silakan masuk untuk mengelola sampah Anda.</p>
        
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Nama Pengguna" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Kata Sandi" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn">Masuk</button>
        </form>
     <div class="footer-text">
            Belum punya akun? <a href="register.php"><strong>Daftar di sini</strong></a>
        </div>
        <div class="footer-text" style="margin-top: 10px;">
            <a href="index.php"><strong>Kembali ke Beranda</strong></a>
        </div>
    </div>
</body>
</html>
    </div>
</body>
</html>