<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$selected_task_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../sidebar.php';
include '../config/db.php';

// Ambil data semua tugas beserta user yang di-assign
$query = "SELECT tasks.*, users.username FROM tasks LEFT JOIN users ON tasks.user_id = users.id ORDER BY tasks.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Semua Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Times New Roman', serif;
        background: linear-gradient(135deg, rgb(6, 32, 151), rgb(51, 127, 250));
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <h3 class="mb-4 text-white">📋 Semua Tugas</h3>

        <div class="mb-3">
            <!-- Tombol untuk membuka modal tambah tugas -->
            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#tambahTugasModal">➕ Tambah
                Tugas</button>

            <!-- Modal Tambah Tugas -->
            <div class="modal fade" id="tambahTugasModal" tabindex="-1" aria-labelledby="tambahTugasModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="tambah_tugas.php" class="modal-content">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">
                            <h5 class="modal-title w-100 text-center" id="tambahTugasModalLabel">Tambah Tugas Baru</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="mb-3 text-start">
                                <label for="judul_tugas" class="form-label ps-2">Judul Tugas</label>
                                <input type="text" class="form-control" id="judul_tugas" name="judul"
                                    placeholder="Masukkan judul tugas" required>
                            </div>

                            <div class="mb-3 text-start">
                                <label for="status_tugas" class="form-label ps-2">Status</label>
                                <select class="form-select" id="status_tugas" name="status" required>
                                    <option value="belum" selected>Belum</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Tambah</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
                <tr>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">No</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Judul</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Status</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Assigned ke</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Dibuat</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Diupdate</th>
                    <th style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= $row['status'] === 'selesai' ? '✅ Selesai' : '⏳ Belum' ?></td>
                    <td>
                        <?php
    $taskId = $row['id'];
    $assignedUsers = $conn->query("SELECT u.username FROM tasks_assignments ta JOIN users u ON ta.user_id = u.id WHERE ta.task_id = $taskId");
    if ($assignedUsers->num_rows > 0) {
        $usernames = [];
        while ($user = $assignedUsers->fetch_assoc()) {
            $usernames[] = htmlspecialchars($user['username']);
        }
        echo implode(', ', $usernames);
    } else {
        echo "<i>Belum di-assign</i>";
    }
    ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= $row['updated_at'] ?></td>
                    <td>
                        <!-- Tombol Edit -->
                        <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $row['id'] ?>">📝 Edit</a>

                        <!-- Modal Edit Tugas -->
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #337FFA, #062097); color: white;">
                                        <h5 class="modal-title w-100 text-center" id="editModalLabel<?= $row['id'] ?>">
                                        >
                                          Edit Tugas</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="edit_tugas.php">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                            <div class="mb-3 text-start">
                                                <label for="judul<?= $row['id'] ?>" class="form-label ps-2">Judul
                                                    Tugas</label>
                                                <input type="text" class="form-control" name="judul"
                                                    id="judul<?= $row['id'] ?>"
                                                    value="<?= htmlspecialchars($row['judul']) ?>" required>
                                            </div>

                                            <div class="mb-3 text-start">
                                                <label for="status<?= $row['id'] ?>"
                                                    class="form-label ps-2">Status</label>
                                                <select class="form-select" name="status" id="status<?= $row['id'] ?>">
                                                    <option value="belum"
                                                        <?= $row['status'] === 'belum' ? 'selected' : '' ?>>Belum
                                                    </option>
                                                    <option value="selesai"
                                                        <?= $row['status'] === 'selesai' ? 'selected' : '' ?>>Selesai
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <a href="hapus_tugas.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus tugas ini?')">🗑️ Hapus</a>

                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#assignUserModal<?= $row['id'] ?>">👤 Assign</a>

                        <!-- Modal untuk Assign ke User -->
                        <div class="modal fade" id="assignUserModal<?= $row['id'] ?>" tabindex="-1"
                            aria-labelledby="assignUserModalLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="assign_user.php" id="assignForm_<?= $row['id'] ?>">
                                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="user_id_<?= $row['id'] ?>" class="form-label">Pilih
                                                    User</label>
                                                <select name="user_ids[]" class="form-control"
                                                    id="user_select_<?= $row['id'] ?>" multiple size="5" required>
                                                    <?php
                                                            $users = $conn->query("SELECT * FROM users WHERE role = 'user'");
                                                            while ($user = $users->fetch_assoc()):
                                                            ?>
                                                    <option value="<?= $user['id'] ?>">
                                                        <?= htmlspecialchars($user['username']) ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="assign_all" value="1"
                                                    id="assign_all_<?= $row['id'] ?>" class="form-check-input">
                                                <label for="assign_all_<?= $row['id'] ?>"
                                                    class="form-check-label">Assign ke Semua User</label>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Assign Tugas</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada tugas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-5 pt-5">
        <a href="dashboard_admin.php" class="btn btn-outline-light btn-lg mb-3 btn-press" style="bg-black">
            <i class="bi bi-arrow-left-circle"></i> ⬅️
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[id^="assignForm_"]').forEach(form => {
            const taskId = form.getAttribute('id').split('_')[1];
            const userSelect = document.getElementById(`user_select_${taskId}`);
            const assignAllCheckbox = document.getElementById(`assign_all_${taskId}`);

            assignAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    userSelect.removeAttribute('required');
                } else {
                    userSelect.setAttribute('required', 'required');
                }
            });

            form.addEventListener('submit', function(e) {
                if (!assignAllCheckbox.checked) {
                    const selectedUsers = Array.from(userSelect.options).filter(opt => opt
                        .selected);
                    if (selectedUsers.length === 0) {
                        alert("Pilih minimal satu user sebelum meng-assign tugas.");
                        e.preventDefault();
                    }
                }
            });
        });
    });
    </script>

</body>

</html>