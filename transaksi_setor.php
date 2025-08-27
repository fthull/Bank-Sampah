
<?php
session_start();
include "conn.php";
global $conn;

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data setoran
$query = "SELECT t.id, u.nama, t.jenis, t.total, t.status, t.created_at
          FROM transaksi t
          JOIN users u ON t.user_id = u.id
          ORDER BY t.created_at DESC";
$result = $conn->query($query);

// Halaman aktif untuk sidebar
$active_page = "transaksi_setor";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Nasabah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .main-sidebar {min-height: 100vh;}
    .nav-link.active { background-color: #007bff !important; color: white !important; }
    .logout-link { color: red !important; }
  </style>
</head>
<body>
<div class="wrapper d-flex">

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary bg-dark p-3">
    <h2 class="text-white"><i class="fas fa-recycle"></i> Bank Sampah</h2>
    <ul class="nav nav-pills nav-sidebar flex-column mt-4">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link <?php if ($active_page=='dashboard') echo 'active'; ?>">
          <i class="fas fa-tachometer-alt"></i> <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="nasabah.php" class="nav-link <?php if ($active_page=='nasabah') echo 'active'; ?>">
          <i class="fas fa-users"></i> <p>Nasabah</p>
        </a>
      </li>
      <li class="nav-item">
          <a href="transaksi_setor.php" class="nav-link <?php if ($active_page == 'transaksi_setor') echo 'active'; ?>">
              <i class="fas fa-donate"></i>
              <p>Setoran</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="transaksi_tarik.php" class="nav-link <?php if ($active_page == 'transaksi_tarik') echo 'active'; ?>">
              <i class="fas fa-arrow-up"></i>
              <p>Penarikan</p>
          </a>
        </li>
      <li class="nav-item">
        <a href="penjemputan.php" class="nav-link <?php if ($active_page=='penjemputan') echo 'active'; ?>">
          <i class="fas fa-truck"></i> <p>Penjemputan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin.php" class="nav-link <?php if ($active_page=='admin') echo 'active'; ?>">
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

  <!-- Content -->
  <div class="content p-4 w-100">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-donate"></i> Daftar Setoran</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover">
          <thead class="thead-dark">
            <tr>
              <th>No</th>
              <th>Nama Nasabah</th>
              <th>Jenis Sampah</th>
              <th>Berat (Kg)</th>
              <th>Total Harga</th>
              <th>Tanggal Setor</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no=1;
            while($row = $result->fetch_assoc()): ?>
              <tr>
                <td data-label="No"><?= $no++; ?></td>
                <td data-label="Nama Nasabah"><?= htmlspecialchars($row['nama']); ?></td>
                <td data-label="Jenis Sampah"><?= htmlspecialchars($row['jenis']); ?></td>
                <td data-label="Berat"><?= htmlspecialchars($row['total']); ?> Kg</td>
                <td data-label="Total Harga">Rp <?= number_format($row['total'],0,',','.'); ?></td>
                <td data-label="Tanggal Setor"><?= date('d-m-Y', strtotime($row['created_at'])); ?></td>
                <td data-label="Aksi">
                  <a href="detail_setoran.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                  <a href="edit_setoran.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                  <a href="hapus_setoran.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus setoran ini?')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
