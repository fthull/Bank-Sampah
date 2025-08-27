
<?php
session_start();
include "conn.php";

$active_page = 'admin';
// Cek login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Proses tambah admin
if (isset($_POST['tambah_admin'])) {
    $nama = $_POST['nama'];
    $email= $_POST['email'];
    $nohp= $_POST['no_hp'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (nama, no_hp, email, role, password) VALUES ('$nama','$email','$nohp','$role','$password')");
    header("Location: admin.php");
    exit();
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Admin</title>
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
    <h2 class="mb-3"><i class="fas fa-user-cog"></i> Kelola Admin</h2>

    <!-- Form Tambah Admin -->
    <div class="card mb-4">
        <div class="card-header">Tambah Admin</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Admin" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
                    </div>
                    <div class="col-md-2">
    <select name="role" class="form-control" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
</div>

                    <div class="col-md-2">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" name="tambah_admin" class="btn btn-success w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Admin -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Role</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
             $no=1;
$data = $conn->query("SELECT * FROM users WHERE role IN ('admin','petugas') ORDER BY id DESC");
            while ($d = $data->fetch_assoc()) {
               echo "<tr>
        <td>" . $no++ . "</td>
        <td>" . $d['nama'] . "</td>
        <td>" . $d['no_hp'] . "</td>
        <td>" . $d['email'] . "</td>
        <td>" . $d['role'] . "</td>
        <td>
            <a href='?hapus=" . $d['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus admin ini?\")'>Hapus</a>
        </td>
      </tr>";

            }
            ?>
        </tbody>
    </table>

</div>
</body>
</html>
