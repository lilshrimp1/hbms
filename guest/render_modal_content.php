<?php
header('Content-Type: text/html; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['modal']) || !isset($input['data'])) {
    echo '<p>Invalid request.</p>';
    exit;
}

$modal = $input['modal'];
$data = $input['data'];

// Include necessary files and classes
include_once '../Database/database.php';
include_once '../models/guest.php';
include_once '../models/model.php';
include_once '../models/room.php';
include_once '../models/reservation.php';
include_once '../models/roomtype.php';
include_once '../layout/modals/modals.php';

// Setup database connection
$database = new database();
$conn = $database->getConnection();
Model::setConnection($conn);
Reservation::setConnection($conn);
RoomType::setConnection($conn);
Room::setConnection($conn);

$modals = new Modals();

switch ($modal) {
    case 'viewReservation':
        // Render readonly form for viewReservation modal
        echo Modals::viewReservationForm($data);
        break;

    case 'editReservation':
        // Render only the inner content for editReservation modal
        echo Modals::editReservationForm($data);
        break;

    default:
        echo '<p>Unknown modal type.</p>';
        break;
}
?>
