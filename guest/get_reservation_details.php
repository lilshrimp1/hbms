<?php
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';
require_once '../models/RoomType.php';
require_once '../models/Payment.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Reservation ID is required']);
    exit;
}

$reservationId = intval($_GET['id']);

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);
RoomType::setConnection($conn);
Payment::setConnection($conn);

try {
    $reservation = Reservation::find($reservationId);

    if (!$reservation) {
        echo json_encode(['error' => 'Reservation not found']);
        exit;
    }

    // Fetch related room and room type
    $room = Room::find($reservation->room_id);
    $roomType = $room ? RoomType::find($room->type_id) : null;

    // Fetch payments and calculate totals
    $payments = Payment::where('reservation_id', '=', $reservation->id);
    $totalPaid = 0;
    $totalDue = 0;
    if ($payments) {
        foreach ($payments as $payment) {
            $totalDue += $payment->amount;
            if ($payment->status === 'confirmed') {
                $totalPaid += $payment->amount;
            }
        }
    }
    $balance = $totalDue - $totalPaid;

    $response = [
        'reservation_id' => $reservation->id,
        'user_id' => $reservation->user_id,
        'room_type_name' => $roomType ? $roomType->name : null,
        'room_number' => $room ? $room->room_number : null,
        'check_in' => $reservation->check_in,
        'check_out' => $reservation->check_out,
        'guests' => $reservation->guests,
        'reservation_status' => $reservation->status,
        'total_paid' => $totalPaid,
        'total_due' => $totalDue,
        'balance' => $balance
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching reservation details: ' . $e->getMessage()]);
}
?>
