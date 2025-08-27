<?php
session_start();
include "conn.php";

// Jika form register disubmit
if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $no_hp    = $_POST['no_hp'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role     = "nasabah"; // default role

    // Cek email sudah terdaftar atau belum
    $cek = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($cek->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {
        $conn->query("INSERT INTO users (nama,no_hp,email,password,role) 
                      VALUES ('$nama','$no_hp','$email','$password','$role')");
        $_SESSION['success'] = "Registrasi berhasil, silakan login!";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Nasabah - Bank Sampah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-center bg-success text-white">
                    <h2><i class="fas fa-recycle"></i> Bank Sampah</h2>
                    <h4>Register Nasabah</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Sudah punya akun? <a href="login.php">Login disini</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
