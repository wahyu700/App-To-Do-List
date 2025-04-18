<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config/db.php';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Cek apakah username sudah ada
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Username sudah digunakan..";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        // Insert user
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
            exit;
        } else {
            $error = "Registrasi gagal: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body, .card, .form-control, .btn {
        font-family: 'Times New Roman', Times, serif;
    }

    body {
        background: linear-gradient(135deg,rgb(6, 32, 151), rgb(51, 127, 250));

    }
</style>

</head>
<body class="bg-white d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4 text-white" style="background: linear-gradient(135deg,rgb(51, 127, 250),rgb(6, 32, 151)); border-radius: 20px; min-width: 350px;">
        <h3 class="text-center mb-3">Register</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Konfirmasi Password:</label>
                <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePassword()" style="cursor: pointer;">
                <label class="form-check-label" for="showPassword" style="cursor: pointer;">
                    Tampilkan Password
                </label>
            </div>

            <button type="submit" class="btn w-100" style="background-color: black; color: white;">Register</button>
        </form>

        <p class="mt-3 text-center">
            Sudah punya akun? <a href="login.php" class="text-white text-decoration-underline">Login di sini</a>.
        </p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePassword() {
        const password = document.getElementById("password");
        const confirm = document.getElementById("confirmPassword");

        if (password) {
            password.type = password.type === "password" ? "text" : "password";
        }

        if (confirm) {
            confirm.type = confirm.type === "password" ? "text" : "password";
        }
    }

    document.getElementById("registerForm").addEventListener("submit", function(e) {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert("Konfirmasi Password tidak cocok!");
        }
    });
</script>

</body>
</html>
