


<?php
session_start();
include "conn.php";
global $conn;

// Cek login
// if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
//     header("Location: login.php");
//     exit;
// }

// ============ PROSES SIMPAN DATA ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_penarikan'])) {
    $user_id = $_POST['user_id'];
    $jumlah  = $_POST['jumlah'];

    // Simpan ke tabel penarikan
    $stmt = $conn->prepare("INSERT INTO penarikan (user_id, jumlah) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $jumlah);

    if ($stmt->execute()) {
        header("Location: transaksi_tarik.php?success=1");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . $stmt->error;
    }
}

// ============ AMBIL DATA PENARIKAN ============
$query = "
SELECT d.transaksi_id, u.nama, d.jenis_sampah_id, d.subtotal 
FROM detail_transaksi d
JOIN users u ON d.id = u.id
";
$result = $conn->query($query);

// ============ AMBIL DATA USER UNTUK DROPDOWN ============
$users = $conn->query("SELECT id, nama FROM users");
$active_page = 'transaksi_tarik';
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
      <div class="card-header bg-success text-white">
      <h4>Tambah Penarikan</h4>
    </div>
    <div class="card-body">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
      <?php elseif (isset($_GET['success'])): ?>
        <div class="alert alert-success">Penarikan berhasil disimpan.</div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="tambah_penarikan" value="1">
        <div class="mb-3">
          <label>Nama Nasabah</label>
          <select name="user_id" class="form-control" required>
            <option value="">Pilih Nasabah</option>
            <?php while($u = $users->fetch_assoc()): ?>
              <option value="<?= $u['id']; ?>"><?= $u['nama']; ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Jumlah Penarikan (Rp)</label>
          <input type="number" name="jumlah" class="form-control" min="1000" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
      </form>
    </div>
  </div>

  <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title"><i class="fas fa-arrow-up"></i> Daftar Penarikan</h3>
    </div>
        <div class="card-body table-responsive">
      <table class="table table-striped table-hover">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
              <tr>
              <th>ID</th>
              <th>Nama Nasabah</th>
              <th>Jumlah Penarikan</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                  <td data-label="ID"><?= $row['id']; ?></td>
                  <td data-label="Nama"><?= $row['nama']; ?></td>
                  <td data-label="Jumlah">Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                  <td data-label="Tanggal"><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">Belum ada data penarikan.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
    </div>
  </div>
</div>
</body>
</html>
