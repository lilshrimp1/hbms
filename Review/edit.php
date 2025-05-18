<?php
require_once '../Database/database.php';
require_once '../models/Review.php';
require_once '../models/Reservation.php';
require_once '../models/User.php';
session_start();
include '../layout/header.php';
include '../auth/super.php';

$database = new Database();
$conn = $database->getConnection();

Review::setConnection($conn);
Reservation::setConnection($conn);
User::setConnection($conn);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Review ID is required.</div>";
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $review = $stmt->fetch();

    if (!$review) {
        echo "<div class='alert alert-danger'>Review not found.</div>";
        exit;
    }

    $reservation = Reservation::find($review['reservation_id']);
    $user = $reservation ? User::find($reservation->user_id) : null;

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Review Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../images/bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
        }
        .data-table-container {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem; 
            margin-top: 100px; 
            margin-left: auto;
            margin-right: auto; 
            max-width: 800px;
        }
    </style>
</head>
<body>

<div class="container data-table-container">
    <h3>Edit Review Status</h3>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($review['id']) ?>">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
                <option value="Visible" <?= $review['status'] == 'Visible' ? 'selected' : '' ?>>Visible</option>
                <option value="Hidden" <?= $review['status'] == 'Hidden' ? 'selected' : '' ?>>Hidden</option>
                <option value="Deleted" <?= $review['status'] == 'Deleted' ? 'selected' : '' ?>>Deleted</option>
            </select>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </div>

        
    </form>
<body class="bg" style="background-image:url(../images/bg.png); position:fixed;">
    <div class="flex">
        <aside id="navbar" class=" text-blue-800 w-64" style="font-size: 20px; background-color: rgba(75, 216, 226, 0.75);">

            <nav class="mt-16 mb-10 p-4" style="color:white; ">
            <div class="logo text-xxl font-semibold text-white-800 flex items-center mb-5 ml-2 mt-4 justify-center" style="font-size:40px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="white" class="bi bi-house-fill" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                            <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                            </svg>
                            HBMS
                        </div>

                <div class="sidebar" id="sidebar">
                    <ul>
                        <li class="logo"><a href="../main/index.php">
                                <span class="icon"><i class="fa fa-clipboard-user"></i></span>
                                <span class="text">Dashboard</span></a>
                        </li>
                        <li><a href="../Rooms/index.php">
                                <span class="icon"><i class="fa fa-book"></i></span>
                                <span class="text">Room Management</span></a>
                        </li>
                        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Super Admin')){ ?>
                        <li><a href="../Amenities/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Amenities</span></a>
                        </li>
                        <?php } ?>
                        <li><a href="../Review/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Feedback</span></a>
                        </li>
                        <li><a href="../User/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Manage User</span></a>
                        </li>
                        <li><a href="../auth/logout.php">
                                <span class="icon"><i class="fa fa-sign-out"></i></span>
                                <span class="text">Logout</span></a>
                        </li>
                       
                    </ul>
                    </div>
                </nav>
        </aside>
        <main class="flex-1 p-8">
            <header>
                <div class="flex">
                <div class="menu-container mr-4">
                                <button id="menu-button" class="bg-white-500 text-black m flex items-center gap-2" >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                                    </svg>
                                    MENU
                                </button>
                            </div>
                            </div>

                            <div class="logo text-xl font-semibold text-gray-800 flex items-center" style="margin-left: auto; position:relative; text-align:middle;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                                </svg>
                                HBMS
                            </div>

                            <div class = "profile mr-4">
                                <div class="text-gray-600 mr-4">Super</div>
                                <img src="https://placehold.co/40x40/80ED99/fff?text=U&font=Montserrat" alt="User Avatar" class="rounded-full">
                            </div>
            </header>

<div class="data-table-container">
    <div class="card p-4 shadow" style="max-width: 800px; margin: 0 auto;">
        <h3 class="mb-4 text-center">Edit Review</h3>
        <form action="update.php" method="POST">
             <input type="hidden" name="id" value="<?= htmlspecialchars($review['id']) ?>">

            <div class="mb-3">
                <label for="rating" class="form-label">Rating (1-5)</label>
                <input type="number" class="form-control" name="rating" id="rating" min="1" max="5" value="<?= htmlspecialchars($review['rating']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea class="form-control" name="comment" id="comment" rows="4"><?= htmlspecialchars($review['comment']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="active" <?= $review['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $review['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <?php if ($user): ?>
                <hr>
                <h5>User Info</h5>
                <p><strong>Name:</strong> <?= htmlspecialchars($user->name) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user->email) ?></p>
            <?php endif; ?>

            <div class="mt-4 d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Review</button>
            </div>
        </form>
    </div>

</div>

</body>
</html>
