<?php 
session_start();
require_once '../Database/database.php';
require_once '../models/User.php';
include '../layout/header.php';

$database = new database();
$conn = $database->getConnection();

User::setConnection($conn);

// Check if POST data is set
$user_id = $_POST['user_id'] ?? $_POST['id'] ?? null;
if (!$user_id) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "User ID is missing.",
                icon: "error"
            }).then(function() {
                window.location = "index.php";
            });
        });
    </script>';
    include '../layout/footer.php';
    exit;
}

$user = User::find($user_id);

if (!$user) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "User not found.",
                icon: "error"
            }).then(function() {
                window.location = "index.php";
            });
        });
    </script>';
    include '../layout/footer.php';
    exit;
}

$user->name = $_POST['name'];
$user->contact_no = $_POST['contact'];
$user->address = $_POST['address'];

if ($user->save()) {
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
        });
    </script>';
}

include '../layout/footer.php';
?>