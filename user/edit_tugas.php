<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: dashboard_user.php');
    exit;
}

// Ambil data tugas
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo "Tugas tidak ditemukan atau bukan milik Anda.";
    exit;
}

// Jika disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST['judul']);

    if ($judul !== '') {
   // Ganti query SELECT
$stmt = $conn->prepare("
SELECT t.* FROM tasks t 
JOIN tasks_assignments ta ON t.id = ta.task_id 
WHERE t.id = ? AND ta.user_id = ?");


        header("Location: tugas_saya.php");
        exit;
    } else {
        $error = "Judul tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100%;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            min-height: 100vh; 
            color: white;
        }
    </style>
</head>
<body class="gradient-bg container mt-5">
    <h3>Edit Tugas</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($task['judul']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="tugas_saya.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
