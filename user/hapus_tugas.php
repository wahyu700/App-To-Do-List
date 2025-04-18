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
    $stmt = $conn->prepare("
        DELETE t FROM tasks t
        JOIN tasks_assignments ta ON t.id = ta.task_id
        WHERE t.id = ? AND ta.user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
}


header("Location: tugas_saya.php");
exit;
