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
            $_SESSION['username'] = $data['username'];
            $_SESSION['role'] = $data['role']; // ambil role dari DB

            // cek role → arahkan ke halaman sesuai role
            if ($data['role'] === 'admin') {
                header("Location: dashboard.php");
            } elseif ($data['role'] === 'nasabah') {
                header("Location: index.php");
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
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      margin: 0;
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #343a40;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
    }
    .form-group i {
      position: absolute;
      margin-left: 10px;
      margin-top: 14px;
      color: #888;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: #04d256ff;
      border: none;
      border-radius: 6px;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
    }
    .btn:hover {
      background: #04d256ff;
    }
    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2><i class="fas fa-recycle"></i> Bank Sampah</h2>
  
  <?php if($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <input type="text" name="username" placeholder="Username" required>
    </div>
    <div class="form-group">
      <input type="password" name="password" placeholder="Password" required>
    </div>
    <button type="submit" class="btn">Login</button>
  </form>
</div>

</body>
</html>
