<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil data user dari database
$query = "SELECT username, email, role, created_at FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Ambil jumlah tugas
$tugas_query = "SELECT 
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) AS selesai,
    SUM(CASE WHEN status = 'belum' THEN 1 ELSE 0 END) AS belum 
    FROM tasks WHERE user_id = $user_id";
$tugas_result = mysqli_query($conn, $tugas_query);
$tugas = mysqli_fetch_assoc($tugas_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar_user.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            👤 Profil Saya
        </div>
        <div class="card-body">
            <p><strong>Nama Pengguna:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Peran:</strong> <?= ucfirst($user['role']) ?></p>
            <p><strong>Tanggal Registrasi:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></p>
            <hr>
            <h6><strong>Status Tugas:</strong></h6>
            <p>✅ Selesai: <strong><?= $tugas['selesai'] ?? 0 ?></strong></p>
            <p>⏳ Belum Selesai: <strong><?= $tugas['belum'] ?? 0 ?></strong></p>
            <hr>
            <a href="ubah_password.php" class="btn btn-warning btn-sm">🔑 Ubah Password</a>
            <a href="edit_profil.php" class="btn btn-info btn-sm">✏️ Edit Profil</a>
        </div>
    </div>
</div>
</body>
</html>
