<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #0d6efd;
        position: fixed;
        top: 0;
        left: -250px;
        transition: all 0.3s;
        padding-top: 10px;
        z-index: 1000;
    }

    .sidebar.show {
        left: 0;
    }

    .sidebar a {
        color: #fff;
        display: block;
        padding: 15px 20px;
        text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #ffffff;
        color: #0d6efd;
        font-weight: bold;
    }

    .menu-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        background-color: #0d6efd;
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 18px;
        z-index: 1100;
        border-radius: 5px;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 900;
    }

    .overlay.show {
        display: block;
    }

    .sidebar-header {
        font-size: 18px;
        padding-top: 0px;
        margin: 5px;
        justify-content: center;
    }


</style>

<!-- Tombol Menu -->
<button class="btn border-0 bg-transparent" id="menu-toggle" onclick="toggleSidebar()" style="position: fixed; top: 15px; left: 50px; z-index: 1100;">
    <span style="font-size: 24px;">&#9776;</span>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header d-flex align-items-center text-white fw-bold">
    <span class="me-2" style="font-size: 22px;">🗂️</span>
    <span>To Do List</span>
  </div>
  <hr style="border: 2px solid white; margin: 0;">

  <a href="dashboard_admin.php" class="<?= $current_page === 'dashboard_admin.php' ? 'active' : '' ?>">🏠 Dashboard</a>
  <hr style="border: 1px solid white; width: 100%; margin: 0;">
  <a href="semua_tugas.php" class="<?= $current_page === 'semua_tugas.php' ? 'active' : '' ?>">📋 Semua Tugas</a>
  <hr style="border: 1px solid white; width: 100%; margin: 0;">
  <a href="data_pengguna.php" class="<?= $current_page === 'data_pengguna.php' ? 'active' : '' ?>">👥 Data Pengguna</a>
  <hr style="border: 1px solid white; width: 100%; margin: 0;">
  <a href="laporan_tugas.php" class="<?= $current_page === 'laporan_tugas.php' ? 'active' : '' ?>">📈 Laporan</a>
  <hr style="border: 1px solid white; width: 100%; margin: 0;">
  <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
  <hr style="border: 1px solid white; width: 100%; margin: 0;">
</div>


<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        const toggleBtn = document.getElementById("menu-toggle");

        sidebar.classList.toggle("show");
        overlay.classList.toggle("show");

        // Sembunyikan tombol ketika sidebar terbuka
        if (sidebar.classList.contains("show")) {
            toggleBtn.classList.add("d-none");
        } else {
            toggleBtn.classList.remove("d-none");
        }
    }

    function closeSidebar() {
        document.getElementById("sidebar").classList.remove("show");
        document.getElementById("overlay").classList.remove("show");
        document.getElementById("menu-toggle").classList.remove("d-none");
    }
</script>
