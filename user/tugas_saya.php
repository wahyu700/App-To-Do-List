<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../config/db.php';
include 'fungsi_upload.php'; // Asumsikan kita membuat file ini untuk fungsi upload

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Proses edit tugas jika form di modal disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task_id'])) {
    $task_id = intval($_POST['edit_task_id']);
    $new_judul = trim($_POST['edit_judul']);
    $uploadError = null;
    $new_file_path = null;

    if (isset($_FILES['edit_file']) && $_FILES['edit_file']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleFileUpload($_FILES['edit_file'], '../uploads/', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], 2 * 1024 * 1024);
        if ($uploadResult['error']) {
            $uploadError = $uploadResult['message'];
        } else {
            $new_file_path = 'uploads/' . $uploadResult['filename'];

            // Hapus file lama jika ada file baru berhasil diupload
            $getOldFileStmt = $conn->prepare("SELECT file_path FROM tasks WHERE id = ?");
            $getOldFileStmt->bind_param("i", $task_id);
            $getOldFileStmt->execute();
            $oldFileResult = $getOldFileStmt->get_result();
            if ($oldFileResult->num_rows > 0) {
                $oldFile = $oldFileResult->fetch_assoc()['file_path'];
                if (!empty($oldFile)) {
                    $oldFilePath = '../' . $oldFile;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            }
            $getOldFileStmt->close();
        }
    } elseif (isset($_FILES['edit_file']) && $_FILES['edit_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadError = "Terjadi kesalahan saat mengupload file. Error code: " . $_FILES['edit_file']['error'];
    }

    if ($new_judul !== '') {
        $checkStmt = $conn->prepare("
            SELECT t.id FROM tasks t
            JOIN tasks_assignments ta ON ta.task_id = t.id
            WHERE t.id = ? AND ta.user_id = ?
        ");
        $checkStmt->bind_param("ii", $task_id, $user_id);
        $checkStmt->execute();
        $resultCheck = $checkStmt->get_result();

        if ($resultCheck->num_rows > 0) {
            if ($uploadError) {
                $message = $uploadError;
            } else {
                if ($new_file_path) {
                    $updateStmt = $conn->prepare("UPDATE tasks SET judul = ?, file_path = ?, updated_at = NOW() WHERE id = ?");
                    $updateStmt->bind_param("ssi", $new_judul, $new_file_path, $task_id);
                } else {
                    $updateStmt = $conn->prepare("UPDATE tasks SET judul = ?, updated_at = NOW() WHERE id = ?");
                    $updateStmt->bind_param("si", $new_judul, $task_id);
                }

                if ($updateStmt->execute()) {
                    $message = "Tugas berhasil diperbarui.";
                } else {
                    $message = "Gagal memperbarui tugas.";
                }
                $updateStmt->close();
            }
        } else {
            $message = "Tugas tidak ditemukan atau bukan milik Anda.";
        }
        $checkStmt->close();
    } else {
        $message = "Judul tidak boleh kosong.";
    }
}

// Ambil data tugas user
$query = "
    SELECT t.id, t.judul, t.status, t.created_at, t.updated_at, t.file_path
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
                    <?php if ($row['file_path']): ?>
                        <p><a href="../<?= $row['file_path'] ?>" target="_blank">📄 Lihat File</a></p>
                    <?php endif; ?>
                    <?php if ($row['status'] !== 'selesai'): ?>
                        <a href="tandai_selesai.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm">✔️ Tandai Selesai</a>
                    <?php endif; ?>
                    <button
                        class="btn btn-primary btn-sm"
                        data-toggle="modal"
                        data-target="#editModal"
                        data-id="<?= $row['id'] ?>"
                        data-judul="<?= htmlspecialchars($row['judul']) ?>"
                        data-file="<?= htmlspecialchars($row['file_path'] ?? '') ?>"
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
    <a href="dashboard_user.php" class="btn btn-outline-light btn-lg mb-3 btn-press">⬅️</a>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
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
                <div class="form-group">
                    <label for="edit_file">Upload Dokumen Baru (Opsional)</label>
                    <input type="file" class="form-control-file" name="edit_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted">pdf, .doc, .docx, .jpg, .jpeg, .png (maks. 2MB).</small>
                    <div id="upload_error_modal" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label>Dokumen Saat Ini:</label>
                    <p id="current_file"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#editModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const taskId = button.data('id');
            const judul = button.data('judul');
            const file = button.data('file');

            const modal = $(this);
            modal.find('#editTaskId').val(taskId);
            modal.find('#editJudul').val(judul);
            modal.find('#current_file').html(file ? `<a href="../${file}" target="_blank">Lihat File</a>` : 'Tidak ada file terlampir.');
            modal.find('#upload_error_modal').text(''); // Bersihkan pesan error sebelumnya
        });

        // Mencegah form modal disubmit jika ada error upload di sisi client (opsional, bisa ditambahkan)
        $('form[enctype="multipart/form-data"]').submit(function(e) {
            const fileInput = $(this).find('input[type="file"]')[0];
            if (fileInput.files.length > 0) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                const maxFileSize = 2 * 1024 * 1024; // 2MB
                const file = fileInput.files[0];

                if (!allowedTypes.includes(file.type)) {
                    $('#upload_error_modal').text('Tipe file tidak diizinkan.');
                    e.preventDefault();
                } else if (file.size > maxFileSize) {
                    $('#upload_error_modal').text('Ukuran file terlalu besar (maks. 2MB).');
                    e.preventDefault();
                } else {
                    $('#upload_error_modal').text('');
                }
            } else {
                $('#upload_error_modal').text(''); // Tidak ada file baru, tidak ada error
            }
        });
    });
</script>

</body>
</html>