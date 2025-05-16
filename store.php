<?php
session_start();
include '../layout/header.php';
require_once '../Database/database.php';
require_once '../models/Review.php';

$database = new database();
$conn = $database->getConnection();

Review::setConnection($conn);

// Check if reservation exists and belongs to current guest
$id = $_POST['id'];

$check_stmt = Review::find($id);

if ($check_stmt) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "You have already submitted a review for this reservation.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
    exit();
}

// Validate rating
$rating = (int)$_POST['rating'];
if ($rating < 1 || $rating > 5) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Rating must be between 1 and 5 stars.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
    exit();
}

// Prepare review data
$review_data = [
    'reservation_id' => $reservation_id,
    'rating' => $rating,
    'comment' => htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8'),
    'date' => date('Y-m-d H:i:s')
];

// Create the review
$result = Review::create($review_data);

if ($result) {
    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Your review has been submitted successfully.",
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
                text: "Failed to submit review, please try again!",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
}
?>