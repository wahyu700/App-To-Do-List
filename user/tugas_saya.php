<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';

// Cek apakah user sudah login dan berperan sebagai user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil data tugas user dari database
$query = "SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tugas Saya</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;

        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4">📋 Tugas Saya</h3>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                    <p class="card-text">
                        <strong>Status:</strong> <?= $row['status'] == 'selesai' ? '✅ Selesai' : '⏳ Belum Selesai' ?>
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
        <div class="alert alert-info text-center">Belum ada tugas yang ditambahkan.</div>
    <?php endif; ?>

</div>
</body>
<?php include '../navbar_user.php'; ?>

</html>
