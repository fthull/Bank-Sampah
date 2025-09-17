<?php
session_start();
include "conn.php";
global $conn;

// Tentukan halaman aktif untuk sidebar
$active_page = "dashboard";

// Ambil statistik dari database
$totalNasabah = $conn->query("SELECT COUNT(*) as jml FROM users WHERE role='nasabah'")->fetch_assoc()['jml'];
$totalSetoran = $conn->query("SELECT IFNULL(SUM(jumlah),0) as jml FROM transaksi_2 WHERE jenis='setor' AND status='berhasil'")->fetch_assoc()['jml'];
$totalPenarikan = $conn->query("SELECT IFNULL(SUM(jumlah),0) as jml FROM transaksi_2 WHERE jenis='tarik' AND status='berhasil'")->fetch_assoc()['jml'];
$totalSampah = $conn->query("SELECT IFNULL(SUM(berat),0) as jml FROM detail_transaksi")->fetch_assoc()['jml'];

// Data untuk chart (7 transaksi terakhir)
$dataChart = $conn->query("
    SELECT DATE(created_at) as tgl, SUM(total) as jml
    FROM transaksi 
    WHERE status='selesai'
    GROUP BY DATE(created_at)
    ORDER BY created_at DESC LIMIT 7
");

$labels = [];
$values = [];
while ($row = $dataChart->fetch_assoc()) {
    $labels[] = $row['tgl'];
    $values[] = $row['jml'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin | Bank Sampah</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="plugins/chart.js/Chart.min.js"></script>
  <style>
    body { background: #f4f6f9; }
    .container { margin-top: 40px; }
    .card { border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    table { font-size: 14px; }

    @media (max-width: 768px) { .card-header h4 { font-size: 18px; } table { font-size: 13px; } }
    @media (max-width: 576px) {
      table thead { display: none; }
      table tr { display: block; margin-bottom: 10px; border-bottom: 1px solid #ddd; }
      table td { display: block; text-align: right; font-size: 13px; }
      table td::before { content: attr(data-label); float: left; font-weight: bold; text-transform: capitalize; }
    }

    /* Statistik */
    .stats-container { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; }
    .stat-box { flex: 1 1 calc(25% - 20px); min-width: 200px; padding: 20px; border-radius: 15px; color: #fff; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s; }
    .stat-box:hover { transform: translateY(-5px); }
    .stat-box .info h3 { font-size: 26px; margin: 0; }
    .stat-box .info p { margin: 0; font-size: 14px; opacity: 0.9; }
    .stat-box .icon { font-size: 40px; opacity: 0.7; }
    .bg-info { background: #17a2b8; }
    .bg-success { background: #28a745; }
    .bg-danger { background: #dc3545; }
    .bg-warning { background: #ffc107; color: #333; }
    @media (max-width: 992px) { .stat-box { flex: 1 1 calc(50% - 20px); } }
    @media (max-width: 576px) { .stat-box { flex: 1 1 100%; } }

    /* Sidebar */
    .main-sidebar {min-height: 100vh;}
    .nav-link.active { background-color: #007bff !important; color: white !important; }
    .logout-link { color: red !important; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper d-flex">

  <!-- Sidebar langsung di sini -->
  <aside class="main-sidebar sidebar-dark-primary bg-dark p-3">
    <h2 class="text-white"><i class="fas fa-recycle"></i> Bank Sampah</h2>
    <ul class="nav nav-pills nav-sidebar flex-column mt-4">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link <?php if ($active_page == 'dashboard') echo 'active'; ?>">
          <i class="fas fa-tachometer-alt"></i> <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="nasabah.php" class="nav-link <?php if ($active_page == 'nasabah') echo 'active'; ?>">
          <i class="fas fa-users"></i> <p>Nasabah</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="transaksi_setor.php" class="nav-link <?php if ($active_page == 'transaksi_setor') echo 'active'; ?>">
          <i class="fas fa-donate"></i> <p>Setoran</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="transaksi_tarik.php" class="nav-link <?php if ($active_page == 'transaksi_tarik') echo 'active'; ?>">
          <i class="fas fa-arrow-up"></i> <p>Penarikan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="penjemputan.php" class="nav-link <?php if ($active_page == 'penjemputan') echo 'active'; ?>">
          <i class="fas fa-truck"></i> <p>Penjemputan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin.php" class="nav-link <?php if ($active_page == 'admin') echo 'active'; ?>">
          <i class="fas fa-user-cog"></i> <p>Admin & Petugas</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="logout.php" class="nav-link logout-link">
          <i class="fas fa-sign-out-alt"></i> <p>Logout</p>
        </a>
      </li>
    </ul>
  </aside>

  <!-- Content Wrapper -->
  <div class="content p-4 w-100">
    <section class="content-header">
      <h2><i class="fas fa-tachometer-alt"></i>Dashboard</h2>
    </section>

    <section class="content">
      <!-- Statistik -->
      <div class="stats-container">
        <div class="stat-box bg-info">
          <div class="info">
            <h3><?= $totalNasabah; ?></h3>
            <p>Total Nasabah</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
        </div>

        <div class="stat-box bg-success">
          <div class="info">
            <h3><?= number_format($totalSetoran,0,',','.'); ?></h3>
            <p>Total Setoran (Rp)</p>
          </div>
          <div class="icon"><i class="fas fa-arrow-down"></i></div>
        </div>

        <div class="stat-box bg-danger">
          <div class="info">
            <h3><?= number_format($totalPenarikan,0,',','.'); ?></h3>
            <p>Total Penarikan (Rp)</p>
          </div>
          <div class="icon"><i class="fas fa-arrow-up"></i></div>
        </div>

        <div class="stat-box bg-warning">
          <div class="info">
            <h3><?= $totalSampah; ?> kg</h3>
            <p>Total Sampah Terkumpul</p>
          </div>
          <div class="icon"><i class="fas fa-recycle"></i></div>
        </div>
      </div>

      <!-- Grafik -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-chart-bar"></i> Statistik Transaksi Mingguan</h3>
        </div>
        <div class="card-body">
          <canvas id="chartTransaksi"></canvas>
        </div>
      </div>

      <!-- Notifikasi Transaksi Terbaru -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-bell"></i> Transaksi Terbaru</h3>
        </div>
        <div class="card-body">
          <ul>
          <?php
            $notif = $conn->query("SELECT u.nama, t.jenis, t.total, t.status, t.created_at 
                                   FROM transaksi t 
                                   JOIN users u ON t.user_id=u.id 
                                   ORDER BY t.created_at DESC LIMIT 5");
            while ($row = $notif->fetch_assoc()) {
              echo "<li><b>{$row['nama']}</b> melakukan <b>{$row['jenis']}</b> Rp".number_format($row['total'],0,',','.')." [{$row['status']}]</li>";
            }
          ?>
          </ul>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
var ctx = document.getElementById('chartTransaksi').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_reverse($labels)); ?>,
        datasets: [{
            label: 'Total Transaksi (Rp)',
            data: <?= json_encode(array_reverse($values)); ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 3,
            fill: false,
            tension: 0.1
        }]
    }
});
</script>

</body>
</html>
