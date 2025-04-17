<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama = mysqli_real_escape_string($conn, $_POST['password_lama']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['password_baru']);
    $konfirmasi_password = mysqli_real_escape_string($conn, $_POST['konfirmasi_password']);

    // Ambil password lama dari database
    $query = "SELECT password FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if (password_verify($password_lama, $data['password'])) {
        if ($password_baru === $konfirmasi_password) {
            $password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$password_baru_hash' WHERE id = $user_id";

            if (mysqli_query($conn, $update_query)) {
                $message = "<div class='alert alert-success'>Password berhasil diubah.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Gagal mengubah password.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>Konfirmasi password tidak cocok.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Password lama salah.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ubah Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
                body {
            height: 100%;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            min-height: 100vh; 
            color: white;
        }
    </style>
</head>
<body class="gradient-bg">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));">
            🔑 Ubah Password
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="password_lama" class="form-label">Password Lama</label>
                    <input type="password" class="form-control" name="password_lama" id="password_lama" required>
                </div>
                <div class="mb-3">
                    <label for="password_baru" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" name="password_baru" id="password_baru" required>
                </div>
                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" name="konfirmasi_password" id="konfirmasi_password" required>
                </div>
                <button type="submit" class="btn btn-warning">🔐 Simpan Perubahan</button>
                <a href="profil.php" class="btn btn-secondary">⬅️ Kembali</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
