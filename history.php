<?php
include "conn.php";
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek kolom yang tersedia di tabel transaksi
$check_columns = mysqli_query($conn, "DESCRIBE transaksi");
$columns = [];
while($col = mysqli_fetch_assoc($check_columns)) {
    $columns[] = $col['Field'];
}

// Tentukan nama kolom untuk jumlah
$amount_col = 'jumlah';
if (!in_array('jumlah', $columns)) {
    $possible_names = ['amount', 'total', 'nominal', 'nilai', 'harga'];
    foreach($possible_names as $name) {
        if (in_array($name, $columns)) {
            $amount_col = $name;
            break;
        }
    }
}

// Ambil data user yang login
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

// Ambil semua transaksi user (pakai tabel transaksi_2 sesuai kodenya)
$result = mysqli_query($conn, "SELECT * FROM transaksi_2 WHERE user_id='$user_id' ORDER BY created_at DESC");

// Hitung total saldo dari tabel transaksi
$total_saldo = 0;
if (in_array($amount_col, $columns) && in_array('jenis', $columns) && in_array('status', $columns)) {
    $saldo_result = mysqli_query($conn, "
      SELECT 
         total_saldo
      FROM saldo 
      WHERE user_id='$user_id' 
    ");
    if ($saldo_result) {
        $saldo_data = mysqli_fetch_assoc($saldo_result);
        $total_saldo = $saldo_data['total_saldo'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>History Transaksi - Bank Sampah</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <style>
    body {
      padding-bottom: 60px;
      background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    }
    
    .saldo-card {
      background: linear-gradient(135deg, #198754 0%, #20c997 100%);
      border-radius: 15px;
      color: white;
      margin-bottom: 20px;
    }
    
    .transaction-item {
      border-radius: 12px;
      margin-bottom: 10px;
      transition: all 0.3s ease;
      cursor: pointer;
      border-left: 4px solid transparent;
    }
    
    .transaction-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .transaction-item.setor {
      border-left-color: #198754;
    }
    
    .transaction-item.tarik {
      border-left-color: #dc3545;
    }
    
    .transaction-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }
    
    .filter-tabs .nav-link {
      border-radius: 20px;
      margin: 0 5px;
    }
    
    .filter-tabs .nav-link.active {
      background: #198754;
      color: white;
    }
    
    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
      display: none;
    }
    
    @media (max-width: 768px) {
      .mobile-bottom-nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: #198754;
        color: white;
        z-index: 9999;
        border-top: 1px solid rgba(255,255,255,0.2);
      }
      .mobile-bottom-nav a {
        color: white;
        font-size: 12px;
        text-align: center;
        text-decoration: none;
        flex-grow: 1;
      }
      .mobile-bottom-nav i {
        display: block;
        font-size: 16px;
        margin-bottom: 2px;
      }
      .mobile-bottom-nav .active {
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        margin: 5px;
        padding: 5px;
      }
    }
  </style>
</head>
<body>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
  <a href="lp.php"><i class="bi bi-house-door"></i><span>Home</span></a>
  <a href="history.php" class="active"><i class="bi bi-clock-history"></i><span>History</span></a>
  <a href="harga.php"><i class="bi bi-recycle"></i><span>Setor</span></a>
  <a href="kontak.php"><i class="bi bi-telephone"></i><span>Tarik</span></a>
  <a href="login.php"><i class="bi bi-person"></i><span>Login</span></a>
</div>

<div class="container mt-3">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-clock-history me-2"></i>History Transaksi</h4>
    <a href="lp.php" class="btn btn-outline-success btn-sm">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Card Saldo -->
  <?php if (in_array($amount_col, $columns)): ?>
  <div class="card saldo-card">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col">
          <h6 class="mb-1 opacity-75">Total Saldo Anda</h6>
          <h3 class="mb-0">Rp<?= number_format($total_saldo, 0, ',', '.') ?></h3>
        </div>
        <div class="col-auto">
          <i class="bi bi-wallet2 fs-1 opacity-50"></i>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Filter Tabs -->
  <?php if (in_array('jenis', $columns)): ?>
  <ul class="nav nav-pills filter-tabs mb-3" id="filterTabs">
    <li class="nav-item">
      <a class="nav-link active" href="#" data-filter="all">Semua</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" data-filter="setor">Setor Sampah</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#" data-filter="tarik">Tarik Saldo</a>
    </li>
  </ul>
  <?php endif; ?>

  <!-- List Transaksi -->
  <div id="transactionList">
    <?php 
    $has_transactions = false;
    while($row = mysqli_fetch_assoc($result)): 
      $has_transactions = true;
      
      // Cek nilai default jika kolom tidak ada
      $jenis = isset($row['jenis']) ? $row['jenis'] : 'setor';
      $deskripsi = isset($row['deskripsi']) ? $row['deskripsi'] : 'Transaksi Bank Sampah';
      $amount = isset($row['jumlah']) ? floatval($row['jumlah']) : 0;
      $metode = isset($row['metode']) ? $row['metode'] : 'tunai';
      $status = isset($row['status']) ? $row['status'] : 'berhasil';
      $created_at = isset($row['created_at']) ? $row['created_at'] : date('Y-m-d H:i:s');

    ?>
      <div class="card transaction-item <?= $jenis ?>" 
           data-jenis="<?= $jenis ?>"
           data-bs-toggle="modal"
           data-bs-target="#detailModal<?= $row['id'] ?>">
        <div class="card-body py-3">
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="transaction-icon <?= $jenis=='setor' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                <?php if($jenis == 'setor'): ?>
                  <i class="bi bi-arrow-down-circle"></i>
                <?php else: ?>
                  <i class="bi bi-arrow-up-circle"></i>
                <?php endif; ?>
              </div>
            </div>
            <div class="col">
              <h6 class="mb-1"><?= htmlspecialchars($deskripsi) ?></h6>
              <div class="d-flex align-items-center text-muted">
                <small><i class="bi bi-calendar3 me-1"></i><?= date("d M Y", strtotime($created_at)) ?></small>
                <small class="ms-3"><i class="bi bi-clock me-1"></i><?= date("H:i", strtotime($created_at)) ?></small>
              </div>
              <small class="badge <?= $status=='berhasil' ? 'bg-success' : ($status=='pending' ? 'bg-warning' : 'bg-danger') ?>">
                <?= ucfirst($status) ?>
              </small>
            </div>
            <div class="col-auto text-end">
              <h5 class="mb-0 <?= $jenis=='setor' ? 'text-success' : 'text-danger' ?>">
                <?= $jenis=='setor' ? '+' : '-' ?>Rp<?= number_format($amount, 0, ',', '.') ?>
              </h5>
              <small class="text-muted"><?= ucfirst($metode) ?></small>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Detail -->
      <div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header <?= $jenis=='setor' ? 'bg-success' : 'bg-danger' ?> text-white">
              <h5 class="modal-title">
                <i class="bi <?= $jenis=='setor' ? 'bi-recycle' : 'bi-wallet2' ?> me-2"></i>
                Detail <?= ucfirst($jenis) ?>
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="row mb-3">
                <div class="col-4"><strong>ID Transaksi:</strong></div>
                <div class="col-8">#<?= $row['id'] ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-4"><strong>Deskripsi:</strong></div>
                <div class="col-8"><?= htmlspecialchars($deskripsi) ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-4"><strong>Jumlah:</strong></div>
                <div class="col-8">
                  <span class="<?= $jenis=='setor' ? 'text-success' : 'text-danger' ?>">
                    <?= $jenis=='setor' ? '+' : '-' ?>Rp<?= number_format($amount, 0, ',', '.') ?>
                  </span>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-4"><strong>Tanggal:</strong></div>
                <div class="col-8"><?= date("d M Y • H:i", strtotime($created_at)) ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-4"><strong>Metode:</strong></div>
                <div class="col-8"><?= ucfirst($metode) ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-4"><strong>Status:</strong></div>
                <div class="col-8">
                  <span class="badge <?= $status=='berhasil' ? 'bg-success' : ($status=='pending' ? 'bg-warning text-dark' : 'bg-danger') ?>">
                    <?= ucfirst($status) ?>
                  </span>
                </div>
              </div>
              

            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

    <?php if (!$has_transactions): ?>
      <div class="empty-state">
        <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
        <h5>Belum Ada Transaksi</h5>
        <p>Mulai setor sampah untuk melihat riwayat transaksi Anda</p>
        <a href="harga.php" class="btn btn-success">
          <i class="bi bi-recycle me-1"></i>Setor Sampah
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if (in_array('jenis', $columns)): ?>
<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
  const filterTabs = document.querySelectorAll('#filterTabs .nav-link');
  const transactionItems = document.querySelectorAll('.transaction-item');
  
  filterTabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Update active tab
      filterTabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      
      const filter = this.dataset.filter;
      
      // Show/hide transactions based on filter
      transactionItems.forEach(item => {
        if (filter === 'all' || item.dataset.jenis === filter) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  });
});
</script>
<?php endif; ?>

</body>
</html>