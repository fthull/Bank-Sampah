<?php
session_start();
include "conn.php";

$active_page = 'nasabah';

// Cek login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Proses tambah nasabah
if (isset($_POST['tambah_nasabah'])) {
    $nama = $_POST['nama'];
    $email= $_POST['email'];
    $nohp= $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (nama, email, no_hp, password, role) 
                  VALUES ('$nama','$email','$nohp','$password','nasabah')");
    header("Location: nasabah.php");
    exit();
}

// Ambil data nasabah
$result = $conn->query("SELECT u.id, u.nama, u.email, u.no_hp, u.created_at, 
                        IFNULL(s.total_saldo,0) as saldo
                        FROM users u 
                        LEFT JOIN saldo s ON u.id = s.user_id
                        WHERE u.role='nasabah'
                        ORDER BY u.created_at DESC");

// Proses Detail
$detail_nasabah = null;
if (isset($_GET['detail'])) {
    $id = (int) $_GET['detail'];
    $detail = $conn->query("SELECT u.*, IFNULL(s.total_saldo,0) as saldo 
                            FROM users u 
                            LEFT JOIN saldo s ON u.id=s.user_id 
                            WHERE u.id=$id");
    if ($detail->num_rows > 0) {
        $detail_nasabah = $detail->fetch_assoc();
    }
}

// Proses Edit
if (isset($_POST['ubah_nasabah'])) {
    $id = (int) $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nohp = $_POST['no_hp'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users 
                      SET nama='$nama', email='$email', no_hp='$nohp', password='$password' 
                      WHERE id=$id");
    } else {
        $conn->query("UPDATE users 
                      SET nama='$nama', email='$email', no_hp='$nohp' 
                      WHERE id=$id");
    }
    header("Location: nasabah.php");
    exit();
}

$edit_nasabah = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $edit = $conn->query("SELECT * FROM users WHERE id=$id");
    if ($edit->num_rows > 0) {
        $edit_nasabah = $edit->fetch_assoc();
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: nasabah.php");
    exit();
}
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
    <h2 class="mb-3"><i class="fas fa-users"></i>Kelola Nasabah</h2>

<?php if ($detail_nasabah): ?>
<div class="card mb-4">
    <div class="card-header bg-success text-white">Detail Nasabah</div>
    <div class="card-body">
        <p><b>Nama:</b> <?= $detail_nasabah['nama'] ?></p>
        <p><b>Email:</b> <?= $detail_nasabah['email'] ?></p>
        <p><b>No HP:</b> <?= $detail_nasabah['no_hp'] ?></p>
        <p><b>Saldo:</b> Rp <?= number_format($detail_nasabah['saldo'],0,',','.') ?></p>
        <p><b>Tanggal Gabung:</b> <?= $detail_nasabah['created_at'] ?></p>
        <a href="nasabah.php" class="btn btn-secondary">Kembali</a>
    </div>
</div>
<?php endif; ?>

    <!-- Form Tambah Nasabah -->
    <div class="card mb-4">
        <div class="card-header">Tambah Nasabah</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Nasabah" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
                    </div>
                    <div class="col-md-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                        <button type="submit" name="tambah_nasabah" class="btn btn-success w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Nasabah -->
    <table class="table table-bordered table-striped">
        <thead >
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Saldo</th>
                <th>Tanggal Gabung</th>
                <th>Aksi</th>
            </tr>
        </thead>
<tbody>
<?php
$no = 1;
while ($d = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . $no++ . "</td>
            <td>{$d['nama']}</td>
            <td>{$d['email']}</td>
            <td>{$d['no_hp']}</td>
            <td>Rp " . number_format($d['saldo'], 0, ',', '.') . "</td>
            <td>{$d['created_at']}</td>
            <td>
                <a href='?detail={$d['id']}' class='btn btn-success btn-sm'>Detail</a>
                <a href='?edit={$d['id']}' class='btn btn-warning btn-sm' onclick='return confirm(\"Ubah Data Nasabah ini?\")'>Ubah</a>
                <a href='?hapus={$d['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus nasabah ini?\")'>Hapus</a>
            </td>
          </tr>";
}
?>
</tbody>
    </table>

<?php if ($edit_nasabah): ?>
<div class="card mb-4">
    <div class="card-header bg-warning">Ubah Data Nasabah</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_nasabah['id'] ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="nama" class="form-control" value="<?= $edit_nasabah['nama'] ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" value="<?= $edit_nasabah['email'] ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="no_hp" class="form-control" value="<?= $edit_nasabah['no_hp'] ?>" required>
                </div>
                <div class="col-md-3">
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="col-md-2 d-flex justify-content-end">
                    <button type="submit" name="ubah_nasabah" class="btn btn-primary w-100">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

  </div>
</div>
</body>
</html>
