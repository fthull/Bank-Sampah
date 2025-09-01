<?php
session_start();
include "conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bank Sampah - Index</title>
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
        <li class="nav-item"><a class="nav-link" href="kontak.php">Tarik</a></li>
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
  <a href="kontak.php"><i class="fas fa-phone"></i><span>Tarik</span></a>
  <a href="login.php"><i class="fas fa-user"></i><span>Login</span></a>
</div>

<div class="container mt-4">
  <div class="text-center">
    <h1>Selamat Datang di Bank Sampah</h1>
    <p class="lead">Kelola sampahmu, jadi lebih bermanfaat dan bernilai ekonomi 🌱</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
