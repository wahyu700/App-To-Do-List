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
// Ganti query SELECT
$stmt = $conn->prepare("
    SELECT t.* FROM tasks t 
    JOIN tasks_assignments ta ON t.id = ta.task_id 
    WHERE t.id = ? AND ta.user_id = ?");

header("Location: tugas_saya.php");
exit;
