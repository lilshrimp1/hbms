<?php
session_start();
require_once '../Database/database.php';
require_once '../models/User.php';
require_once '../auth/super.php';

header('Content-Type: application/json'); // Respond with JSON

$database = new Database();
$conn = $database->getConnection();
User::setConnection($conn);

// Validate required GET parameters
if (!isset($_GET['id']) || !isset($_GET['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$id = $_GET['id'];
$password = $_GET['password'];

$user = User::find($id);

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found.'
    ]);
    exit;
}

// Hash the new password and update
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$user->password = $hashedPassword;

if ($user->save()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Password updated successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update password.'
    ]);
}
