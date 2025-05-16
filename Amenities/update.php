<?php 
session_start();
require_once '../Database/database.php';
require_once '../models/Amenity.php';
include '../layout/header.php';
include '../auth/super.php';

$database = new database();
$conn = $database->getConnection();

Amenity::setConnection($conn);

$id = $_GET['id'];

$amenity = Amenity::find($id);

$amenity->name = $_GET['name'];
$amenity->price = $_GET['price'];
$amenity->description = $_GET['description'];
$amenity->status = $_GET['status'];

$amenity->save();

if ($amenity) {
    if ($amenity) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Amenity record has been updated.",
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
                    text: "Failed to update Amenity record.",
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