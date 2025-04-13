<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

// Ambil data statistik tugas
$totalTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks")->fetch_assoc()['total'];
$completedTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'selesai'")->fetch_assoc()['total'];
$pendingTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'belum'")->fetch_assoc()['total'];

// Filter dari query string
$filter = $_GET['filter'] ?? 'semua';

// Ambil data tugas berdasarkan filter
if ($filter === 'selesai') {
    $tasks = $conn->query("SELECT * FROM tasks WHERE status = 'selesai'");
} elseif ($filter === 'belum') {
    $tasks = $conn->query("SELECT * FROM tasks WHERE status = 'belum'");
} else {
    $tasks = $conn->query("SELECT * FROM tasks");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card h4 {
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include '../sidebar.php'; ?>

<div class="container mt-5">

    <!-- Statistik Tugas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=semua" class="text-decoration-none">
                <div class="card p-4 bg-white text-dark text-center">
                    <h4>Total Tugas</h4>
                    <p class="display-6"><?php echo $totalTasks; ?></p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=selesai" class="text-decoration-none">
                <div class="card p-4 bg-white text-dark text-center">
                    <h4>Selesai</h4>
                    <p class="display-6"><?php echo $completedTasks; ?></p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=belum" class="text-decoration-none">
                <div class="card p-4 bg-white text-dark text-center">
                    <h4>Belum Selesai</h4>
                    <p class="display-6"><?php echo $pendingTasks; ?></p>
                </div>
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Tugas -->
    <div class="card p-4">
        <h4 class="mb-4">📋 Daftar Tugas - <?= ucfirst($filter) ?></h4>
        <table class="table table-bordered text-center table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Judul Tugas</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($task = $tasks->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($task['judul']) ?></td>
                        <td><?= $task['status'] ?></td>
                        <td><?= $task['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
