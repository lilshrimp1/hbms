<?php   
      session_start();
      include '../auth/super.php';
      include '../layout/header.php';  
      require_once '../Database/database.php';
     
?>
<style>
        .card-header {
            border-radius: 10px 10px 0 0; 
        }

        .card {
            margin-top: 20px;
            border: none; 
            border-radius: 15px; 
            overflow: hidden; 
        }

        .card-body {
            border-radius: 0 0 15px 15px; 
            padding: 20px;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .card-body.small-text {
        font-size: 15px; 
        }

        .btn-secondary {
            border-radius: 8px;
        }

        .btn-success {
            border-radius: 8px;
        }

        .data-table-container {
            background-color: white;
            border-radius: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem;
            margin-top: 0 !important;
            width: 100%;
            max-width: 800px;
            margin-left: 400px;
            
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
                        <li><a href="../Reservation/index.php">
                                <span class="icon"><i class="fa fa-user"></i></span>
                                <span class="text">Reservation</span></a>
                        </li>
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


            <body>
    <div class="data-table-container">
            <form action="store.php" method="POST">
                <div class="card">
                    <div class="card-header bg-danger-subtle text-white">
                        <h2 class="text-left px-3">ADD AMENITY</h2>
                    </div>
                    <div class="card-body">
                        <div class="row gx-3">
                            <div class="col-md-11.5 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row gx-3">
                            <div class="col-md-11.5 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" name="description" id="description" class="form-control" required>
                            </div>
                        </div>
                        <div class="row gx-3">
                            <div class="col-md-11.5 mb-3">
                                <label for="price" class="form-label">Price (24 Hours)</label>
                                <input type="text" name="price" id="price" class="form-control" required>
                            </div>
                            <div class="row gx-3">
                                <label for="status" class="form-label">Status</label>
                                <select type="text" name="status" id="status" class="form-select" required>
                                    <option value="" selected hidden>(Active, Inactive)</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row gx-3 mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="index.php" class="btn btn-secondary me-2">Back</a>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
