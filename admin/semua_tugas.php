<?php
$selected_task_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../sidebar.php';
include '../config/db.php';

// Ambil data semua tugas beserta user yang di-assign
$query = "SELECT tasks.*, users.username FROM tasks LEFT JOIN users ON tasks.user_id = users.id ORDER BY tasks.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Semua Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h3 class="mb-4">📋 Semua Tugas</h3>

        <div class="mb-3">
            <a href="tambah_tugas.php" class="btn btn-success">➕ Tambah Tugas</a>
        </div>

        <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Assigned ke</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= $row['status'] === 'selesai' ? '✅ Selesai' : '⏳ Belum' ?></td>
                            <td><?= $row['username'] ?? '<i>Belum di-assign</i>' ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['updated_at'] ?></td>
                            <td>
                                <a href="edit_tugas.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">📝 Edit</a>
                                <a href="hapus_tugas.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?')">🗑️ Hapus</a>
                                <a href="assign_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">👤 Assign</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada tugas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>