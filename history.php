<?php
include "conn.php";
session_start();
// $user_id = $_SESSION['user_id'];

// Ambil semua transaksi user
$result = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id='1' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>History Transaksi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-3">
  <h4 class="mb-3">History Transaksi</h4>

  <ul class="list-group">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center"
          data-bs-toggle="modal"
          data-bs-target="#detailModal<?= $row['id'] ?>">
        <div>
          <?php if($row['jenis'] == 'setor'){ ?>
            <i class="bi bi-plus-circle text-success"></i>
          <?php } else { ?>
            <i class="bi bi-dash-circle text-danger"></i>
          <?php } ?>
          <strong><?= $row['deskripsi'] ?></strong><br>
          <small><?= date("d M Y • H:i", strtotime($row['created_at'])) ?></small>
        </div>
        <span class="<?= $row['jenis']=='setor' ? 'text-success' : 'text-danger' ?>">
          <?= $row['jenis']=='setor' ? '+' : '-' ?>Rp<?= number_format($row['jumlah'],0,',','.') ?>
        </span>
      </li>

      <!-- Modal Detail -->
      <div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Detail Transaksi</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <h6><?= $row['deskripsi'] ?></h6>
              <p><strong>Total:</strong> Rp<?= number_format($row['jumlah'],0,',','.') ?></p>
              <p><strong>Tanggal:</strong> <?= date("d M Y • H:i", strtotime($row['created_at'])) ?></p>
              <p><strong>Metode:</strong> <?= $row['metode'] ?></p>
              <p><strong>Status:</strong> <?= ucfirst($row['status']) ?></p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </ul>
</div>

</body>
</html>
