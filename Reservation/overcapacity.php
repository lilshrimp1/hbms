<?php
session_start();
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';

// Verify staff access
if (!in_array($_SESSION['role'], ['Super Admin', 'Admin', 'Front Desk'])) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Unauthorized access",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
            });
        </script>';
    exit;
}

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);

// Get reservation ID from POST or GET
$reservation_id = $_POST['reservation_id'] ?? $_GET['reservation_id'] ?? null;

if (!$reservation_id) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Missing reservation ID.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
            });
        </script>';
    exit;
}

// Fetch the reservation and its room
$reservation = Reservation::find($reservation_id);
if (!$reservation) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Reservation not found.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
            });
        </script>';
    exit;
}
$room = Room::find($reservation->room_id);
if (!$room) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Room not found.",
                    icon: "error",
                    confirmButtonText: "Ok"
                });
            });
        </script>';
    exit;
}

// Check if guest count exceeds room capacity
if ($reservation->guests > $room->capacity) {
    $overflow_guests = $reservation->guests - $room->capacity;

    // Find an available room with enough capacity for overflow guests
    $sql = "SELECT * FROM room WHERE status = 'available' AND capacity >= :overflow_guests LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':overflow_guests', $overflow_guests, PDO::PARAM_INT);
    $stmt->execute();
    $additional_room = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$additional_room) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "No available room for overflow guests.",
                        icon: "error",
                        confirmButtonText: "Ok"
                    });
                });
            </script>';
        exit;
    }

    try {
        // Create a new reservation for the additional room
        $additional_reservation = Reservation::create([
            'user_id' => $reservation->user_id,
            'room_id' => $additional_room->id,
            'check_in' => $reservation->check_in,
            'check_out' => $reservation->check_out,
            'guests' => $overflow_guests,
            'status' => $reservation->status,
            'total_bill' => $additional_room->price,
            'parent_reservation_id' => $reservation_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update additional room status
        Room::setConnection($conn);
        $roomObj = Room::find($additional_room->id);
        $roomObj->status = 'booked';
        $roomObj->updated_at = date('Y-m-d H:i:s');
        $roomObj->save();

        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Additional room assigned for overflow guests.",
                        icon: "success",
                        confirmButtonText: "Ok"
                    });
                });
            </script>';

    } catch (Exception $e) {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to assign additional room: ' . $e->getMessage() . '",
                        icon: "error",
                        confirmButtonText: "Ok"
                    });
                });
            </script>';
    }
} else {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "No overflow. Guest count fits in the current room.",
                    icon: "success",
                    confirmButtonText: "Ok"
                });
            });
        </script>';
}
?>