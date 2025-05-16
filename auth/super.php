<?php
require_once '../Database/database.php';

$database = new database();
$conn = $database->getConnection();


if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Super Admin' && $_SESSION['role'] != 'Admin') {
    echo '<script>
        Swal.fire({
            title: "Unauthorized!",
            text: "You do not have permission to access this page.",
            icon: "error",
            confirmButtonText: "Ok"
        }).then(() => {
            window.location = "../main/index.php";
        });
    </script>';
    exit();
}

?>