<?php include '../layout/header.php'; ?>
<?php require_once '../Database/database.php';       
      require_once '../models/Room.php';
    $database = new database();
    $conn = $database->getConnection();
?>

<?php
    Room::setConnection($conn);
    $room_number = $_POST['room_number'] ?? null;
    $check_stmt = Room::findByColumn('room_number', $room_number);
    
    if($check_stmt){   
        if($check_stmt){
            // Room already exists
            echo '<script>
                    Swal.fire({
                        title: "Error!",
                        text: "This Room Number already exists. Please use a different Room Number.",
                        icon: "error",
                        confirmButtonText: "Ok"
                    }).then(function() {
                        window.location = "create.php";
                    });
                </script>';
            exit();
        }
    }
    $data = [
        'room_number' => $_POST['room_number'],
        'type_id' => $_POST['type_id'],
        'price' => $_POST['price'],
        'status' => $_POST['status'],
        'description' => $_POST['description'],
        'capacity' => $_POST['capacity'],
        'created_at' => date('Y-m-d H:i:s'),
    ];

   $stmt = Room::create($data);

    if($stmt){
        if($stmt){
            echo '<script>
                    Swal.fire({
                        title: "Success!",
                        text: "Room has been created.",
                        icon: "success",
                        confirmButtonText: "Ok"
                    }).then(function() {
                        window.location = "index.php";
                    });
                </script>';
        }
        else{
            echo '<script>
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to save Room, please try again!",
                        icon: "error",
                        confirmButtonText: "Ok"
                    }).then(function() {
                        window.location = "create.php";
                    });
                </script>';
        }
    }
?>

<?php include '../layout/footer.php'; ?>