<?php 
session_start();
require_once '../Database/database.php';
require_once '../models/User.php';
require_once '../auth/super.php';
include '../layout/header.php';

$database = new database();
$conn = $database->getConnection();
User::setConnection($conn);

$id = $_GET['id'];
$user = User::find($id);

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($newPassword) < 6) {
        echo '<script>alert("Password must be at least 6 characters.");</script>';
    } elseif ($newPassword !== $confirmPassword) {
        echo '<script>alert("Passwords do not match.");</script>';
    } else {
        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->save();

        echo '<script>
            alert("Password updated successfully.");
            window.location.href = "index.php";
        </script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <form method="POST">
        <h2>Reset Password for <?php echo htmlspecialchars($user->name); ?></h2>
        <label>New Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
