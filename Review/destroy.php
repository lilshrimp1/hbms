<?php session_start(); ?>
<?php include '../layout/header.php'; ?>
<?php require_once '../Database/database.php'; ?>
<?php require_once '../models/Review.php'; ?>

<?php
$database = new Database();
$conn = $database->getConnection();

if (isset($_SESSION['role']) && ($_SESSION['role'] != 'Super Admin' && $_SESSION['role'] != 'Admin')) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Unauthorized!",
                text: "You do not have permission to delete this review.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(() => {
                window.location = "index.php";
            });
        });
    </script>';
    exit();
}

Review::setConnection($conn);

if (isset($_POST['confirm_delete'])) {
    $id = $_GET['id'];
    $review = Review::find($id);

    if ($review && $review->delete()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "Review has been deleted.",
                    icon: "success"
                }).then(() => {
                    window.location = "index.php";
                });
        </script>';
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to delete review, please try again!",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(() => {
                    window.location = "index.php";
                });
        </script>';
    }
} else {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Delete Review",
                text: "Are you sure you want to delete this review?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
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

