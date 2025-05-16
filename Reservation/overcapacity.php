<?php
session_start();
require_once '../database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';

// Verify staff access
if (!in_array($_SESSION['role'], ['Super Admin', 'Admin', 'Front Desk'])) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Unauthorized access",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
    exit;
}

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);

// Get reservation and additional room details
$reservation_id = $_POST['reservation_id'] ?? null;
$additional_room_id = $_POST['additional_room_id'] ?? null;

if (!$reservation_id || !$additional_room_id) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Missing required parameters.",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
    exit;
}

// Fetch the reservation and additional room
$reservation = Reservation::find($reservation_id);
$additional_room = Room::find($additional_room_id);

if (!$reservation || !$additional_room) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Reservation or room not found.",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
    exit;
}

try {
    // Create a new reservation for the additional room
    $additional_reservation = Reservation::create([
        'user_id' => $reservation->user_id,
        'room_id' => $additional_room_id,
        'check_in' => $reservation->check_in,
        'check_out' => $reservation->check_out,
        'guests' => $_POST['overflow_guests'],
        'status' => $reservation->status,
        'total_bill' => $additional_room->price,
        'parent_reservation_id' => $reservation_id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    // Update additional room status
    $additional_room->update([
        'status' => 'booked',
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Additional room assigned successfully.",
                icon: "success",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "show.php?id=' . $reservation_id . '";
            });
        </script>';

} catch (Exception $e) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Failed to assign additional room: ' . $e->getMessage() . '",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
}
?>