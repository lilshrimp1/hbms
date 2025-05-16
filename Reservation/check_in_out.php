<?php
session_start();
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);
Payment::setConnection($conn);

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

// Get reservation and action type
$reservation_id = $_POST['reservation_id'] ?? null;
$action = $_POST['action'] ?? null; // 'check_in' or 'check_out'

if (!$reservation_id || !$action) {
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

// Fetch the reservation
$reservation = Reservation::find($id);
if (!$reservation) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Reservation not found.",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
    exit;
}

$payment = Payment::find($reservation->id);
try {
    switch ($action) {
        case 'check_in':
            // Update reservation status
            $reservation->update([
                'status' => 'checked-in',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update room status to occupied
            $room = Room::find($reservation->room_id);
            $room->update(['status' => 'occupied']);

            $message = "Guest checked in successfully.";
            break;
            
        case 'check_out':
            // Verify payment status
            if ($payment->status !== 'Paid') {
                echo '<script>
                        Swal.fire({
                            title: "Error!",
                            text: "Cannot check out: Payment is pending.",
                            icon: "error",
                            confirmButtonText: "Ok"
                        });
                    </script>';
                exit;
            }

            // Update reservation status
            $reservation->update([
                'status' => 'completed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update room status to available
            $room = Room::find($reservation->room_id);
            $room->update([
                'status' => 'available',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $message = "Guest checked out successfully.";
            break;

        default:
            throw new Exception("Invalid action specified.");
    }

    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "' . $message . '",
                icon: "success",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';

} catch (Exception $e) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Operation failed: ' . $e->getMessage() . '",
                icon: "error",
                confirmButtonText: "Ok"
            });
        </script>';
}
?>