<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f8f9fa;
            padding-top: 80px; /* supaya isi tidak tertutup navbar */
        }
    </style>
</head>
<body>

<?php include '../navbar_user.php'; ?>

<div class="container text-center">
    <h3>🧑‍💼 Selamat Datang di Dashboard</h3>
    <p>Halo, <strong><?= htmlspecialchars($username) ?></strong> 👋</p>
</div>

</body>
</html>
