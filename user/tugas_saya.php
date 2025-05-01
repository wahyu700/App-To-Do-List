<?php
session_start();
include '../config/db.php';

// Cek apakah user sudah login dan berperan sebagai user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Proses edit tugas jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task_id'])) {
    $task_id = intval($_POST['edit_task_id']);
    $new_judul = trim($_POST['edit_judul']);

    if ($new_judul !== '') {
        // Pastikan tugas milik user
        $checkStmt = $conn->prepare("
            SELECT t.id FROM tasks t
            JOIN tasks_assignments ta ON ta.task_id = t.id
            WHERE t.id = ? AND ta.user_id = ?
        ");
        $checkStmt->bind_param("ii", $task_id, $user_id);
        $checkStmt->execute();
        $resultCheck = $checkStmt->get_result();

        if ($resultCheck->num_rows > 0) {
            // Update tugas
            $updateStmt = $conn->prepare("UPDATE tasks SET judul = ?, updated_at = NOW() WHERE id = ?");
            $updateStmt->bind_param("si", $new_judul, $task_id);
            $updateStmt->execute();
            $message = "Tugas berhasil diperbarui.";
        } else {
            $message = "Tugas tidak ditemukan atau bukan milik Anda.";
        }
    } else {
        $message = "Judul tidak boleh kosong.";
    }
}

// Ambil data tugas user dari tabel relasi tasks_assignments
$query = "
    SELECT t.id, t.judul, t.status, t.created_at, t.updated_at
    FROM tasks_assignments ta
    INNER JOIN tasks t ON ta.task_id = t.id
    WHERE ta.user_id = ?
    ORDER BY t.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tugas Saya</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4 text-white">📋 Tugas Saya</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                    <p class="card-text">
                        <strong>Status:</strong> <?= $row['status'] === 'selesai' ? '✅ Selesai' : '⏳ Belum Selesai' ?>
                    </p>
                    <?php if ($row['status'] !== 'selesai'): ?>
                        <a href="tandai_selesai.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✔️ Tandai Selesai</a>
                    <?php endif; ?>
                    <!-- Tombol untuk membuka modal edit -->
                    <button 
                        class="btn btn-primary btn-sm" 
                        data-toggle="modal" 
                        data-target="#editModal" 
                        data-id="<?= $row['id'] ?>" 
                        data-judul="<?= htmlspecialchars($row['judul']) ?>"
                    >📝 Edit</button>
                    <a href="hapus_tugas.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus tugas ini?')">🗑️ Hapus</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada tugas yang ditugaskan untuk Anda.</div>
    <?php endif; ?>
</div>

<?php include '../navbar_user.php'; ?>

<div class="container mt-5 pt-5">
    <a href="dashboard_user.php" class="btn btn-outline-light btn-lg mb-3 btn-press">
        ⬅️
    </a>
</div>

<!-- Modal Edit Tugas -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="edit_task_id" id="editTaskId">
                <div class="form-group">
                    <label for="editJudul">Judul Tugas</label>
                    <input type="text" class="form-control" name="edit_judul" id="editJudul" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Script untuk isi data ke modal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $('#editModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const taskId = button.data('id');
        const judul = button.data('judul');

        const modal = $(this);
        modal.find('#editTaskId').val(taskId);
        modal.find('#editJudul').val(judul);
    });
</script>

</body>
</html>