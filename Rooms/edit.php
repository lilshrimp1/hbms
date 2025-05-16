<?php 
      session_start();
      require_once '../Database/database.php';
      require_once '../models/Room.php';

      $database = new database();
      $conn = $database->getConnection();


      Room::setConnection($conn);
      $id = $_GET['id'];
      
      $rooms = Room::find($id);
?>
<?php include '../layout/header.php'; ?>

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

    <div class="data-table-container">
    <form action="update.php" method="GET">
    <div class="card shadow mb-3">
            <div class="card-header bg-danger-subtle text-white">
                    <h2 class="text-left px-3">EDIT RECORD</h2>
            </div>
    <div class="card-body" style="font-family: Arial, Helvetica, sans-serif;">
                <input type="hidden" name="id" value="<?=$rooms->id?>"> 
                <div class="row gx-3">
                    <div class="col-md-3 mb-3">
                        <label for="room_number" class="form-label">Room Number</label>
                        <input type="text" name="room_number" id="room_number" class="form-control" value="<?=$rooms->room_number?>" required>
                    </div>
                    <div class="col-md-5 mb-3"> 
                        <label for="description" class="form-label">Room Description</label>
                        <input type="text" name="description" id="description" class="form-control" value="<?=$rooms->description?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="text" name="capacity" id="capacity" class="form-control" value="<?=$rooms->capacity?>" required>
                    </div>
                </div>
                <div class="row gx-3">
                    <div class="col-md-3 mb-3"> 
                        <label for="status" class="form-label">Status</label>
                        <input type="text" name="status" id="status" class="form-control" value="<?=$rooms->status?>" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="type_id" class="form-label">Room Type</label>
                        <select name="type_id" id="type_id" class="form-select" required>
                            <?php
                            // Fetch room types from the database
                            $sql = "SELECT id, name FROM room_types";
                            $stmt = $conn->query($sql);
                            $roomTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            
                            foreach ($roomTypes as $type) {
                                echo "<option value='{$type['id']}'>{$type['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" step=".01" id="price" class="form-control" value="<?=$rooms->price?>" required>
                    </div>
                </div>
                <div class="row gx-3">
                    <div class="col-12">
                        <a href="index.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-success">Sumbit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
</html>