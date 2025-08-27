<?php
session_start();
include "conn.php"; 
global $conn;

// Tentukan halaman aktif untuk sidebar
$active_page = "penjemputan";

// Handle tambah data penjemputan
if (isset($_POST['tambah_penjemputan'])) {
    $nama   = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp  = $_POST['no_hp'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $conn->query("INSERT INTO penjemputan (nama, alamat, no_hp, tanggal, status) 
                  VALUES ('$nama', '$alamat', '$no_hp', '$tanggal', '$status')");
}

// Handle hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM penjemputan WHERE id = $id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Penjemputan - Bank Sampah</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .main-sidebar {min-height: 100vh;}
    .nav-link.active { background-color: #007bff !important; color: white !important; }
    .logout-link { color: red !important; }
  </style>
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper d-flex">

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary bg-dark p-3">
    <h2 class="text-white"><i class="fas fa-recycle"></i> Bank Sampah</h2>
    <ul class="nav nav-pills nav-sidebar flex-column mt-4" data-widget="treeview">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link <?php if ($active_page == 'dashboard') echo 'active'; ?>">
          <i class="fas fa-tachometer-alt"></i> <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="nasabah.php" class="nav-link <?php if ($active_page == 'Nasabah') echo 'active'; ?>">
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

  <!-- Content -->
  <div class="content p-4 w-100">
    <h3><i class="fas fa-truck"></i> Data Penjemputan</h3>
    <hr>

    <!-- Form tambah penjemputan -->
    <form method="post" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="nama" class="form-control" placeholder="Nama" required>
      </div>
      <div class="col-md-3">
        <input type="text" name="alamat" class="form-control" placeholder="Alamat" required>
      </div>
      <div class="col-md-2">
        <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
      </div>
      <div class="col-md-2">
        <input type="date" name="tanggal" class="form-control" required>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-control" required>
          <option value="Menunggu">Menunggu</option>
          <option value="Diproses">Diproses</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" name="tambah_penjemputan" class="btn btn-success w-100">Tambah</button>
      </div>
    </form>

    <!-- Tabel data -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No HP</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $result = $conn->query("SELECT * FROM penjemputan ORDER BY id DESC");
          while ($d = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . $no++ . "</td>
                      <td>" . $d['nama'] . "</td>
                      <td>" . $d['alamat'] . "</td>
                      <td>" . $d['no_hp'] . "</td>
                      <td>" . $d['tanggal'] . "</td>
                      <td>" . $d['status'] . "</td>
                      <td>
                        <a href='?hapus=" . $d['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
                      </td>
                    </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>

