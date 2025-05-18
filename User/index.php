    <?php require_once '../Database/database.php';
    require_once '../models/User.php';
    $database = new database();
    $conn = $database->getConnection();
    ?>
    <?php session_start();?>
    <?php include '../layout/header.php'; ?>
    <?php include '../auth/super.php'; ?>

    <?php
    User::setConnection($conn);
    $users = User::all();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
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
            padding: 0.50rem; 
            margin-top: 100px; 
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
            font-size: 0.8rem;
            line-height: 1.25rem;
            font-weight: 200;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            text-decoration: none !important;
            }

            .edit-button {
            background-color: #fef08a;
            color: #1e293b;
            }
            .edit-button:hover{
            background-color: #fde047;
            }

            .deactivate-button {
            background-color: #fecaca;
            color: #7f1d1d;
            }

            .deactivate-button:hover {
            background-color: #fca5a5;
            }

            .status-active {
            background-color: #ecfdf5;
            color: #064e3b;
            border-radius: 1rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            }

        .reset-password-button {
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            line-height: 1.25rem;
            font-weight: 200;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            text-decoration: none !important;
            border: none;
            transition: background-color 0.2s ease;
        }

        .reset-password-button:hover {
            background-color: #b5f3d9;
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
            margin-top: 8px;
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
        font-size: 15px;
        color: white;
        display: block;
        transition: 0.3s;
        display: flex;
        align-items: center;
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
    }

        .scroll-container {
        height: 700px; /* or kung gaano kataas gusto mo */
        overflow-y: scroll; /* vertical scrollbar */
        overflow-x: hidden; /* para walang horizontal scrollbar */
        /* optional: to prevent shifting */
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #888 #eee;
        }

    </style>
    </head>
    <body class="bg" style="background-image:url(../image/bg.png); position:fixed; ">
    <div class ="scroll-container">
    <div class="flex">
    <aside id="navbar" class=" text-blue-800 w-64" style="font-size: 20px; background-color: rgba(75, 216, 226, 0.75);">

    <nav class="space-y-2 mt-16 mb-10 p-4" style="color:white; ">
    <div class="logo text-xxl font-semibold text-white-800 flex items-center mb-5 ml-2 mt-4" style="font-size:40px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="white" class="bi bi-house-fill" viewBox="0 0 16 16">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
    </svg>
    HBMS
    </div>
    <div class="sidebar" id="sidebar">
                <ul>
                <a href="../Main/index.php">Dashboard</a>
                <a href="../Rooms/index.php">Room Management</a>
                <a href="../Amenities/index.php">Amenities</a>
                <a href="../Reservation/index.php">Reservations</a>
                <a href="../Reservation/checkin_in_out.php">Check-in/Check-out</a>
                <a href="../Review/index.php">Feedback</a>
                <a href="index.php">Manage Users</a>
                <a href="../auth/logout.php">Log Out</a>
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
            <div class="text-gray-600 mr-4">Super</div> <!--depends sa role-->
            <img src="https://placehold.co/40x40/80ED99/fff?text=U&font=Montserrat" alt="User Avatar" class="rounded-full">
            </div>
    </header>

            <div class="flex justify-center mt-[150px]">
            <div class="data-table-container" style="font-size:20px;">

            <div style="margin-top:-130px;">

            <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-4">
            <select class="border rounded p-2 text-white-700 focus:outline-none" 
                    style="border-radius: 1rem; background-color:#1BB3BD; color:white;">
                <option disabled selected>Roles</option>
                <option value="Super Admin">Super Admin</option>
                <option value="Admin">Admin</option>
                <option value="Front Desk">Front Desk</option>
            </select>

            <select class="border rounded p-2 text-white-700 focus:outline-none" 
                    style="border-radius: 1rem; background-color:#1BB3BD; color:white;">
                <option disabled selected>Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            
            </div>
            
            <div class="d-flex justify-content-end mb-3 gap-2">
            <div class="flex items-center space-x-4">
                            <div style="margin-top:-100px;">
                            <a href="create.php" class="btn text-white fw-bold" 
                            style="border-radius: 1rem; background-color:#1BB3BD; text-decoration: none;">
                                Add Users
                            </a>
            </div>
            </div>
            </div>
    </div>
</div>
            <div class="overflow-x-auto">
            <table class="min-w-full">
            <thead class="bg-white-100" style="text-align: center;">
            <tr>
            <th class="px-4 py-2 text-left text-gray-600 font-semibold" style="text-align: center;">Name</th>
            <th class="px-4 py-2 text-left text-gray-600 font-semibold" style="text-align: center;">Email</th>
            <th class="px-4 py-2 text-left text-gray-600 font-semibold" style="text-align: center;">Role</th>
            <th class="px-4 py-2 text-center text-gray-600 font-semibold">Status</th>
            <th class="px-4 py-2 text-center text-gray-600 font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            <?php
            $i = 1;
            foreach($users as $user):
            ?>
            <tr>
            <td class="px-4 py-2 text-gray-800" style="text-align: center;"><?php echo $user->name; ?></td>
            <td class="px-4 py-2 text-gray-800" style="text-align: center;"><?php echo $user->email; ?></td>
            <td class="px-4 py-2 text-gray-800" style="text-align: center;"><?php echo $user->role; ?></td>
            <td class="px-4 py-2 text-center">
            <span class="status-active"><?php echo $user->status; ?></span>
            </td>
            <td class="px-4 py-2 space-x-2 text-center">
            <a href="edit.php?id=<?php echo $user->id; ?>" class="action-button edit-button">Edit</a>
            <?php if($user->status == 'Active'): ?>
            <a href="deactivate.php?id=<?php echo $user->id; ?>" class="action-button deactivate-button">Deactivate</a>
            <?php else: ?>
            <a href="activate.php?id=<?php echo $user->id; ?>" class="action-button deactivate-button">Activate</a>
            <?php endif; ?>
            <button onclick="resetPassword(<?= $user->id ?>)" class="reset-password-button">Reset Password</button>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function resetPassword(userId) {
    Swal.fire({
        title: 'Enter new password',
        input: 'password',
        inputLabel: 'New Password',
        inputPlaceholder: 'Enter the new password',
        showCancelButton: true,
        confirmButtonText: 'Reset Password',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Password cannot be empty');
                return;
            }
            return fetch(`password_reset.php?id=${userId}&password=${encodeURIComponent(password)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success') {
                        throw new Error(data.message);
                    }
                    return data;
                })
                .catch(err => Swal.showValidationMessage(`Request failed: ${err}`));
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Success!', 'Password updated.', 'success').then(() => {
                location.reload(); // or window.location = 'index.php';
            });
        }
    });
}
</script>
            </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
            </div>
            </div>

            </main>
            </div>

            <script>
                const menuContainer = document.querySelector('.menu-container');
                const navbar = document.querySelector('#navbar');
                const mainContent = document.querySelector('main');
                let isNavbarVisible = false;

                menuContainer.addEventListener('mouseenter', () => {
                navbar.classList.add('show');
                mainContent.classList.add('shifted');
                isNavbarVisible = true;
                });

                navbar.addEventListener('mouseleave', () => {
                navbar.classList.remove('show');
                mainContent.classList.remove('shifted');
                isNavbarVisible = false;
                });

                navbar.addEventListener('mouseenter', () => {
                isNavbarVisible = true;
                })
                </script>
                </body>
    </html>
