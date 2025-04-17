<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil filter jika ada
$filter = $_GET['filter'] ?? 'semua';

// Set filter kondisi dan judul
$filter_condition = "";
if ($filter === 'selesai') {
    $filter_condition = "AND t.status = 'selesai'";
    $title = "Tugas Selesai";
} elseif ($filter === 'belum') {
    $filter_condition = "AND t.status = 'belum'";
    $title = "Tugas Belum Selesai";
} else {
    $filter_condition = "";
    $title = "Semua Tugas";
}

// Ambil semua tugas yang dibuat oleh user ATAU di-assign ke user
$sql = "
SELECT t.*
FROM tasks t
LEFT JOIN tasks_assignments ta ON t.id = ta.task_id
WHERE (t.user_id = $user_id OR ta.user_id = $user_id)
$filter_condition
ORDER BY t.created_at DESC
";

$result = $conn->query($sql);

// Hitung total, selesai, belum
$result_total = $conn->query("
    SELECT COUNT(*) AS total
    FROM tasks t
    LEFT JOIN tasks_assignments ta ON t.id = ta.task_id
    WHERE t.user_id = $user_id OR ta.user_id = $user_id
")->fetch_assoc()['total'];

$result_selesai = $conn->query("
    SELECT COUNT(*) AS total
    FROM tasks t
    LEFT JOIN tasks_assignments ta ON t.id = ta.task_id
    WHERE (t.user_id = $user_id OR ta.user_id = $user_id) AND t.status = 'selesai'
")->fetch_assoc()['total'];

$result_belum = $conn->query("
    SELECT COUNT(*) AS total
    FROM tasks t
    LEFT JOIN tasks_assignments ta ON t.id = ta.task_id
    WHERE (t.user_id = $user_id OR ta.user_id = $user_id) AND t.status = 'belum'
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
        html, body {
          height: 100%;
          margin: 0;
          display: flex;
          flex-direction: column;
        }

        .content {
          flex: 1;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: linear-gradient(135deg,rgb(6, 32, 151), rgb(51, 127, 250));
        }

        h4 {
            font-family: 'Times New Roman', Times, serif;
        }

        .list-group-item {
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>
<body>

<?php include '../navbar_user.php'; ?>

<div class="container mt-5 pt-5">

    <div class="row mb-4">
        <!-- Total tugas -->
        <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=semua" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Total Tugas</h4>
                    <p class="display-6"><?= $result_total ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas yang belum selesai -->
        <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=belum" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Belum Selesai</h4>
                    <p class="display-6"><?= $result_belum ?></p>
                </div>
            </a>
        </div>

        <!-- Tugas yang sudah selesai -->
        <div class="col-md-4">
            <a href="daftar_tugas_user.php?filter=selesai" class="text-decoration-none">
                <div class="card p-4 text-white text-center"
                    style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px;">
                    <h4>Selesai</h4>
                    <p class="display-6"><?= $result_selesai ?></p>
                </div>
            </a>
        </div>
    </div>

    <h3 class="text-white"><?= $title ?></h3>

  <ul class="list-group">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($row['judul']) ?>
          <span class="badge <?= $row['status'] === 'selesai' ? 'bg-success' : 'bg-warning' ?>">
            <?= ucfirst($row['status']) ?>
          </span>
        </li>
      <?php endwhile; ?>
    <?php else: ?>
      <li class="list-group-item">Tidak ada tugas yang ditemukan.</li>
    <?php endif; ?>
  </ul>
</div>

<div class="container mt-5 pt-5">
    <a href="dashboard_user.php" class="btn btn-outline-light btn-lg mb-3 btn-press">
        ⬅️ Kembali ke Dashboard
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
