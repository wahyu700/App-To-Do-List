<?php
session_start();
include '../config/db.php';

// Cek apakah user sudah login dan berperan sebagai user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data tugas user dari tabel relasi tasks_assignments
$query = "
    SELECT t.id, t.judul, t.status, t.created_at, t.updated_at
    FROM tasks_assignments ta
    INNER JOIN tasks t ON ta.task_id = t.id
    WHERE ta.user_id = ?
    ORDER BY t.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tugas Saya</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4 text-white">📋 Tugas Saya</h3>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                    <p class="card-text">
                        <strong>Status:</strong> <?= $row['status'] === 'selesai' ? '✅ Selesai' : '⏳ Belum Selesai' ?>
                    </p>
                    <?php if ($row['status'] !== 'selesai'): ?>
                        <a href="tandai_selesai.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✔️ Tandai Selesai</a>
                    <?php endif; ?>
                    <a href="edit_tugas.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">📝 Edit</a>
                    <a href="hapus_tugas.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus tugas ini?')">🗑️ Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada tugas yang ditugaskan untuk Anda.</div>
    <?php endif; ?>
</div>

<?php include '../navbar_user.php'; ?>

<div class="container mt-5 pt-5">
    <a href="dashboard_user.php" class="btn btn-outline-light btn-lg mb-3 btn-press">
        ⬅️ Kembali ke Dashboard
    </a>
</div>

</body>
</html>
