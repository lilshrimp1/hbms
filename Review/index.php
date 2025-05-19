<?php 
require_once '../Database/database.php'; 
require_once '../models/Review.php';
session_start();
require_once '../models/User.php';
require_once '../models/Reservation.php';
include '../layout/header.php';
include '../auth/super.php';


$database = new database();
$conn = $database->getConnection();

Review::setConnection($conn);
Reservation::setConnection($conn);
User::setConnection($conn);


// Filter handling
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Only one filter at a time
if ($rating_filter) {
    $reviews = Review::where('rating', '=', $rating_filter);
} elseif ($date_filter) {
    $reviews = Review::where('created_at', 'LIKE', $date_filter . '%');
} elseif ($status_filter) {
    $reviews = Review::where('status', '=', $status_filter);
} else {
    $reviews = Review::all();
}

// Ensure $reviews is always an array
if (!$reviews) {
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background: url('../images/bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
        }

        #navbar {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 10;
            overflow-y: auto;
            background-color: rgba(75, 216, 226, 0.75);
            width: 300px;
        }

        #navbar.show {
            display: flex;
            opacity: 1;
        }

        .menu-container {
            position: relative;
            display: inline-block;
        }

        #menu-button {
            cursor: pointer;
        }

        main {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }

        main.shifted {
            margin-left: 8rem;
        }

        .data-table-container {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem; 
            margin-top:70px; 
            margin-left: 150px; 
            margin-right: auto; 
            max-width: 95%; 
        }

        .overflow-x-auto {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }

        .min-w-full {
            width: 50%;
            border-collapse: separate; 
            border-spacing: 0;
        }

        .min-w-full thead th {
            background-color: #f9fafb; 
            color: #374151; 
            padding: 0.75rem 1rem; 
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap; 
            padding-top: 1rem; 
            padding-bottom: 1rem; 
        }

        .min-w-full tbody td {
            padding: 0.6rem 1rem; 
            font-size: 0.875rem;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
            text-align: center; 
        }
            .action-button {
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            line-height: 1.25rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }


        header {
            background-color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 20;
        }

        header .logo {
            margin-right: auto;
            margin-left: auto;
        }

        header .profile {
            display: flex;
            align-items: center;
        }

        #navbar nav {
            display: flex;
            flex-direction: column; 
            align-items: center; 
            padding-top: 30px; 
        }

        #navbar nav a {
            background-color: #fff;
            color: #000;
            margin-top: 10px; 
            padding: 15px 15px; 
            border-radius: 0.5rem;
            width: 90%;
            text-align: center;
            font-size: 20px; 
            display: flex;
            align-items: center;
            justify-content: center;

        }
        #navbar nav a:hover {
            background-color: #e5e7eb;
        }
        #menu-button:hover{
            background-color:rgb(255, 255, 255);
        }

        .sidebar ul li a {
        padding: 12px 20px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        transition: 0.3s;
        display: flex;
        align-items: center;
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
    }

        .action-button {
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            line-height: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .view-button {
            background-color:rgb(137, 190, 252); 
            color: white;
            text-decoration: none;
        }

        .view-button:hover {
            background-color:rgb(135, 193, 247); 
        }

        .edit-button {
            background-color: #fef08a;
            color: #1e293b;
            text-decoration: none; 
        }
        .edit-button:hover{
             background-color: #fde047;
        }

        .deactivate-button {
            background-color: #fecaca;
            color: #7f1d1d;
            text-decoration: none;
        }
</style>
</head>

<body class="bg" style="background-image:url(../images/bg.png); position:fixed; margin-left: 40px;">
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
                        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Librarian' && $_SESSION['role'] != 'Admin')){ ?>
                        <li><a href="../Amenities/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Amenities</span></a>
                        </li>
                        <li><a href="../main/pdf.php">
                                <span class="icon"><i class="fa fa-file-pdf"></i></span>
                                <span class="text">Feedback</span></a>
                        </li>
                        <li><a href="../Reservation/index.php">
                                <span class="icon"><i class="fa fa-file-pdf"></i></span>
                                <span class="text">Reservation</span></a>
                        </li>
                        <li><a href="../auth/logout.php">
                                <span class="icon"><i class="fa fa-sign-out"></i></span>
                                <span class="text">Logout</span></a>
                        </li>
                       
                    </ul>
                    </div>
                </nav>
        </aside>
        <?php } ?>
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
    <div class="card shadow">
        <div class="card-header bg-danger-subtle text-white d-flex justify-content-between">
            <h2 class="text-left px-3">REVIEWS</h2>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="reviewsTable" class="table table-striped table-hovered table-bordered mt-4 mb-9">
                        <tr>
                            <th style = "text-align: center;">#</th>
                            <th style = "text-align: center;">Reviewer Name</th>
                            <th style = "text-align: center;">Rating</th>
                            <th style = "text-align: center;">Comment</th>
                            <th style = "text-align: center;">Status</th>
                            <th style = "text-align: center;">Created At</th>
                            <th style = "text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reviews as $review): 
                            $reservation = Reservation::find($review->reservation_id);
                            $userResult = User::where('id', '=', $reservation->user_id);
                            $user = is_array($userResult) && !empty($userResult) ? $userResult[0] : null;
                            $room = Room::find($reservation->room_id);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($review->id) ?></td>
                                <td><?= htmlspecialchars($user->name) ?></td>
                                <td><?= htmlspecialchars($review->rating) ?></td>
                                <td><?= htmlspecialchars($review->comment) ?></td>
                                <td><?= htmlspecialchars($review->status) ?></td>
                                <td><?= htmlspecialchars($review->created_at) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                    <a href="show.php?id=<?= $review->id ?>" class="action-button view-button">View</a>
                                    <a href="edit.php?id=<?= $review->id ?>" class="action-button edit-button">Edit</a>
                                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] == 'Super Admin' || $_SESSION['role'] == 'Admin')): ?>
                                        <a href="destroy.php?id=<?= $review->id ?>" class="action-button deactivate-button">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
        document.addEventListener('DOMContentLoaded', function() {
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

