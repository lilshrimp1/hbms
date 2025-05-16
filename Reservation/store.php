<?php
session_start();
include '../layout/header.php';
include '../auth/super.php';
require_once '../Database/database.php';
require_once '../models/Reservation.php';
require_once '../models/Room.php';
require_once '../models/Amenity.php';

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
Room::setConnection($conn);
Amenity::setConnection($conn);

// Retrieve form data
$full_name = $_POST['full_name'];
$contact_number = $_POST['contact_number'];
$address = $_POST['address'];
$room_type_id = $_POST['room_type'];
$check_in_date = $_POST['check_in_date'];
$check_out_date = $_POST['check_out_date'];
$selected_amenities = $_POST['amenities'] ?? []; // Optional amenities
$number_of_guests = $_POST['number_of_guests'];

// Validate room availability
$available_room_query = Room::where('type_id', '=', $room_type_id);
$available_room = is_object($available_room_query) ? $available_room_query->where('status', '=', 'available')->first() : null;

if (!$available_room) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "No available rooms for the selected room type.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
    exit();
}

// Validate guest count
if ($number_of_guests > $available_room->capacity) {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Guest count exceeds the room capacity.",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
    exit();
}

// Calculate total bill
$total_bill = $available_room->price; // Start with room price
foreach ($selected_amenities as $amenity_id) {
    $amenity = Amenity::find($amenity_id);
    if ($amenity) {
        $total_bill += $amenity->price;
    }
}

// Prepare reservation data
$reservation_data = [
    'user_id' => $_SESSION['user_id'], // Assuming the logged-in user's ID is stored in the session
    'room_id' => $available_room->id,
    'check_in' => $check_in_date,
    'check_out' => $check_out_date,
    'guests' => $number_of_guests,
    'status' => 'Pending Payment',
    'total_bill' => $total_bill,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

// Store reservation
$result = Reservation::create($reservation_data);

if ($result) {
    // Update room status to "booked"
    $available_room->update(['status' => 'booked']);

    echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Reservation has been created successfully.",
                icon: "success",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "index.php";
            });
        </script>';
} else {
    echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Failed to create reservation, please try again!",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
}
?>