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

$user->name = $_GET['name'];
$user->email = $_GET['email'];
$user->role = $_GET['role'];
$user->status = $_GET['status'];
$user->update($_GET['update']);

$user->save();

if ($user) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "User record has been updated.",
                    icon: "success"
                }).then(function() {
                    window.location = "index.php";
                });
            });
        </script>';
} else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
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