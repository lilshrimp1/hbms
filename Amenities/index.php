<?php require_once '../database.php'; 
      require_once '../models/Amenity.php';
    $database = new database();
    $conn = $database->getConnection();
?>
<?php session_start();?>
<?php include '../layout/header.php'; ?>
<?php include '../auth/super.php'; ?>

<?php
Amenity::setConnection($conn);
$amenities = Amenity::all();

?>
<style> 
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

        .deactivate-button:hover {
             background-color: #fca5a5;
        }
        
        .card-body.small-text {
        font-size: 15px; 
        }
        
        .data-table-container {
            background-color: white;
            border-radius: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-top: 120px !important;
            width: 100%;
            max-width: 1000px;
            margin-left: 250px;
            
        }
</style>
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

                <div class="data-table-container" style="margin-top:180px; position:relative; font-size:15px;">
                <div style="margin-top:-100px;">
                    <div class="flex items-right mb-6 justify-end" >
                        <a href="create.php" class="hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" style="border-radius: 1rem; 
                        background-color:#1BB3BD; text-decoration: none;">Add Amenity</a>
                    </div>


        <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white-100">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach($amenities as $amenity):
                        ?>
                        <tr style="text-align: center;">
                            <td><?= $i++ ?></td>    
                            <td><?= $amenity->name ?></td>
                            <td><?= $amenity->price ?></td>
                            <td><?= $amenity->description ?></td>
                            <td><?= $amenity->status ?></td>
                            <td>    
                                <a href="edit.php?id=<?= $amenity->id ?>" class="action-button edit-button"">Edit</a>
                                <a href="destroy.php?id=<?= $amenity->id ?>" class="action-button deactivate-button"">Delete</a>
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
