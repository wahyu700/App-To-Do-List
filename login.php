<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $_SESSION['login_error'] = "Username atau password tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['login_error'] = "User <strong>$username</strong> tidak ditemukan.";
        } else {
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role']    = $role;
                $_SESSION['username'] = $username;
                unset($_SESSION['login_error']);
            
                if ($role === 'admin') {
                    header("Location: admin/dashboard_admin.php");
                } else {
                    header("Location: user/dashboard_user.php");
                }
                exit;
            } else {
                $_SESSION['login_error'] = "Password salah.";
            }
        }

        $stmt->close();
    }

    $conn->close();

    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        <h3 class="text-center mb-3">Login</h3>

        <?php
        session_start();
        if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                <?= $_SESSION['login_error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="showPassword" style="cursor: pointer;" onclick="togglePassword()">
                    <label class="form-check-label" for="showPassword">
                        Tampilkan Password
                    </label>
                </div>
            </div>

            <button type="submit" class="btn w-100" style="background-color: black; color: white;">Login</button>
        </form>

        <p class="mt-3 text-center">
            Belum punya akun? <a href="register.php" class="text-white text-decoration-underline">Register di sini</a>.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const alertBox = document.getElementById('errorAlert');
        if (alertBox) {
            setTimeout(() => {
                const alert = bootstrap.Alert.getOrCreateInstance(alertBox);
                alert.close();
            }, 3000);
        }

        function togglePassword() {
            const passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>

