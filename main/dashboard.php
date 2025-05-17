<?php
// dashboard_template.php
// Ensure template_data is available; initialize with defaults to prevent errors
$template_data = $template_data ?? [];
$userRole = $_SESSION['role'] ?? '';
?>

<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<style>
    .custom-color {
        color: #9ACBD0 !important;
    }
    .reviews-container {
        margin-bottom: 1.5rem;
    }
    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .reviews-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .reviews-count {
        font-size: 1rem;
        color: #6c757d;
    }
    .review-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .reviewer-name {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .review-text {
        margin-bottom: 0.5rem;
    }
    .rating {
        color: #ffc107;
    }
</style>

<link href="assets/tagabenta/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<div class="container-xxl mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <?php if (in_array($userRole, ['Super Admin', 'Admin', 'Front Desk'])): ?>
                <!-- Role-Specific Dashboard -->
                <div class="row">
                    <?php if (isset($template_data['cards']) && !empty($template_data['cards'])): ?>
                        <!-- Cards Section (User Counts or Today's Activity) -->
                        <div class="col-md-12">
                            <div class="card mb-4 shadow-lg" style="border-radius: 40px;">
                                <div class="card-body">
                                    <h5 class="text-center"><?php echo $userRole == 'Front Desk' ? 'Today\'s Activity' : 'User Statistics'; ?></h5>
                                    <div class="row gx-3 mb-4">
                                        <?php foreach ($template_data['cards'] as $card): ?>
                                            <div class="<?php echo htmlspecialchars($card['col_size']); ?> mb-3">
                                                <div class="card border-0 bg-transparent">
                                                    <div class="card-body text-center">
                                                        <label class="form-label fw-bold"><?php echo htmlspecialchars($card['title']); ?></label>
                                                        <h3 class="custom-color"><?php echo htmlspecialchars($card['value']); ?></h3>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($template_data['table']) && !empty($template_data['table'])): ?>
                        <!-- Reservation History Table (Guest) -->
                        <div class="col-md-12">
                            <div class="card mb-4 shadow-lg" style="border-radius: 40px;">
                                <div class="card-body">
                                    <h5 class="text-center"><?php echo htmlspecialchars($template_data['table']['title']); ?></h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <?php foreach ($template_data['table']['headers'] as $header): ?>
                                                        <th><?php echo htmlspecialchars($header); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($template_data['table']['rows'])): ?>
                                                    <?php foreach ($template_data['table']['rows'] as $row): ?>
                                                        <tr>
                                                            <?php foreach ($row as $cell): ?>
                                                                <td><?php echo $cell; ?></td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="<?php echo count($template_data['table']['headers']); ?>">No reservation history available.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($template_data['upcoming_bookings']) && !empty($template_data['upcoming_bookings'])): ?>
                        <!-- Upcoming Reservations -->
                        <div class="col-md-6">
                            <div class="card mb-4 border-0" style="background-image: url(../images/Lobby1.jpg); background-size: cover; background-position: center;">
                                <div class="card-body" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                    <h5 class="text-center text-white">Upcoming Reservations</h5>
                                    <ul class="list-group">
                                        <?php foreach ($template_data['upcoming_bookings'] as $booking): ?>
                                            <li class="list-group-item border-0 text-white" style="background-color: transparent;">
                                                <strong>Room:</strong> <?php echo htmlspecialchars($booking['room_number']); ?><br>
                                                <strong>Check-in:</strong> <?php echo htmlspecialchars($booking['check_in']); ?><br>
                                                <strong>Check-out:</strong> <?php echo htmlspecialchars($booking['check_out']); ?><br>
                                                <strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?><br>
                                                <?php if (isset($booking['guest_name'])): ?>
                                                    <strong>Guest:</strong> <?php echo htmlspecialchars($booking['guest_name']); ?><br>
                                                <?php endif; ?>
                                                <strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($template_data['latest_reviews']) && !empty($template_data['latest_reviews'])): ?>
                        <!-- Latest Reviews (Super Admin/Admin) -->
                        <div class="col-md-6">
                            <div class="reviews-container">
                                <div class="reviews-header">
                                    <h2 class="reviews-title">LATEST FEEDBACKS</h2>
                                    <span class="reviews-count">Total Visible Reviews: <?php echo htmlspecialchars($template_data['total_visible_reviews'] ?? 0); ?></span>
                                </div>
                                <?php foreach ($template_data['latest_reviews'] as $review): ?>
                                    <div class="review-card">
                                        <div class="reviewer-name"><?php echo htmlspecialchars($review['guest_name']); ?></div>
                                        <div class="review-text"><?php echo htmlspecialchars(mb_strimwidth($review['comment'], 0, 100, '...')); ?></div>
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
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($template_data['room_status'])): ?>
                        <!-- Room Status -->
                        <div class="col-md-6">
                            <div class="card mb-4 border-0" style="background-image: url(../images/Room1.jpg); background-size: cover; background-position: center;">
                                <div class="card-body text-center text-white" style="background-color: rgba(0, 0, 0, 0.5); border-radius: 10px;">
                                    <h5 class="text-center fw-bold mb-4">Room Status</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <label class="form-label fw-bold text-white">Occupied</label>
                                                    <h3 class="custom-color"><?php echo htmlspecialchars($template_data['room_status']['occupied'] ?? 0); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <label class="form-label fw-bold text-white">Out Of Order</label>
                                                    <h3 class="custom-color"><?php echo htmlspecialchars($template_data['room_status']['out_of_order'] ?? 0); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <label class="form-label fw-bold text-white">Vacant (Ready)</label>
                                                    <h3 class="custom-color"><?php echo htmlspecialchars($template_data['room_status']['vacant_ready'] ?? 0); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-transparent border-0">
                                                <div class="card-body text-center">
                                                    <label class="form-label fw-bold text-white">Vacant (Not Ready)</label>
                                                    <h3 class="custom-color"><?php echo htmlspecialchars($template_data['room_status']['vacant_not_ready'] ?? 0); ?></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Fallback for invalid roles -->
                <div class="alert alert-warning text-center">
                    Invalid user role. Please contact the administrator.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>