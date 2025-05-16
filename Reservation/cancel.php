<?php
session_start();
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);

// Get reservation ID
$reservation_id = $_POST['reservation_id'] ?? $_GET['id'];

if (!$reservation_id) {
    echo json_encode(['error' => 'Reservation ID is required']);
    exit;
}

// Fetch the reservation
$reservation = Reservation::find($reservation_id);

if (!$reservation) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Reservation not found.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
    exit();
}

// Check if the user is authorized to cancel this reservation
if ($_SESSION['user_id'] != $reservation->user_id && $_SESSION['role'] != 'Super Admin' && $_SESSION['role'] != 'Admin') {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "You are not authorized to cancel this reservation.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
    exit();
}

// Check if check-in date hasn't started
$today = date('Y-m-d');
if ($today >= $reservation->check_in) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Cannot cancel reservation after check-in date.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
    exit();
}

try {
    // Update reservation status to cancelled
    $result = $reservation->update([
        'status' => 'Cancelled',
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    // Update room status back to available
    $room = Room::find($reservation->room_id);
    if ($room) {
        $room->update(['status' => 'available']);
    }

    if ($result) {
        echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Reservation has been cancelled successfully.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(function() {
                    window.location = "index.php";
                });
            </script>';
    }
} catch (Exception $e) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Failed to cancel reservation: ' . $e->getMessage() . '",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
}
?>