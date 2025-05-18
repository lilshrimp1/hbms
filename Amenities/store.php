<?php
session_start();
include '../layout/header.php';
include '../auth/super.php';
require_once '../Database/database.php';
require_once '../models/Amenity.php';

$database = new database();
$conn = $database->getConnection();

Amenity::setConnection($conn);

// First check if name exists
$name = $_POST['name'];

$check_stmt = Amenity::findByColumn('name', $name);

if ($check_stmt) {
    if ($check_stmt) {
        // Amenity already exists
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "This Amenity already exists. Please use a different Amenity.",
                        icon: "error",
                        confirmButtonText: "Ok"
                    }).then(function() {
                        window.location = "create.php";
                    });
                });
            </script>';
        exit();
    }
}


// Prepare user data as an associative array
$user_data = [
    'name' => $_POST['name'],
    'price' => $_POST['price'],
    'description' => $_POST['description'],
    'status' => $_POST['status'],
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

// Call the create method
$result = Amenity::create( $user_data);

if ($result) {
    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Amenity record has been created.",
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
                text: "Failed to save Amenity record, please try again!",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
}

?>