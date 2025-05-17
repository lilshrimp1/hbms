<?php 
require_once '../Database/database.php';
require_once '../models/Amenity.php';
require_once '../models/Reservation.php';

$database = new database();
$conn = $database->getConnection();
session_start();
include '../auth/super.php'; 

Amenity::setConnection($conn);
Reservation::setConnection($conn);

if (!isset($_GET['id'])) {
    die('Error: Amenity ID is not provided in the URL.');
}

$id = $_GET['id'];
$amenity = Amenity::find($id);

if (!$amenity) {
    die('Error: Amenity not found.');
}

$currentGuest = Amenity::getCurrentGuestInfo($id);
?>

<?php include '../layout/header.php'; ?>
<body class="bg" style="background-image:url(../images/bg.png); position:fixed;">
<div class="flex">
    <aside id="navbar" class="text-blue-800 w-64" style="font-size: 20px; background-color: rgba(75, 216, 226, 0.75);">
        <nav class="mt-16 mb-10 p-4" style="color:white;">
            <div class="logo text-xxl font-semibold text-white-800 flex items-center mb-5 ml-2 mt-4 justify-center" style="font-size:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="white" class="bi bi-house-fill" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                </svg>
                HBMS
            </div>
            <div class="sidebar" id="sidebar">
                <ul>
                    <li class="logo"><a href="../main/index.php"><span class="text">Dashboard</span></a></li>
                    <li><a href="../Rooms/index.php"><span class="text">Room Management</span></a></li>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] != 'Front Desk' && $_SESSION['role'] != 'Guest')) { ?>
                    <li><a href="../Amenities/index.php"><span class="text">Amenities</span></a></li>
                    <li><a href="../Reservation/index.php"><span class="text">Reservation</span></a></li>
                    <li><a href="../Review/index.php"><span class="text">Feedback</span></a></li>
                    <li><a href="../User/index.php"><span class="text">Manage User</span></a></li>
                    <li><a href="../auth/logout.php"><span class="text">Logout</span></a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </aside>
    <main class="flex-1 p-8">
        <header class="flex items-center justify-between mb-6">
            <div class="menu-container mr-4">
                <button id="menu-button" class="bg-white-500 text-black flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    MENU
                </button>
            </div>
            <div class="logo text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                </svg>
                HBMS
            </div>
            <div class="profile mr-4 flex items-center gap-2">
                <div class="text-gray-600">Super</div>
                <img src="https://placehold.co/40x40/80ED99/fff?text=U&font=Montserrat" alt="User Avatar" class="rounded-full">
            </div>
        </header>

        <div class="card shadow mb-5">
            <div class="card-header bg-info text-white">
                <h2 class="px-3">Amenity Details</h2>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?= htmlspecialchars($amenity->name) ?></p>
                <p><strong>Price:</strong> <?= htmlspecialchars($amenity->price) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($amenity->description) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($amenity->status) ?></p>
            </div>
        </div>

        <?php if ($currentGuest): ?>
        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <h3 class="px-3">Current Guest Information</h3>
            </div>
            <div class="card-body">
                <p><strong>Full Name:</strong> <?= htmlspecialchars($currentGuest->name) ?></p>
                <p><strong>Contact Number:</strong> <?= htmlspecialchars($currentGuest->contact) ?></p>
                <p><strong>Check-in:</strong> <?= htmlspecialchars($currentGuest->check_in) ?></p>
                <p><strong>Check-out:</strong> <?= htmlspecialchars($currentGuest->check_out) ?></p>
                <p><strong>Number of Guests:</strong> <?= htmlspecialchars($currentGuest->guest_count) ?></p>
            </div>
        </div>
        <?php else: ?>
        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <h3 class="px-3">Current Guest Information</h3>
            </div>
            <div class="card-body">
                <p class="text-muted"><em>No guest is currently using this amenity.</em></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuButton = document.getElementById('menu-button');
        const navbar = document.getElementById('navbar');
        const main = document.querySelector('main');

        menuButton.addEventListener('click', () => {
            navbar.classList.toggle('show');
            main.classList.toggle('shifted');
        });
    });
</script>
</body>
</html>