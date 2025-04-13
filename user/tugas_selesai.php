<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Pastikan hanya tugas milik user yang bisa diubah
$stmt = $conn->prepare("UPDATE tasks SET status = 'selesai', updated_at = NOW() WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: tugas_saya.php");
exit;
