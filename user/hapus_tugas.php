<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if ($id) {
    // Hapus hanya jika tugas milik user
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

header("Location: tugas_saya.php");
exit;
