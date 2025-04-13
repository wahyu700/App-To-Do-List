<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);

    if ($judul !== '') {
        $stmt = $conn->prepare("INSERT INTO tasks (judul, status, created_at, updated_at) VALUES (?, 'belum', NOW(), NOW())");
        $stmt->bind_param("s", $judul);
        $stmt->execute();
        $stmt->close();
        header("Location: semua_tugas.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h3>Tambah Tugas Baru</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Judul Tugas</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="semua_tugas.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
