<?php 
session_start();
require_once '../Database/database.php';
require_once '../models/User.php';
require_once '../auth/super.php';
include '../layout/header.php';

$database = new database();
$conn = $database->getConnection();

User::setConnection($conn);

$id = $_GET['id'];

$user = User::find($id);
$hashedPassword = password_hash($_GET['password'], PASSWORD_DEFAULT);
$user->password = $hashedPassword;


$user->save();

if ($user) {
    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Password has been updated.",
                icon: "success",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
} else {
        echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Failed to update Password.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location = "edit.php?id=' . $id . '";
                });
            </script>';
    }




?>

<?php include '../layout/footer.php';?>