<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Ambil data user saat ini
$query = "SELECT username, email FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_baru = mysqli_real_escape_string($conn, $_POST['username']);
    $email_baru = mysqli_real_escape_string($conn, $_POST['email']);

    $update = "UPDATE users SET username = '$username_baru', email = '$email_baru' WHERE id = $user_id";

    if (mysqli_query($conn, $update)) {
        $_SESSION['username'] = $username_baru; // perbarui sesi
        $message = "<div class='alert alert-success'>Profil berhasil diperbarui.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Gagal memperbarui profil.</div>";
    }

    // Refresh data setelah update
    $user['username'] = $username_baru;
    $user['email'] = $email_baru;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100%;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
            min-height: 100vh; 
            color: white;
        }
    </style>
</head>
<body class="gradient-bg">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background: linear-gradient(135deg, rgb(51, 127, 250), rgb(6, 32, 151));">
            ✏️ Edit Profil
        </div>
        <div class="card-body">
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nama Pengguna</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
                <a href="profil.php" class="btn btn-secondary">⬅️ Kembali</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
