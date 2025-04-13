<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../sidebar.php';
include '../config/db.php';

// Cek apakah ada filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Siapkan query dengan kondisi filter
$sql = "SELECT tasks.*, users.username 
        FROM tasks 
        JOIN users ON tasks.user_id = users.id";

if ($statusFilter === 'belum' || $statusFilter === 'selesai') {
    $sql .= " WHERE tasks.status = '$statusFilter'";
}

$sql .= " ORDER BY tasks.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f4f6f8;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h3 class="mb-4">📊 Laporan Tugas Semua User</h3>

        <!-- Form Filter -->
        <form method="get" class="mb-3 row g-2 align-items-center">
            <div class="col-auto">
                <label for="status" class="form-label mb-0">Filter Status:</label>
            </div>
            <div class="col-auto">
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="belum" <?= $statusFilter === 'belum' ? 'selected' : '' ?>>Belum</option>
                    <option value="selesai" <?= $statusFilter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
        </form>

        <!-- Tabel Tugas -->
        <table class="table table-bordered text-center table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Judul Tugas</th>
                    <th>Status</th>
                    <th>Dibuat Pada</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'selesai'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Belum</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date("d-m-Y H:i", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada tugas yang sesuai dengan filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
