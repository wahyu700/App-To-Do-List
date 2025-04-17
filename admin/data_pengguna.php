<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../sidebar.php';
include '../config/db.php';

// Ambil semua data user
$query = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(135deg,rgb(6, 32, 151), rgb(51, 127, 250));
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h3 class="mb-4 text-white">👥 Data Pengguna</h3>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <table class="table table-bordered text-center table-striped">
            <thead class="table-primary">
                <tr>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">No</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Username</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Email</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Role</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <?php if ($row['role'] !== 'admin'): ?>
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">📝 Edit</a>
                                    <a href="hapus_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">🗑️ Hapus</a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data pengguna.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="container mt-5 pt-5">
    <a href="dashboard_admin.php" class="btn btn-outline-light btn-lg mb-3 btn-press" style="bg-black">
        <i class="bi bi-arrow-left-circle"></i> ⬅️
    </a>
</div>
</body>
</html>
