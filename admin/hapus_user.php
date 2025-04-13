<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cegah penghapusan admin
    $check = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($role);
    $check->fetch();
    $check->close();

    if ($role !== 'admin') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal menghapus user.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Tidak bisa menghapus user dengan role admin.";
    }
}

$conn->close();
header('Location: data_pengguna.php');
exit;
