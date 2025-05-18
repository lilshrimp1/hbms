<?php
require_once '../Database/database.php';
require_once '../models/Review.php';

session_start();

$database = new Database();
$conn = $database->getConnection();
Review::setConnection($conn);

// Default values
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $review = Review::find($id);

        if ($review) {
            $review->status = $status;
            $review->updated_at = date('Y-m-d H:i:s');
            $review->save();
            $message = 'Review status updated successfully!';
            $success = true;
        } else {
            $message = 'Review not found.';
        }
    } else {
        $message = 'Missing review ID or status.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Review Status</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if ($message): ?>
        Swal.fire({
            icon: '<?= $success ? 'success' : 'error' ?>',
            title: '<?= $success ? 'Success' : 'Error' ?>',
            text: '<?= $message ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'index.php';
        });
        <?php endif; ?>
    });
</script>
</body>
</html>
