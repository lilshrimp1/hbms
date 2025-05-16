<?php 
    session_start();
    require_once '../database.php';
    require_once '../models/Room.php';
    require_once '../models/RoomType.php';

    $database = new database();
    $conn = $database->getConnection();

    Room::setConnection($conn);
    $id = $_GET['id'];
    $room = Room::find($id);

    $roomType = RoomType::find($room->type_id);

     Amenity::setConnection($conn);

    $amenities = Amenity::find($id);

    if (!$room) {
        show404Error();
        exit();
    }

// Function to display 404 error
function show404Error() {
    echo '<div class="container-xxl mt-5">
            <div class="card shadow">
                <div class="card-header bg-danger-subtle">
                    <h2 class="text-left px-3">404 - room Not Found</h2>
                </div>
                <div class="card-body">
                    <p>The requested room could not be found.</p>
                    <a href="manage_rooms.php" class="btn btn-outline-success">Return to room List</a>
                </div>
            </div>
          </div>';
    include '../layout/footer.php';
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
            margin-top: 60px; 
            margin-left: 300px; 
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

        .card-header.bg-danger-subtle {
            background-color:#1bb3bd !important; 
            color: white !important; 
    }
        .card-body.small-text {
        font-size: 15px; 
    }

    </style>
</head>
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
                        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Librarian' && $_SESSION['role'] != 'Admin')){ ?>
                        <li><a href="../Amenities/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Amenities</span></a>
                        </li>
                        <li><a href="../main/pdf.php">
                                <span class="icon"><i class="fa fa-file-pdf"></i></span>
                                <span class="text">PDF</span></a>
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

<?php if(isset($room)): ?>
    <div class="data-table-container">
    <div class="card shadow  mx-auto" style="max-height: 700px;">
        <div class="card-header bg-danger-subtle">
            <h2 class="text-left text-white px-3">Room Details</h2>
        </div>

        <div class="card-body p-3 small-text">
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Room Number:</strong>
                <span style="color: #555;"><?= $room->room_number ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Room Type:</strong>
                <span style="color: #555;"><?= $roomType->name; ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Description:</strong>
                <span style="color: #555;"><?= $room->description ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Capacity:</strong>
                <span style="color: #555;"><?= $room->capacity ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Inclusions:</strong>
                <span style="color: #555;"><?= $amenities->name ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Price:</strong>
                <span style="color: #555;">₱ <?= number_format($room->price, 2) ?></span>
            </div>
            <div class="mb-2">
                <strong class="d-block mb-1" style="font-weight: bold; color: #333;">Status:</strong>
                <span style="color: #555;"><?= $room->status ?></span>
            </div>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary">Back to Room List</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
</html>