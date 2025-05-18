
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

