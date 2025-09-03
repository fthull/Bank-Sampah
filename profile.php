<?php
session_start();
include 'conn.php';

// Redirect ke halaman login jika user belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$message_status = '';

// Direktori untuk foto profil
$uploadDir = 'uploads/';
$defaultMaleAvatar = 'uploads/pria.png';
$defaultFemaleAvatar = 'uploads/wanita.png';
$defaultAvatar = 'uploads/avatar.webp';

// Pastikan folder uploads ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// === LOGIKA UNGGAH DAN HAPUS FOTO PROFIL ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'] ?? null;

    if ($userId === null) {
        $message_status = 'error_no_id';
    } else {
        $stmt = $conn->prepare("SELECT foto FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($oldFoto);
        $stmt->fetch();
        $stmt->close();

        if ($action === 'upload') {
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['foto']['tmp_name'];
                $fileName = $_FILES['foto']['name'];
                $fileSize = $_FILES['foto']['size'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2 MB

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $message_status = 'error_invalid_type';
                } elseif ($fileSize > $maxFileSize) {
                    $message_status = 'error_file_size';
                } else {
                    $newFileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        if (!empty($oldFoto) && file_exists($uploadDir . $oldFoto) &&
                            $oldFoto != pathinfo($defaultMaleAvatar, PATHINFO_BASENAME) &&
                            $oldFoto != pathinfo($defaultFemaleAvatar, PATHINFO_BASENAME) &&
                            $oldFoto != pathinfo($defaultAvatar, PATHINFO_BASENAME)) {
                            unlink($uploadDir . $oldFoto);
                        }

                        $stmt = $conn->prepare("UPDATE users SET foto = ? WHERE id = ?");
                        $stmt->bind_param("si", $newFileName, $userId);
                        $stmt->execute();
                        $stmt->close();
                        $message_status = 'success_upload';
                    } else {
                        $message_status = 'error_upload';
                    }
                }
            } else {
                $message_status = 'error_file';
            }
        } elseif ($action === 'delete') {
            if (!empty($oldFoto) && file_exists($uploadDir . $oldFoto) &&
                $oldFoto != pathinfo($defaultMaleAvatar, PATHINFO_BASENAME) &&
                $oldFoto != pathinfo($defaultFemaleAvatar, PATHINFO_BASENAME) &&
                $oldFoto != pathinfo($defaultAvatar, PATHINFO_BASENAME)) {
                unlink($uploadDir . $oldFoto);
            }

            $stmt = $conn->prepare("UPDATE users SET foto = NULL WHERE id = ?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                $message_status = 'success_delete';
            } else {
                $message_status = 'error_delete';
            }
            $stmt->close();
        }
    }
    header("Location: profile.php?status=" . $message_status);
    exit;
}

// Ambil data user, termasuk foto, gender, dan saldo dari tabel saldo
$stmt = $conn->prepare("SELECT u.id, u.nama, u.nama_lengkap, u.foto, u.gender, u.role, s.total_saldo
                         FROM users u
                         LEFT JOIN saldo s ON u.id = s.user_id
                         WHERE u.nama = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    echo "User tidak ditemukan.";
    exit;
}

$_SESSION['user_id'] = $data['id'];

$namaPengguna = $data['nama'] ?? '';
$namaLengkap = $data['nama_lengkap'] ?? '';
$foto = $data['foto'] ?? '';
$gender = $data['gender'] ?? '';
$role = $data['role'] ?? '';
$saldo = $data['total_saldo'] ?? 0.00;

$currentFotoFileName = basename($foto);
if (!empty($foto) && file_exists($uploadDir . $currentFotoFileName)) {
    $fotoPath = $uploadDir . $currentFotoFileName;
} else {
    if ($gender === 'Pria') {
        $fotoPath = $defaultMaleAvatar;
    } elseif ($gender === 'Wanita') {
        $fotoPath = $defaultFemaleAvatar;
    } else {
        $fotoPath = $defaultAvatar;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Definisi Variabel Warna */
        :root {
            --primary-green: #2e8b57;
            --secondary-green: #3cb371;
            --accent-yellow: #ffc107;
            --bg-light: #f3fff8;
            --card-background: #ffffff;
            --text-dark: #333333;
            --text-light: #777777;
            --white: #ffffff;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --gradient-main: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        }

        /* Gaya Umum */
        html {
            background-color: var(--bg-light);
            min-height: 100vh;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: transparent;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 70px;
            color: var(--text-dark);
            overflow-x: hidden;
            padding-top: 0;
        }

        /* --- Navbar Style (Menyerupai beranda.php) --- */
        .desktop-navbar {
            background: var(--gradient-main);
            box-shadow: 0 2px 10px var(--shadow-medium);
        }
        .desktop-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--white);
            transition: transform 0.3s ease;
        }
        .desktop-navbar .navbar-brand:hover {
            transform: scale(1.05);
        }
        .desktop-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: color 0.3s, transform 0.3s;
            position: relative;
        }
        .desktop-navbar .nav-link:hover,
        .desktop-navbar .nav-link.active {
            color: var(--accent-yellow) !important;
            transform: translateY(-2px);
        }
        .desktop-navbar .nav-link.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -5px;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background-color: var(--accent-yellow);
            border-radius: 2px;
        }

        /* --- Mobile Navbar (Menyerupai beranda.php) --- */
        .mobile-bottom-nav {
            display: none;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: var(--primary-green);
            color: var(--white);
            z-index: 9999;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.2);
        }
        .mobile-bottom-nav a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .mobile-bottom-nav a:hover {
            color: var(--accent-yellow);
            transform: translateY(-3px);
        }
        .mobile-bottom-nav a.active {
            color: var(--accent-yellow);
            font-weight: 600;
            transform: translateY(-3px);
        }
        .mobile-bottom-nav i {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
        }

        /* Header Section */
        .header {
            background: var(--gradient-main);
            color: var(--white);
            padding: 80px 20px 60px;
            text-align: center;
            border-bottom-left-radius: 80px;
            border-bottom-right-radius: 80px;
            position: relative;
            z-index: 0;
            box-shadow: 0 8px 25px var(--shadow-medium);
            overflow: hidden;
            margin-bottom: -50px;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }
        .header h1 {
            font-size: 3.2rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
        }
        .header p {
            font-size: 1.3rem;
            font-weight: 400;
            margin-top: 15px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }

        /* --- Card & Profile --- */
        .profile-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            border-radius: 25px;
            background: var(--card-background);
            padding: 40px;
            box-shadow: 0 15px 40px var(--shadow-medium);
            border: none;
            overflow: hidden;
            position: relative;
            z-index: 1;
            max-width: 550px;
            width: 100%;
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-green);
            padding: 3px;
            margin: 0 auto 20px;
            display: block;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s ease-in-out;
        }
        .profile-photo:hover {
            transform: scale(1.05) rotate(2deg);
        }

        /* Perbaikan untuk teks "Halo" */
        .welcome-text {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .welcome-text span {
            color: var(--primary-green);
            font-weight: 800;
        }

        /* --- Details Section Styling --- */
        .profile-details-section {
            background-color: var(--bg-light);
            border-radius: 20px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: inset 0 3px 10px rgba(0,0,0,0.05);
        }
        .profile-details-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }
        .profile-details-item:hover {
            transform: translateX(5px);
        }
        .profile-details-item:last-child {
            margin-bottom: 0;
        }
        .profile-details-item i {
            color: var(--primary-green);
            font-size: 1.5em;
            width: 35px;
            text-align: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .detail-label {
            color: var(--text-light);
            font-weight: 600;
            flex-shrink: 0;
            margin-right: 10px;
        }
        .detail-value {
            font-weight: 500;
            color: var(--text-dark);
            word-break: break-word;
        }
        .detail-value em {
            color: var(--text-light);
            font-style: italic;
            font-size: 0.9em;
        }

        /* --- New Styling for Inline Details --- */
        .profile-details-item .details-content {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .profile-details-item .detail-label {
            margin-bottom: 0;
        }
        .profile-details-item .detail-value {
            line-height: 1;
        }

        /* --- Button Styling --- */
        .btn {
            border-radius: 25px;
            font-weight: 700;
            padding: 14px 28px;
            transition: all 0.3s ease-in-out;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }
        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-primary:hover {
            background-color: var(--secondary-green);
            border-color: var(--secondary-green);
            transform: scale(0.98);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.5);
        }
        .btn-danger {
            border-radius: 12px;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-sm-custom {
            border-radius: 12px;
            font-weight: 500;
            padding: 8px 16px;
        }

        /* Media Queries untuk Responsif */
        @media (max-width: 768px) {
            .desktop-navbar { display: none; }
            .mobile-bottom-nav { display: flex; }
            body { padding-top: 0; }
            .profile-container { align-items: flex-start; }
            .card { padding: 25px; margin-top: 10px; }
            .profile-details-item .details-content {
                display: block;
            }
            .header {
                padding: 60px 15px 40px;
                border-bottom-left-radius: 50px;
                border-bottom-right-radius: 50px;
                margin-bottom: -30px;
            }
            .header h1 { font-size: 2.5rem; }
            .header p { font-size: 1rem; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark desktop-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Bank Sampah</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'beranda.php' ? 'active' : ''); ?>" href="beranda.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'harga.php' ? 'active' : ''); ?>" href="harga.php">Setor Sampah</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'saldo.php' ? 'active' : ''); ?>" href="saldo.php">Penarikan</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'history.php' ? 'active' : ''); ?>" href="history.php">History</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>" href="profile.php">Akun</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="mobile-bottom-nav">
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <a href="beranda.php" class="<?php echo ($current_page == 'beranda.php' ? 'active' : ''); ?>"><i class="fas fa-home"></i><span>Home</span></a>
    <a href="harga.php" class="<?php echo ($current_page == 'harga.php' ? 'active' : ''); ?>"><i class="fas fa-recycle"></i><span>Setor</span></a>
    <a href="saldo.php" class="<?php echo ($current_page == 'saldo.php' ? 'active' : ''); ?>"><i class="fas fa-money-bill-wave"></i><span>Tarik</span></a>
    <a href="history.php" class="<?php echo ($current_page == 'history.php' ? 'active' : ''); ?>"><i class="fas fa-history"></i><span>History</span></a>
    <a href="profile.php" class="<?php echo ($current_page == 'profile.php' ? 'active' : ''); ?>"><i class="fas fa-user"></i><span>Akun</span></a>
</div>

<div class="header">
    <h1 class="animate__animated animate__fadeInDown">Profil Akun</h1>
    <p class="animate__animated animate__fadeInUp">Kelola informasi akun, ubah password, dan pantau saldo Anda di sini. Mudah, cepat, dan aman!</p>
</div>

<div class="profile-container">
    <div class="card mx-auto p-4">
        <h4 class="welcome-text">Halo, <span><?= htmlspecialchars($namaPengguna) ?></span>!</h4>
        <img src="<?= htmlspecialchars($fotoPath) ?>" alt="Foto Profil" class="profile-photo">
        <div class="text-center mb-4">
            <button class="btn btn-sm btn-outline-secondary btn-sm-custom" data-bs-toggle="modal" data-bs-target="#editFotoModal">
                <i class="fas fa-camera me-2"></i> Ubah Foto Profil
            </button>
        </div>

        <div class="profile-details-section">
            <div class="profile-details-item">
                <i class="fas fa-user"></i>
                <div class="details-content">
                    <span class="detail-label">Nama Pengguna:</span>
                    <span class="detail-value"><?= htmlspecialchars($namaPengguna) ?></span>
                </div>
            </div>
            <div class="profile-details-item">
                <i class="fas fa-id-card"></i>
                <div class="details-content">
                    <span class="detail-label">Nama Lengkap:</span>
                    <span class="detail-value"><?= !empty($namaLengkap) ? htmlspecialchars($namaLengkap) : '<em>~Belum diisi~</em>' ?></span>
                </div>
            </div>
            <div class="profile-details-item">
                <i class="fas fa-venus-mars"></i>
                <div class="details-content">
                    <span class="detail-label">Jenis Kelamin:</span>
                    <span class="detail-value"><?= !empty($gender) ? htmlspecialchars($gender) : '<em>~Belum diisi~</em>' ?></span>
                </div>
            </div>
            <div class="profile-details-item">
                <i class="fas fa-wallet"></i>
                <div class="details-content">
                    <span class="detail-label">Saldo Anda:</span>
                    <span class="detail-value text-success fw-bold">Rp<?= number_format($saldo, 2, ',', '.') ?></span>
                </div>
            </div>
            <div class="profile-details-item">
                <i class="fas fa-user-tag"></i>
                <div class="details-content">
                    <span class="detail-label">Role:</span>
                    <span class="detail-value badge bg-success text-white rounded-pill px-3 py-1"><?= htmlspecialchars(ucfirst($role)) ?></span>
                </div>
            </div>
        </div>

        <div class="d-grid gap-3 mt-4 bottom-buttons">
            <a href="lengkapi_profil.php" class="btn btn-primary">Ubah Profil</a>
            <a href="pw.php" class="btn btn-primary">Ubah Password</a>
        </div>
        <div class="mt-3 d-flex justify-content-center">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<div class="modal fade" id="editFotoModal" tabindex="-1" aria-labelledby="editFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="profile.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFotoModalLabel">Ubah Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="foto" class="form-label">Pilih Foto</label>
                    <input class="form-control" type="file" id="foto" name="foto" accept="image/*" required />
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="action" value="upload" class="btn btn-primary">Unggah Foto</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Hapus Foto</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="profile.php" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    }

    function showSweetAlert() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status) {
            let title, text, icon;

            switch (status) {
                case 'success_upload':
                    title = 'Berhasil!';
                    text = 'Foto profil berhasil diunggah.';
                    icon = 'success';
                    break;
                case 'success_delete':
                    title = 'Berhasil!';
                    text = 'Foto profil berhasil dihapus.';
                    icon = 'success';
                    break;
                case 'error_upload':
                    title = 'Gagal!';
                    text = 'Terjadi kesalahan saat mengunggah foto.';
                    icon = 'error';
                    break;
                case 'error_delete':
                    title = 'Gagal!';
                    text = 'Terjadi kesalahan saat menghapus foto.';
                    icon = 'error';
                    break;
                case 'error_file':
                    title = 'Gagal!';
                    text = 'Silakan pilih file foto untuk diunggah.';
                    icon = 'error';
                    break;
                case 'error_invalid_type':
                    title = 'Gagal!';
                    text = 'Hanya format JPG, JPEG, PNG, dan GIF yang diperbolehkan.';
                    icon = 'error';
                    break;
                case 'error_file_size':
                    title = 'Gagal!';
                    text = 'Ukuran file melebihi batas 2 MB.';
                    icon = 'error';
                    break;
                case 'error_no_id':
                    title = 'Gagal!';
                    text = 'ID pengguna tidak ditemukan.';
                    icon = 'error';
                    break;
                default:
                    return;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK'
            }).then(() => {
                history.replaceState(null, '', 'profile.php');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', showSweetAlert);
</script>
</body>
</html>