<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $assign_all = isset($_POST['assign_all']);

    if ($assign_all) {
        // Assign ke semua user
        $users = $conn->query("SELECT id FROM users WHERE role = 'user'");
        while ($user = $users->fetch_assoc()) {
            // Cek duplikat
            $check = $conn->prepare("SELECT * FROM tasks WHERE judul = (SELECT judul FROM tasks WHERE id = ?) AND user_id = ?");
            $check->bind_param("ii", $task_id, $user['id']);
            $check->execute();
            $result = $check->get_result();
            if ($result->num_rows === 0) {
                $conn->query("INSERT INTO tasks (judul, status, user_id) SELECT judul, status, {$user['id']} FROM tasks WHERE id = $task_id");
            }
        }
        echo "<script>alert('Tugas berhasil di-assign ke semua user.'); window.location.href='semua_tugas.php';</script>";
        exit;
    }

    $user_ids = $_POST['user_ids'] ?? [];

    if (empty($user_ids)) {
        echo "<script>alert('Pilih minimal satu user sebelum meng-assign tugas.'); window.history.back();</script>";
        exit;
    }

    foreach ($user_ids as $user_id) {
        $check = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
        $check->bind_param("ii", $task_id, $user_id);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows === 0) {
            $conn->query("INSERT INTO tasks (judul, status, user_id) SELECT judul, status, {$user_id} FROM tasks WHERE id = $task_id");
        }
    }

    echo "<script>alert('Tugas berhasil di-assign ke user yang dipilih.'); window.location.href='semua_tugas.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Tugas ke User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f4f6f9;
        }
        .card {
            max-width: 600px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div class="card shadow p-4">
        <h4 class="mb-3">📌 Assign Tugas ke User</h4>

        <form method="POST" id="assignForm">
            <div class="mb-3">
                <label for="task_id" class="form-label">Pilih Tugas:</label>
                <select name="task_id" class="form-control" required>
                    <?php
                    $tasks = $conn->query("SELECT * FROM tasks WHERE user_id IS NULL");
                    while ($task = $tasks->fetch_assoc()):
                    ?>
                        <option value="<?= $task['id'] ?>"><?= htmlspecialchars($task['judul']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="assign_all" id="assign_all" class="form-check-input" onchange="toggleUserSelect(this)">
                <label for="assign_all" class="form-check-label">Assign ke Semua User</label>
            </div>

            <div class="mb-3" id="user_select">
                <label for="user_ids" class="form-label">Pilih User:</label>
                <select name="user_ids[]" class="form-control" multiple>
                    <?php
                    $users = $conn->query("SELECT * FROM users WHERE role = 'user'");
                    while ($user = $users->fetch_assoc()):
                    ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endwhile; ?>
                </select>
                <small class="text-muted">Gunakan Ctrl / Cmd untuk memilih lebih dari satu user</small>
            </div>

            <button type="submit" class="btn btn-primary w-100">Assign Sekarang</button>
        </form>
    </div>

    <script>
        function toggleUserSelect(checkbox) {
            const userSelect = document.getElementById('user_select');
            userSelect.style.display = checkbox.checked ? 'none' : 'block';
        }

        document.getElementById('assignForm').addEventListener('submit', function(e) {
            const isAllChecked = document.getElementById('assign_all').checked;
            const selectedUsers = document.querySelectorAll('select[name="user_ids[]"] option:checked');
            if (!isAllChecked && selectedUsers.length === 0) {
                alert("Pilih minimal satu user sebelum meng-assign tugas.");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
