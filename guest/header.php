<?php
require_once '../Database/database.php';
require_once '../layout/modals/modals.php';
require_once '../models/User.php';
require_once '../models/guest.php';


$database = new database();
$conn = $database->getConnection();
$guest = new Guest();
$modals = new Modals();

if (!$conn) {
    die("Failed to connect to the database."); // Stop if no connection
}

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

User::setConnection($conn);
$user = User::find($_SESSION['user_id']);


?>
<!DOCTYPE html>
<html lang="en">  
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HBMS Room Services</title>
  <!-- Bootstrap CDN -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">

    <a class="navbar-brand fw-bold" href="index.php" style="margin-left: 200px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
        <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
      </svg>
      HBMS
    </a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item" style="margin-right:100px;"><a class="nav-link fw-semibold" href="index.php">Home</a></li>
        <li class="nav-item" style="margin-right:100px;"><a class="nav-link fw-semibold" href="accommodation.php">Accommodation</a></li>
        <li class="nav-item" style="margin-right:450px;"><a class="nav-link fw-semibold" href="profile.php">Manage Profile</a></li>
        <li class="nav-item d-flex align-items-center gap-3" style="margin-right: 100px;">
          <a class="nav-link p-0" href="profile.php"><i class="bi bi-person-circle"></i> <?php echo $user->name; ?></a>
          <form action="../auth/logout.php" method="POST" style="margin: 0; padding: 0;">
            <button type="submit" class="btn btn-link p-3" style="padding: 20px; border: none; background: none; color: red; cursor: pointer; text-decoration: none; margin-right:-30px;">
              <i class="bi bi-box-arrow-right"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>