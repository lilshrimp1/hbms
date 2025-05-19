<?php
session_start();
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';
require_once '../models/Payment.php';

$database = new database();
$conn = $database->getConnection();
Reservation::setConnection($conn);

include '../layout/header.php';
include '../auth/super.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Reservation ID is required.</div>';
    exit;
}

$id = $_GET['id'];
$reservation = Reservation::findByColumn('id', $id);
$room = Room::findByColumn('id', $reservation->room_id);

if (!$reservation) {
    echo '<div class="alert alert-danger">Reservation not found.</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_reservation'])) {
    $reservation->status = 'Confirmed';
    $room->status = 'Booked';

    if (!$reservation->save() && $room->save()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "Reservation status updated to Confirmed and room status set to Booked.",
                    icon: "success"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to update status. Please try again.",
                    icon: "error"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    }
    exit;
}
?>

<div class="container mt-5">
    <div class="card p-4 shadow" style="max-width: 500px; margin: 0 auto;">
        <h3 class="mb-4 text-center">Confirm Reservation</h3>
        <p>Are you sure you want to set this reservation status to <strong>Confirmed</strong>?</p>
        <form method="POST">
            <input type="hidden" name="confirm_reservation" value="1">
            <button type="submit" class="btn btn-success">Yes, Confirm</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../layout/footer.php'; ?>