<?php
session_start();
include '../layout/header.php';
include '../auth/super.php';
require_once '../Database/database.php';
require_once '../models/User.php';

$database = new database();
$conn = $database->getConnection();

User::setConnection($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (!$name || !$status || !$role || !$email || !$password || !$confirm_password) {
        $_SESSION['error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } elseif (User::findByColumn('email', $email)) {
        $_SESSION['error'] = "Email already registered.";
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
                'role' => $role,
                'status' => $status
            ];
            if (User::create($userData)) {
                echo '<script>
            Swal.fire({
                title: "Success!",
                text: "User has been created.",
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
                text: "Failed to save User record, please try again!",
                icon: "error",
                confirmButtonText: "Ok"
            }).then(function() {
                window.location = "create.php";
            });
        </script>';
            }
        }
    }
    // If we get here, there was an error, redirect back to the create page
    header('Location: create.php');
    exit();
}
?>