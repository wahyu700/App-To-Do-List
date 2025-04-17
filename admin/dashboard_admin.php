<?php
// Mulai sesi untuk mengecek autentikasi
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Menghubungkan ke database
include '../config/db.php';

// Ambil data statistik tugas dari database
$totalTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks")->fetch_assoc()['total'];
$completedTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'selesai'")->fetch_assoc()['total'];
$pendingTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'belum'")->fetch_assoc()['total'];
$inProgressTasks = $conn->query("SELECT COUNT(*) AS total FROM tasks WHERE status = 'proses'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100%;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card h4 {
            font-weight: bold;
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            min-height: 100vh; 
            color: white;
        }
    </style>
</head>
<body class="gradient-bg">

<!-- Sidebar navigation -->
<?php include '../sidebar.php'; ?>

<!-- Konten utama dashboard -->
<div class="container mt-5">
    <!-- Statistik tugas dalam bentuk 3 kolom -->
    <div class="row mb-4">
        <!-- Total tugas -->
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=semua" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Total Tugas</h4>
                    <p class="display-6"><?php echo $totalTasks; ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas yang sudah selesai -->
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=selesai" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Selesai</h4>
                    <p class="display-6"><?php echo $completedTasks; ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas yang belum selesai -->
        <div class="col-md-4">
            <a href="daftar_tugas.php?filter=belum" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Belum Selesai</h4>
                    <p class="display-6"><?php echo $pendingTasks; ?></p>
                </div>
            </a>
        </div>

    <!-- Tugas yang Sedang Dikerjakan -->
    <div class="mt-5">
        <h4 class="mb-3 text-white">
            🛠️ Tugas yang Sedang Dikerjakan
        </h4>
        <div class="table-responsive">
            <table class="table table-bordered bg-white text-center">
                 <thead class="table-light">
                    <tr>
                        <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Judul</th>
                        <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Status</th>
                        <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil tugas dengan status 'belum'
                    $inProgress = $conn->query("SELECT * FROM tasks WHERE status = 'belum'");
                    if ($inProgress->num_rows > 0):
                        while ($row = $inProgress->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td>⏳ <?= ucfirst($row['status']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tidak ada tugas yang sedang dikerjakan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan informasi admin -->
    <div class="card p-4">
        <h4 class="mb-3">📌 Ringkasan</h4>
        <p>Selamat datang di Dashboard Admin. Gunakan menu di samping untuk mengelola tugas, pengguna, dan laporan tugas.</p>
    </div>
</div>

<!-- Link Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>