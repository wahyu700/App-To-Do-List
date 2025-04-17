<?php
session_start();
include '../config/db.php';

// Cek jika admin & ada ID user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: data_pengguna.php');
    exit;
}

$userId = (int) $_GET['id'];

// Ambil data user
$query = $conn->query("SELECT * FROM users WHERE id = $userId");
$user = $query->fetch_assoc();

if (!$user) {
    $_SESSION['message'] = 'User tidak ditemukan.';
    header('Location: data_pengguna.php');
    exit;
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRole = $_POST['role'];
    $conn->query("UPDATE users SET role = '$newRole' WHERE id = $userId");

    $_SESSION['message'] = 'Role pengguna berhasil diperbarui.';
    header('Location: data_pengguna.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Role Pengguna</title>
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
    <div class="container">
        <h3 class="mb-4">📝 Edit Role: <?= htmlspecialchars($user['username']) ?></h3>

        <form method="POST">
            <div class="mb-3">
                <label for="role" class="form-label">Pilih Role Baru</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="data_pengguna.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
