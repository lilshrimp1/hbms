<?php require_once '../Database/database.php'; 
    $database = new database();
    $conn = $database->getConnection();
    require_once '../models/Room.php';
    Room::setConnection($conn);
    require_once '../models/Review.php';
    Review::setConnection($conn);
    require_once '../models/Reservation.php';
    Reservation::setConnection($conn);
?>
<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get user role from session
$userRole = $_SESSION['role'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

// Common queries for all dashboards
$today = date('Y-m-d');

// Room availability stats
$available_rooms = Room::countByStatus('available');
$booked_rooms = Room::countByStatus('booked');
$maintenance_rooms = Room::countByStatus('maintenance');
$not_available_rooms = Room::countByStatus('not available');

// Upcoming reservations
$upcoming_reservations = Reservation::countByStatusAndDate('confirmed', 'check_in', '>=', $today);

// Today's arrivals and departures (for Front Desk)
$todays_arrivals = Reservation::countByStatusAndDate('confirmed', 'check_in', '=', $today);
$todays_departures = Reservation::countByStatusAndDate('checked-in', 'check_out', '=', $today);

// Latest reviews
$sql_reviews = "SELECT r.id, r.rating, r.comment, r.created_at, u.name as guest_name
                FROM reviews r
                JOIN reservations res ON r.reservation_id = res.id
                JOIN users u ON res.user_id = u.id
                WHERE r.status = 'approved'
                ORDER BY r.created_at DESC
                LIMIT 5";
$stmt_reviews = $conn->query($sql_reviews);
$latest_reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

// Count all approved reviews
$total_visible_reviews = Review::countByStatus('approved');

// Count approved reviews by star
$five_star_reviews = Review::countByStatusAndRating('approved', 5);
$four_star_reviews = Review::countByStatusAndRating('approved', 4);
$three_star_reviews = Review::countByStatusAndRating('approved', 3);
$two_star_reviews = Review::countByStatusAndRating('approved', 2);
$one_star_reviews = Review::countByStatusAndRating('approved', 1);

// Additional Super Admin and Admin specific queries
if ($userRole == 'Super Admin' || $userRole == 'Admin') {
    // Get total users by role
    $sql_users_by_role = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $stmt_users_by_role = $conn->query($sql_users_by_role);
    $users_by_role = $stmt_users_by_role->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert to associative array for easier access
    $userCounts = [];
    foreach ($users_by_role as $count) {
        $userCounts[$count['role']] = $count['count'];
    }
    
    // Total users
    $sql_total_users = "SELECT COUNT(*) as total_users FROM users";
    $stmt_total_users = $conn->query($sql_total_users);
    $total_users = $stmt_total_users->fetch(PDO::FETCH_ASSOC)['total_users'];
}

// Define room status variables
$occupied_rooms = $booked_rooms; // Assuming booked means occupied
$out_of_order_rooms = $maintenance_rooms;
$vacant_ready_rooms = $available_rooms;
$vacant_not_ready_rooms = $not_available_rooms;

// Guest specific queries
if ($userRole == 'Guest') {
    // Reservation history for current guest
    $sql_history = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, 
                    rt.name as room_type 
                    FROM reservations r 
                    JOIN room rm ON r.room_id = rm.id
                    JOIN room_types rt ON rm.type_id = rt.id
                    WHERE r.user_id = :user_id
                    ORDER BY r.check_in DESC";
    $stmt_history = $conn->prepare($sql_history);
    $stmt_history->bindValue(':user_id', $userId);
    $stmt_history->execute();
    $reservation_history = $stmt_history->fetchAll(PDO::FETCH_ASSOC);
    
    // Upcoming bookings for current guest
    $sql_upcoming_guest = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, 
                         rt.name as room_type 
                         FROM reservations r 
                         JOIN room rm ON r.room_id = rm.id
                         JOIN room_types rt ON rm.type_id = rt.id
                         WHERE r.user_id = :user_id AND r.status IN ('pending', 'confirmed')
                         AND r.check_in >= :today
                         ORDER BY r.check_in ASC";
    $stmt_upcoming_guest = $conn->prepare($sql_upcoming_guest);
    $stmt_upcoming_guest->bindValue(':user_id', $userId);
    $stmt_upcoming_guest->bindValue(':today', $today);
    $stmt_upcoming_guest->execute();
    $upcoming_guest_bookings = $stmt_upcoming_guest->fetchAll(PDO::FETCH_ASSOC);
}

// For admin/staff, get upcoming bookings for display
if ($userRole != 'Guest') {
    $sql_upcoming_bookings = "SELECT r.id, r.check_in, r.check_out, r.status, rm.room_number, 
                           rt.name as room_type, u.name as guest_name
                           FROM reservations r 
                           JOIN room rm ON r.room_id = rm.id
                           JOIN room_types rt ON rm.type_id = rt.id
                           JOIN users u ON r.user_id = u.id
                           WHERE r.status IN ('pending', 'confirmed')
                           AND r.check_in >= :today
                           ORDER BY r.check_in ASC
                           LIMIT 5";
    $stmt_upcoming_bookings = $conn->prepare($sql_upcoming_bookings);
    $stmt_upcoming_bookings->bindValue(':today', $today);
    $stmt_upcoming_bookings->execute();
    $upcoming_bookings = $stmt_upcoming_bookings->fetchAll(PDO::FETCH_ASSOC);
}


?>
<?php include '../layout/header.php'; 
      include '../layout/sidebar.php';
?>
<style>
    .custom-color {
        color: #9ACBD0 !important;
    }
</style>

<link href="assets/tagabenta/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<div class="container-xxl mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <?php if ($userRole == 'Super Admin'): ?>
                <!-- Super Admin Dashboard -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4 border-0">
                            <div class="card-body">
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Total Users</label>
                                                <h3 class="custom-color"><?= $total_users ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Admins</label>
                                                <h3 class="custom-color"><?= $userCounts['admin'] ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Front Desk</label>
                                                <h3 class="custom-color"><?= $userCounts['front-desk'] ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Guests</label>
                                                <h3 class="custom-color"><?= $userCounts['guest'] ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../lobby1.jpg); background-size: cover; background-position: center;">
                            <div class="card-body" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center text-white">Upcoming Reservations</h5>
                                <ul class="list-group">
                                    <?php if (!empty($upcoming_guest_bookings)): ?>
                                        <?php foreach ($upcoming_guest_bookings as $booking): ?>
                                            <li class="list-group-item border-0 text-white" style="background-color: transparent;">
                                                <strong>Room:</strong> <?= htmlspecialchars($booking['room_number']) ?><br>
                                                <strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']) ?><br>
                                                <strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']) ?><br>
                                                <strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?><br>
                                                <strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item border-0 text-white" style="background-color: transparent;">No upcoming reservations.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <div class="col-md-6">
                    <div class="reviews-container">
                        <div class="reviews-header">
                            <h2 class="reviews-title">LATEST FEEDBACKS</h2>
                            <span class="reviews-count">Total Visible Reviews: <?= $total_visible_reviews ?></span>
                        </div>
                        <?php if (!empty($latest_reviews)): ?>
                            <?php foreach ($latest_reviews as $review): ?>
                                <div class="review-card">
                                    <div class="reviewer-name"><?= htmlspecialchars($review['guest_name']) ?></div>
                                    <div class="review-text"><?= htmlspecialchars(mb_strimwidth($review['comment'], 0, 100, '...')) ?></div>
                                    <div class="rating">
                                        <?php
                                        $rating = (int)$review['rating'];
                                        for ($i = 0; $i < $rating; $i++) {
                                            echo "★";
                                        }
                                        for ($i = $rating; $i < 5; $i++) {
                                            echo "☆";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="review-card">
                                <div class="review-text">No reviews available yet.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../bed2.jpg); background-size: cover; background-position: center;">
                            <div class="card-body text-center text-white" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center fw-bold mb-4">Room Status</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Occupied</label>
                                                <h3 class="custom-color"><?= $occupied_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Out Of Order</label>
                                                <h3 class="custom-color"><?= $out_of_order_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Not Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_not_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($userRole == 'Admin'): ?>
                <!-- Admin Dashboard -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4 border-0">
                            <div class="card-body">
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Total Users</label>
                                                <h3 class="custom-color"><?= $total_users ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Front Desk</label>
                                                <h3 class="custom-color"><?= $userCounts['front-desk'] ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-3 mb-4 justify-content-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Guests</label>
                                                <h3 class="custom-color"><?= $userCounts['guest'] ?? 0 ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../lobby1.jpg); background-size: cover; background-position: center;">
                            <div class="card-body" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center text-white">Upcoming Reservations</h5>
                                <ul class="list-group">
                                    <?php if (!empty($upcoming_guest_bookings)): ?>
                                        <?php foreach ($upcoming_guest_bookings as $booking): ?>
                                            <li class="list-group-item border-0 text-white" style="background-color: transparent;">
                                                <strong>Room:</strong> <?= htmlspecialchars($booking['room_number']) ?><br>
                                                <strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']) ?><br>
                                                <strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']) ?><br>
                                                <strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?><br>
                                                <strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item border-0 text-white" style="background-color: transparent;">No upcoming reservations.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <div class="col-md-6">
                    <div class="reviews-container">
                        <div class="reviews-header">
                            <h2 class="reviews-title">LATEST FEEDBACKS</h2>
                            <span class="reviews-count">Total Visible Reviews: <?= $total_visible_reviews ?></span>
                        </div>
                        <?php if (!empty($latest_reviews)): ?>
                            <?php foreach ($latest_reviews as $review): ?>
                                <div class="review-card">
                                    <div class="reviewer-name"><?= htmlspecialchars($review['guest_name']) ?></div>
                                    <div class="review-text"><?= htmlspecialchars(mb_strimwidth($review['comment'], 0, 100, '...')) ?></div>
                                    <div class="rating">
                                        <?php
                                        $rating = (int)$review['rating'];
                                        for ($i = 0; $i < $rating; $i++) {
                                            echo "★";
                                        }
                                        for ($i = $rating; $i < 5; $i++) {
                                            echo "☆";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="review-card">
                                <div class="review-text">No reviews available yet.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../bed2.jpg); background-size: cover; background-position: center;">
                            <div class="card-body text-center text-white" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center fw-bold mb-4">Room Status</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Occupied</label>
                                                <h3 class="custom-color"><?= $occupied_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Out Of Order</label>
                                                <h3 class="custom-color"><?= $out_of_order_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Not Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_not_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($userRole == 'Front Desk'): ?>
                <!-- Front Desk Dashboard -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4 shadow-lg" style="border-radius: 40px;">
                            <div class="card-body">
                                <h5 class="text-center">Today's Activity</h5>
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 bg-transparent">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Arrivals Today</label>
                                                <h3><?= $todays_arrivals ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 bg-transparent">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Departures Today</label>
                                                <h3><?= $todays_departures ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 bg-transparent">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold">Upcoming Reservations</label>
                                                <h3><?= $upcoming_reservations ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../lobby1.jpg); background-size: cover; background-position: center;">
                            <div class="card-body" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center text-white">Upcoming Reservations</h5>
                                <ul class="list-group">
                                    <?php if (!empty($upcoming_guest_bookings)): ?>
                                        <?php foreach ($upcoming_guest_bookings as $booking): ?>
                                            <li class="list-group-item border-0 text-white" style="background-color: transparent;">
                                                <strong>Room:</strong> <?= htmlspecialchars($booking['room_number']) ?><br>
                                                <strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']) ?><br>
                                                <strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']) ?><br>
                                                <strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?><br>
                                                <strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item border-0 text-white" style="background-color: transparent;">No upcoming reservations.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../bed2.jpg); background-size: cover; background-position: center;">
                            <div class="card-body text-center text-white" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center fw-bold mb-4">Room Status</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Occupied</label>
                                                <h3 class="custom-color"><?= $occupied_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Out Of Order</label>
                                                <h3 class="custom-color"><?= $out_of_order_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Not Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_not_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($userRole == 'Guest'): ?>
                <!-- Guest Dashboard -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4 shadow-lg" style="border-radius: 40px;">
                            <div class="card-body">
                                <h5 class="text-center">Reservation History</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Room</th>
                                                <th>Check-in</th>
                                                <th>Check-out</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservation_history as $history): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($history['room_number']) ?></td>
                                                    <td><?= htmlspecialchars($history['check_in']) ?></td>
                                                    <td><?= htmlspecialchars($history['check_out']) ?></td>
                                                    <td><?= htmlspecialchars($history['status']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../lobby1.jpg); background-size: cover; background-position: center;">
                            <div class="card-body" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center text-white">Upcoming Reservations</h5>
                                <ul class="list-group">
                                    <?php if (!empty($upcoming_guest_bookings)): ?>
                                        <?php foreach ($upcoming_guest_bookings as $booking): ?>
                                            <li class="list-group-item border-0 text-white" style="background-color: transparent;">
                                                <strong>Room:</strong> <?= htmlspecialchars($booking['room_number']) ?><br>
                                                <strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in']) ?><br>
                                                <strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out']) ?><br>
                                                <strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?><br>
                                                <strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item border-0 text-white" style="background-color: transparent;">No upcoming reservations.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row for Guest dashboard -->

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card mb-4 border-0" style="background-image: url(../bed2.jpg); background-size: cover; background-position: center;">
                            <div class="card-body text-center text-white" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                <h5 class="text-center fw-bold mb-4">Room Status</h5>
                                <div class="row justify-content-center">
                                    <div class="col-md-5 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Occupied</label>
                                                <h3 class="custom-color"><?= $occupied_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Out Of Order</label>
                                                <h3 class="custom-color"><?= $out_of_order_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-5 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <div class="card bg-transparent border-0">
                                            <div class="card-body text-center">
                                                <label class="form-label fw-bold text-white">Vacant (Not Ready)</label>
                                                <h3 class="custom-color"><?= $vacant_not_ready_rooms ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row for Room Status -->
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../layout/footer.php'; ?>