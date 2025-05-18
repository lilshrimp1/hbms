<?php session_start();?>
<?php include '../layout/header.php'; ?>
<?php include '../auth/super.php'; ?>
<?php require_once '../Database/database.php'; 
      require_once '../models/User.php';
        $database = new database();
        $conn = $database->getConnection();
?>

<?php
User::setConnection($conn);
// If deletion is confirmed via POST
if (isset($_POST['confirm_delete'])) {
    $id = $_GET['id'];
    $user = User::find($id);
    $user->status = 'Inactive';

    if ($user->save()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Deactivated!",
                    text: "The User has been deactivated.",
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
                    text: "Failed to Deactivate User, please try again!",
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
                confirmButtonText: "Yes, deactivate it!"
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