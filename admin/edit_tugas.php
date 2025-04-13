<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID tidak valid.";
    exit;
}
$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE tasks SET judul = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $judul, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: semua_tugas.php");
    exit;
}

$stmt = $conn->prepare("SELECT judul, status FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($judul, $status);

if (!$stmt->fetch()) {
    echo "Tugas tidak ditemukan.";
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Edit Tugas</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Tugas</label>
            <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($judul) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="belum" <?= $status === 'belum' ? 'selected' : '' ?>>Belum</option>
                <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="semua_tugas.php" class="btn btn-secondary">Batal</a>
    </form>
</body>
</html>
