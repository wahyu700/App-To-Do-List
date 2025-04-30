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

$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo "Tugas tidak ditemukan.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST['judul']);
    $uploadError = null;
    $filePath = $task['file_path']; // Inisialisasi dengan path file lama

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        $fileType = mime_content_type($_FILES['file']['tmp_name']);
        $fileSize = $_FILES['file']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $uploadError = "Tipe file yang diunggah tidak diizinkan.";
        } elseif ($fileSize > $maxFileSize) {
            $uploadError = "Ukuran file terlalu besar. Maksimum " . ($maxFileSize / (1024 * 1024)) . "MB.";
        } else {
            $uploadDir = '../uploads/';
            $filename = time() . '_' . basename($_FILES['file']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                // Hapus file lama jika ada file baru berhasil diupload
                if (!empty($task['file_path'])) {
                    $oldFilePath = '../' . $task['file_path'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $filePath = 'uploads/' . $filename;
            } else {
                $uploadError = "Terjadi kesalahan saat mengupload file.";
            }
        }
    } elseif (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle error upload selain tidak ada file yang dipilih
        $uploadError = "Terjadi kesalahan saat mengupload file. Error code: " . $_FILES['file']['error'];
    }

    if ($judul !== '') {
        // Hanya update judul dan file_path jika tidak ada error upload atau jika tidak ada file baru diupload
        if (!$uploadError) {
            $stmt = $conn->prepare("
                UPDATE tasks t
                JOIN tasks_assignments ta ON t.id = ta.task_id
                SET t.judul = ?, t.file_path = ?, t.updated_at = NOW()
                WHERE t.id = ? AND ta.user_id = ?");
            $stmt->bind_param("ssii", $judul, $filePath, $id, $user_id);
            $stmt->execute();

            header("Location: tugas_saya.php");
            exit;
        } else {
            $error = $uploadError;
        }
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
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            min-height: 100vh;
            color: white;
        }
    </style>
</head>
<body class="container mt-5">
    <h3>Edit Tugas</h3>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($task['judul']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Dokumen (Opsional)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            <?php if (isset($uploadError)): ?>
                <div class="form-text text-danger"><?= $uploadError ?></div>
            <?php else: ?>
                <div class="form-text text-muted">pdf, .doc, .docx, .jpg, .jpeg, .png (maks. 2MB).</div>
            <?php endif; ?>
        </div>
        <?php if (!empty($task['file_path'])): ?>
            <p>📄 <a href="../<?= $task['file_path'] ?>" target="_blank" style="color:white;">Lihat Dokumen Lama</a></p>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="tugas_saya.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>