<?php session_start();?>
<?php include '../layout/header.php'; ?>
<?php include '../auth/super.php'; ?>
<?php require_once '../Database/database.php'; 
      require_once '../models/Room.php';
        $database = new database();
        $conn = $database->getConnection();
?>

<?php
Room::setConnection($conn);
// If deletion is confirmed via POST
if (isset($_POST['confirm_delete'])) {
    $id = $_GET['id'];
    $room = Room::find($id);
    $room->status = 'Active';

    if ($room->save()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Activated!",
                    text: "The User has been activated.",
                    icon: "success"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to Activate User, please try again!",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    }
} else {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Are you sure?",
                text: "You won\'t be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, activate it!"
            }).then((result) => {
                if (result.isConfirmed) {
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
        });
    </script>';
}
?>

<?php include '../layout/footer.php'; ?>