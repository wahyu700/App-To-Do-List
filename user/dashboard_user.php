<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Ambil total tugas dari assignment
$sql_total = "
  SELECT COUNT(*) AS total 
  FROM tasks t 
  JOIN tasks_assignments ta ON t.id = ta.task_id 
  WHERE ta.user_id = $user_id
";
$result_total = $conn->query($sql_total)->fetch_assoc()['total'];

// Tugas yang sudah selesai
$sql_selesai = "
  SELECT COUNT(*) AS selesai 
  FROM tasks t 
  JOIN tasks_assignments ta ON t.id = ta.task_id 
  WHERE ta.user_id = $user_id AND t.status = 'selesai'
";
$result_selesai = $conn->query($sql_selesai)->fetch_assoc()['selesai'];

// Tugas yang belum selesai
$sql_belum = "
  SELECT COUNT(*) AS belum 
  FROM tasks t 
  JOIN tasks_assignments ta ON t.id = ta.task_id 
  WHERE ta.user_id = $user_id AND t.status = 'belum'
";
$result_belum = $conn->query($sql_belum)->fetch_assoc()['belum'];

// Tugas hari ini
$sql_today = "
  SELECT t.* 
  FROM tasks t 
  JOIN tasks_assignments ta ON t.id = ta.task_id 
  WHERE ta.user_id = $user_id AND DATE(t.created_at) = CURDATE()
  ORDER BY t.created_at DESC
";
$result_today = $conn->query($sql_today);

// Aktivitas terbaru
$sql_aktivitas = "
  SELECT t.* 
  FROM tasks t 
  JOIN tasks_assignments ta ON t.id = ta.task_id 
  WHERE ta.user_id = $user_id 
  ORDER BY t.updated_at DESC 
  LIMIT 5
";
$result_aktivitas = $conn->query($sql_aktivitas);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

        body {
            height: 100%;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;        
        }

        h4 {
            font-family: 'Times New Roman', Times, serif;
        }

        .list-group-item {
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

<?php include '../navbar_user.php'; ?>


  <div class="container mt-5 pt-5">
    <div class="row mb-4">

      <!-- Total tugas -->
      <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=semua" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Total Tugas</h4>
                    <p class="display-6"><?php echo $result_total; ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas yang belum selesai -->
        <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=belum" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Belum Selesai</h4>
                    <p class="display-6"><?php echo $result_belum; ?></p>
                </div>
            </a>
        </div>

          <!-- Tugas yang sudah selesai -->
          <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=selesai" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Selesai</h4>
                    <p class="display-6"><?php echo $result_selesai; ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas Hari Ini -->
        <div class="mt-5">
        <h4 class="text-white">Tugas Hari Ini</h4>
        <ul class="list-group">
            <?php if ($result_today->num_rows > 0): ?>
            <?php while($row = $result_today->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($row['judul']) ?>
                <span class="badge <?= $row['status'] == 'selesai' ? 'bg-success' : 'bg-primary' ?> badge-pill">
                    <?= ucfirst($row['status']) ?>
                </span>
                </li>
            <?php endwhile; ?>
            <?php else: ?>
            <li class="list-group-item">Tidak ada tugas hari ini.</li>
            <?php endif; ?>
        </ul>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="mt-5">
        <h4 class="text-white">Aktivitas Terbaru</h4>
        <ul class="list-group">
            <?php if ($result_aktivitas->num_rows > 0): ?>
            <?php while($row = $result_aktivitas->fetch_assoc()): ?>
                <li class="list-group-item">
                <?= $row['status'] == 'selesai' ? '✔️' : '🕒' ?>
                "<?= htmlspecialchars($row['judul']) ?>" - <?= date("d M Y H:i", strtotime($row['updated_at'])) ?>
                </li>
            <?php endwhile; ?>
            <?php else: ?>
            <li class="list-group-item">Belum ada aktivitas terbaru.</li>
            <?php endif; ?>
        </ul>
        </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
