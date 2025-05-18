<?php
require_once '../Database/database.php';
require_once '../models/User.php';
session_start();
include '../layout/header.php';
include '../auth/super.php'; // only super admins

$database = new database();
$conn = $database->getConnection();
User::setConnection($conn);

$errors = [];
$success = '';
$showSweetAlert = false;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$user = User::find($_GET['id']);

if (!$user) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Validation
    if (empty($full_name) || empty($email) || empty($role) || empty($status)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if email is unique (except current user)
    $existingUser = User::findByColumn('email', $email);
    if ($existingUser && $existingUser->id != $user->id) {
        $errors[] = "Email already exists.";
    }

    if (empty($errors)) {
        $user->name = $full_name;
        $user->email = $email;
        $user->role = $role;
        $user->status = $status;

        // Don't change password here

        if ($user->save()) {
            $showSweetAlert = true;
        } else {
            $errors[] = "Failed to update user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans:wght@400;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: url('../images/bg.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cal Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md mt-20">
        <h2 class="text-2xl font-semibold mb-4 text-center">Edit User</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?= htmlspecialchars($user->id) ?>" method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user->name) ?>" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?>" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block mb-1 font-medium">Role</label>
                <select name="role" class="w-full border p-2 rounded" required>
                    <option value="">-- Select Role --</option>
                    <option value="Admin" <?= $user->role === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Front Desk" <?= $user->role === 'Front Desk' ? 'selected' : '' ?>>Front Desk</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Status</label>
                <select name="status" class="w-full border p-2 rounded" required>
                    <option value="Active" <?= $user->status === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= $user->status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex justify-end items-center pt-6 space-x-2">
                <a href="index.php" class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-6 rounded">
                    Back
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>

    <?php if ($showSweetAlert): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'User updated successfully!',
            confirmButtonText: 'OK',
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>
    <?php endif; ?>
</body>
</html>
