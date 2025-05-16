<?php session_start(); ?>
<?php include '../layout/header.php'; ?>
<?php require_once '../database.php'; 
      require_once '../models/Room.php';
    $database = new database();
    $conn = $database->getConnection();
?>



<?php

if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Super Admin' && $_SESSION['role'] != 'Admin')){
    echo '<script>
        Swal.fire({
            title: "Unauthorized!",
            text: "You do not have permission to delete this Room.",
            icon: "error",
            confirmButtonText: "Ok"
        }).then(() => {
            window.location = "index.php";
        });
    </script>';
    exit();
}

Room::setConnection($conn);
// If deletion is confirmed via POST
if(isset($_POST['confirm_delete'])) {
    $id = $_GET['id'];
    $book = Room::find($id);
    $book->delete();
    
    if($book){
        if($book){
            echo '<script>
                Swal.fire({
                    title: "Deleted!",
                    text: "The Room has been deleted.",
                    icon: "success"
                }).then(() => {
                    window.location = "index.php";
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to delete room record, please try again!",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(() => {
                    window.location = "index.php";
                });
            </script>';
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    // Show confirmation dialog first
    echo '<script>
        Swal.fire({
            title: "Are you sure?",
            text: "You won\'t be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form to trigger deletion
                const form = document.createElement("form");
                form.method = "POST";
                form.action = window.location.href;
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "confirm_delete";
                input.value = "1";
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location = "index.php";
            }
        });
    </script>';
}

?>

<?php include '../layout/footer.php'; ?>