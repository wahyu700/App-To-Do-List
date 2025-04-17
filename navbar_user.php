<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body {
    font-family: 'Times New Roman', Times, serif;
  }
  
  .menu-box {
    background: linear-gradient(135deg, rgb(51, 127, 250), rgb(6, 32, 151));
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    padding: 15px;
  }

  .menu-box a {
    text-decoration: none;
    color: #000;
  }

  .menu-box .card:hover {
    background-color: #f0f0f0;
    cursor: pointer;
  }

  .dropdown-menu-panel {
    position: absolute;
    top: 60px;
    right: 20px;
    z-index: 1000;
    width: 250px;
    display: none;
  }

  .show-menu {
    display: block;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, rgb(51, 127, 250), rgb(6, 32, 151));">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">🗂️ To Do List</a>
    <button class="btn btn-outline-light ms-auto" id="menuToggle">☰ Menu</button>
  </div>
</nav>

<div class="dropdown-menu-panel" id="dropdownPanel">
  <div class="menu-box">
    <div class="card p-2 mb-2">
      <a href="dashboard_user.php">🏠 Dashboard</a>
    </div>
    <div class="card p-2 mb-2">
      <a href="tugas_saya.php">📝 Tugas Saya</a>
    </div>
    <div class="card p-2 mb-2">
      <a href="profil.php">👤 Profil</a>
    </div>
    <div class="card p-2">
      <a href="../logout.php">🚪 Logout</a>
    </div>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('menuToggle');
  const panel = document.getElementById('dropdownPanel');

  toggleBtn.addEventListener('click', () => {
    panel.classList.toggle('show-menu');
  });

  // Close dropdown if click outside
  document.addEventListener('click', function(event) {
    if (!panel.contains(event.target) && !toggleBtn.contains(event.target)) {
      panel.classList.remove('show-menu');
    }
  });
</script>
