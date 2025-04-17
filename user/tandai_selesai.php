<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah ada ID tugas yang dikirim
if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Update status menjadi 'selesai' jika milik user ini
    $sql = "UPDATE tasks SET status = 'selesai', updated_at = NOW() WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Tugas berhasil ditandai selesai.";
    } else {
        $_SESSION['error'] = "Gagal menandai tugas.";
    }

    $stmt->close();
}

$conn->close();

header("Location: tugas_saya.php");
exit();
?>
