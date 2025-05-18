<?php
session_start();

require_once '../Database/database.php';
require_once '../models/Reservation.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $fullName = trim($_POST['fullName'] ?? '');
    $contactNumber = trim($_POST['contactNumber'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $roomTypeId = intval($_POST['roomType'] ?? 0);
    $numberOfGuests = intval($_POST['numberOfGuests'] ?? 1);
    $checkInDate = $_POST['checkInDate'] ?? '';
    $checkOutDate = $_POST['checkOutDate'] ?? '';
    $amenities = $_POST['amenities'] ?? [];
    $totalBill = floatval($_POST['totalBill'] ?? 0);
    $deposit = floatval($_POST['deposit'] ?? 0);

    // Basic validation
    if ($roomTypeId <= 0 || empty($checkInDate) || empty($checkOutDate) || $numberOfGuests <= 0) {
        // Invalid input, redirect back with error (could be improved)
        header('Location: accommodation.php');
        exit();
    }

    // Find an available room id for the selected room type
    $stmtRoom = $conn->prepare("SELECT id FROM room WHERE type_id = ? AND id NOT IN (SELECT room_id FROM reservations WHERE (check_in <= ? AND check_out >= ?) OR (check_in <= ? AND check_out >= ?)) LIMIT 1");
    $stmtRoom->execute([$roomTypeId, $checkOutDate, $checkInDate, $checkOutDate, $checkInDate]);
    $room = $stmtRoom->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        // No available room found, redirect back with error (could be improved)
        header('Location: accommodation.php');
        exit();
    }

    $roomId = $room['id'];

    // Insert reservation into database
    $amenitiesStr = implode(',', $amenities);

    $stmt = $conn->prepare("INSERT INTO reservations (user_id, room_id, guests, check_in, check_out, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())");  // Added updated_at
    $stmt->execute([$user_id, $roomId, $numberOfGuests, $checkInDate, $checkOutDate]);

    $reservationId = $conn->lastInsertId();

    // Insert payment record
    $stmtPayment = $conn->prepare("INSERT INTO payments (reservation_id, amount, status, paid_at, created_at, updated_at) VALUES (?, ?, 'pending', NOW(), NOW(), NOW())");  //Added paid_at, created_at, updated_at
    $stmtPayment->execute([$reservationId, $totalBill]);

    // Redirect back to accommodation.php to show updated summary
    header('Location: accommodation.php');
    exit();
} else {
    // Invalid request method
    header('Location: accommodation.php');
    exit();
}
?>