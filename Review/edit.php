<?php
require_once '../Database/database.php';
require_once '../models/Review.php';
require_once '../models/Reservation.php';
require_once '../models/User.php';
session_start();
include '../layout/header.php';
include '../auth/super.php';

$database = new Database();
$conn = $database->getConnection();

Review::setConnection($conn);
Reservation::setConnection($conn);
User::setConnection($conn);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Review ID is required.</div>";
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $review = $stmt->fetch();

    if (!$review) {
        echo "<div class='alert alert-danger'>Review not found.</div>";
        exit;
    }

    $reservation = Reservation::find($review['reservation_id']);
    $user = $reservation ? User::find($reservation->user_id) : null;

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Review Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../images/bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
        }
        .data-table-container {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem; 
            margin-top: 100px; 
            margin-left: auto;
            margin-right: auto; 
            max-width: 800px;
        }
    </style>
</head>
<body>

<div class="container data-table-container">
    <h3>Edit Review Status</h3>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($review['id']) ?>">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
                <option value="Visible" <?= $review['status'] == 'Visible' ? 'selected' : '' ?>>Visible</option>
                <option value="Hidden" <?= $review['status'] == 'Hidden' ? 'selected' : '' ?>>Hidden</option>
                <option value="Deleted" <?= $review['status'] == 'Deleted' ? 'selected' : '' ?>>Deleted</option>
            </select>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </div>

        
    </form>
</div>

</body>
</html>
