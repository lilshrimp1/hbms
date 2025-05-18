<?php 
session_start();
require_once '../Database/database.php';
require_once '../models/User.php';
include '../layout/header.php';

$database = new database();
$conn = $database->getConnection();

User::setConnection($conn);

$id = $_GET['id'];

$user = User::find($id);

$user->name = $_GET['name'];
$user->contact_no = $_GET['contact_no'];
$user->address = $_GET['address'];

$user->save();

if ($user) {
    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "User record has been updated.",
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
                    text: "Failed to update User record.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location = "edit.php?id=' . $id . '";
                });
            </script>';
    }




?>

<?php include '../layout/footer.php';?>