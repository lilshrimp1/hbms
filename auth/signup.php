<?php
require '../Database/database.php';
require_once '../models/User.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

User::setConnection($conn);

if (isset($_SESSION['email'])) {
    header('Location: ../main/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (!$name || !$contact || !$address || !$email || !$password || !$confirm_password) {
        $_SESSION['error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
    } else {
        $existingUser = User::findByColumn('email', $email);
        if ($existingUser) {
            $_SESSION['error'] = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'Guest',
                'status' => 'active',
                'contact_no' => $contact,
                'address' => $address
            ];
            if (User::create($userData)) {
                $_SESSION['success'] = "Account created successfully. Please log in.";
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['error'] = "Failed to create account.";
            }
        }
    }
    // If we get here, there was an error, redirect back to the login page
    header('Location: login.php');
    exit();
}
?>