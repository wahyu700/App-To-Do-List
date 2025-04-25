<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $judul = $_POST['judul'];
    $status = $_POST['status'];

    if ($id <= 0) {
        echo "ID tidak valid.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE tasks SET judul = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $judul, $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: semua_tugas.php");
    exit;
}

// Jika dari GET (pertama kali buka modal), ambil data berdasarkan id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID tidak valid.";
    exit;
}
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT judul, status FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($judul, $status);

if (!$stmt->fetch()) {
    echo "Tugas tidak ditemukan.";
    exit;
}
$stmt->close();
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
<!-- Tombol untuk membuka modal -->
<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editTaskModal">
  ✏️ Edit Tugas
</button>

<!-- Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="edit_tugas.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editTaskModalLabel">Edit Tugas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <!-- Tambahan hidden input untuk ID -->
          <input type="hidden" name="id" value="<?= $id ?>">
          
          <div class="mb-3">
            <label for="judul" class="form-label">Judul Tugas</label>
            <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($judul) ?>" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="belum" <?= $status === 'belum' ? 'selected' : '' ?>>Belum</option>
              <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
