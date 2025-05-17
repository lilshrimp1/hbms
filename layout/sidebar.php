<?php
require_once '../database/Database.php';
include '../layout/header.php';

if (!isset($_SESSION)) session_start();

$currentPath = $_SERVER['REQUEST_URI'];
$currentFolder = basename(dirname($currentPath));
$currentFile = basename($currentPath);

// Page title
function getPageTitle($folder, $file) {
  switch ($folder) {
    case 'main': return 'Dashboard';
    case 'Rooms': return 'Room Management';
    case 'Amenities': return 'Amenities';
    case 'reservation': return 'Reservations';
    case 'Reservation': return 'Check In/Check Out';
    case 'User': return 'Manage User';
  }
  if ($file === 'Review.php' || $folder === 'Review') return 'Guest Feedback';
  return 'HBMS';
}
$pageTitle = getPageTitle($currentFolder, $currentFile);

// Role display
function getRoleDisplay($role) {
  switch ($role) {
    case 'Superadmin':
    case 'Admin':
    case 'Front Desk':
      return $role;
    default:
      return htmlspecialchars($role);
  }
}
$roleDisplay = $_SESSION['role'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?> - HBMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/YOUR_KIT_ID.js" crossorigin="anonymous"></script>
  <!-- Replace YOUR_KIT_ID with your FontAwesome Kit -->
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    /* Sidebar */
    #sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 260px;
      height: 100%;
      background: rgba(75, 216, 226, 0.85);
      padding-top: 100px;
      transition: left 0.3s ease;
      z-index: 1000;
    }

    #sidebar.active {
      left: 0;
    }

    .btn-sidebar {
      display: block;
      padding: 12px 20px;
      margin: 10px auto;
      width: 80%;
      font-size: 18px;
      background-color: #fff;
      border-radius: 8px;
      color: #000;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .btn-sidebar:hover {
      background-color: #e5e7eb;
    }

    .toggle-btn {
      font-size: 30px;
      cursor: pointer;
      color: white;
    }

    .navbar {
      padding: 25px;
      z-index: 1100;
    }

    .profile-info {
      display: flex;
      align-items: center;
      font-size: 20px;
      color: white;
    }

    .profile-circle {
      width: 40px;
      height: 40px;
      background-color: #80ED99;
      border-radius: 50%;
      margin-right: 10px;
    }

    main {
      transition: margin-left 0.3s ease;
      padding: 20px;
      margin-top: 100px;
    }

    main.shifted {
      margin-left: 260px;
    }

    .hbms-title, .page-title {
      font-size: 30px;
      color: white;
    }
  </style>
</head>
<body>

<!-- ✅ TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-4">
      <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
      <div class="hbms-title">HBMS</div>
    </div>
    <div class="page-title"><?php echo $pageTitle; ?></div>
    <div class="profile-info">
      <div class="profile-circle"></div>
      <div><?php echo $roleDisplay; ?></div>
    </div>
  </div>
</nav>

<!-- ✅ SIDEBAR -->
<div class="sidebar" id="sidebar">
  <ul class="nav flex-column text-center">
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../main/index.php">DASHBOARD</a></li>
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../Rooms/index.php">ROOM MANAGEMENT</a></li>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] != 'Front Desk' && $_SESSION['role'] != 'Admin'): ?>
      <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../Amenities/index.php">AMENITIES</a></li>
    <?php endif; ?>
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../reservation/index.php">RESERVATIONS</a></li>
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../Reservation/index.php">CHECK IN/CHECK OUT</a></li>
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../Review/index.php">GUEST FEEDBACK</a></li>
    <li class="nav-item mb-3"><a class="nav-link btn-sidebar" href="../User/index.php">MANAGE USER</a></li>
    <li class="nav-item mt-auto mb-4"><a class="btn btn-danger w-75" href="../auth/logout.php">LOG OUT</a></li>
  </ul>
</div>

<!-- ✅ MAIN CONTENT AREA -->
<main id="main">
  <div class="container">
    <h2>Welcome to HBMS - <?php echo $pageTitle; ?></h2>
    <!-- Your page content here -->
  </div>
</main>

<!-- ✅ JS SCRIPTS -->
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    sidebar.classList.toggle('active');
    main.classList.toggle('shifted');
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
