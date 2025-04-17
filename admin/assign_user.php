<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = (int)$_POST['task_id'];
    $assign_all = isset($_POST['assign_all']) ? 1 : 0;
    $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];

    // Debug log
    // var_dump($task_id, $assign_all, $user_ids); exit;

    // Hapus assignment lama dulu
    $deleteQuery = "DELETE FROM tasks_assignments WHERE task_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $stmt->close();

    if ($assign_all) {
        // Assign ke semua user
        $usersResult = $conn->query("SELECT id FROM users WHERE role = 'user'");
        $insertQuery = $conn->prepare("INSERT INTO tasks_assignments (task_id, user_id) VALUES (?, ?)");

        while ($user = $usersResult->fetch_assoc()) {
            $insertQuery->bind_param("ii", $task_id, $user['id']);
            $insertQuery->execute();
        }
        $insertQuery->close();
    } elseif (!empty($user_ids)) {
        // Assign hanya ke user tertentu
        $insertQuery = $conn->prepare("INSERT INTO tasks_assignments (task_id, user_id) VALUES (?, ?)");
        // Debugging: Periksa data yang diterima
echo "Task ID: $task_id<br>";
echo "User IDs: " . implode(", ", $user_ids) . "<br>";

        foreach ($user_ids as $user_id) {
            $insertQuery->bind_param("ii", $task_id, $user_id);
            $insertQuery->execute();
        }
        $insertQuery->close();
    } else {
        echo "Tidak ada user yang dipilih.";
        exit;
    }

    header("Location: semua_tugas.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h3 class="mb-4">Assign Tugas</h3>

        <form method="POST" action="assign_user.php">
            <input type="hidden" name="task_id" value="<?= isset($_GET['task_id']) ? $_GET['task_id'] : '' ?>">
            <input type="hidden" name="assign_all" value="0">

            <div class="mb-3">
                <label for="user_ids" class="form-label">Pilih User</label>
                <select name="user_ids[]" class="form-control" id="user_ids" multiple size="5" required>
                    <?php
                    // Ambil semua user dengan role 'user'
                    $users = $conn->query("SELECT id, username FROM users WHERE role = 'user'");
                    while ($user = $users->fetch_assoc()):
                    ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endwhile; ?>
                </select>
                <small class="text-muted">Gunakan Ctrl / Cmd untuk memilih lebih dari satu user</small>
            </div>

            <div class="form-check">
                <input type="checkbox" name="assign_all" value="1" id="assign_all" class="form-check-input">
                <label for="assign_all" class="form-check-label">Assign ke Semua User</label>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Assign Tugas</button>
                <a href="semua_tugas.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
