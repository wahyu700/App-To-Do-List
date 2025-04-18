<?php
session_start();
include '../config/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Update status tugas jika user memang di-assign ke tugas tersebut
    $sql = "UPDATE tasks t
            JOIN tasks_assignments ta ON t.id = ta.task_id
            SET t.status = 'selesai', t.updated_at = NOW()
            WHERE t.id = ? AND ta.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['message'] = "Tugas berhasil ditandai selesai.";
    } else {
        $_SESSION['error'] = "Gagal menandai tugas. Mungkin tugas bukan milik Anda.";
    }

    $stmt->close();
}

$conn->close();
header("Location: tugas_saya.php");
exit();
?>
