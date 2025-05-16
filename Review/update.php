<?php 
session_start();
include '../layout/header.php';
include '../auth/super.php';
require_once '../database.php';
require_once '../models/Review.php';

$database = new database();
$conn = $database->getConnection();

Review::setConnection($conn);

$status = $_GET['status'];

$amenity = Review::find($status);

$amenity->status = $_GET['status'];

$amenity->save();

if ($amenity) {
    if ($amenity) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Status has been updated.",
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
                    text: "Failed to update Status.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location = "edit.php?id=' . $id . '";
                });
            </script>';
    }
}



?>

<?php include '../layout/footer.php';?>