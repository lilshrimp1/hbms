<?php 
require_once '../Database/database.php'; 
require_once '../models/Review.php';
session_start();
require_once '../models/User.php';
require_once '../models/Reservation.php';
include '../layout/header.php';
include '../auth/super.php';
include '../layout/sidebar.php';

$database = new database();
$conn = $database->getConnection();

Review::setConnection($conn);
Reservation::setConnection($conn);
User::setConnection($conn);


// Filter handling
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Only one filter at a time
if ($rating_filter) {
    $reviews = Review::where('rating', '=', $rating_filter);
} elseif ($date_filter) {
    $reviews = Review::where('created_at', 'LIKE', $date_filter . '%');
} elseif ($status_filter) {
    $reviews = Review::where('status', '=', $status_filter);
} else {
    $reviews = Review::all();
}

// Ensure $reviews is always an array
if (!$reviews) {
    $reviews = [];
}
?>

<div class="container-xxl mb-5 mt-5">
    <div class="card shadow">
        <div class="card-header bg-danger-subtle text-white d-flex justify-content-between">
            <h2 class="text-left px-3">REVIEWS</h2>
            <a href="create.php" class="btn btn-light btn-lg">Create</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="reviewsTable" class="table table-striped table-hovered table-bordered mt-3 mb-3">
                    <thead class="table-danger">
                        <tr>
                            <th>#</th>
                            <th>Reviewer Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reviews as $review): 
                            $reservation = Reservation::find($review->reservation_id);
                            $userResult = User::where('id', '=', $reservation->user_id);
                            $user = is_array($userResult) && !empty($userResult) ? $userResult[0] : null;
                            $room = Room::find($reservation->room_id);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($review->id) ?></td>
                                <td><?= htmlspecialchars($user->name) ?></td>
                                <td><?= htmlspecialchars($review->rating) ?></td>
                                <td><?= htmlspecialchars($review->comment) ?></td>
                                <td><?= htmlspecialchars($review->status) ?></td>
                                <td><?= htmlspecialchars($review->created_at) ?></td>
                                <td>
                                    <a href="show.php?id=<?= $review->id ?>" class="btn btn-outline-success btn-sm px-2 mx-2"><i class="fa-regular fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $review->id ?>" class="btn btn-outline-success btn-sm px-2 mx-2"><i class="fa-regular fa-edit"></i></a>
                                    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] == 'Super Admin' || $_SESSION['role'] == 'Admin')): ?>
                                        <a href="destroy.php?id=<?= $review->id ?>" class="btn btn-outline-success btn-sm px-2 mx-2"><i class="fa-solid fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>