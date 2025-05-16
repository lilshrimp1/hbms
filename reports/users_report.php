<?php
require_once '../models/User.php';
require_once '../database.php';
require '../plugins/fpdf186/fpdf.php';

$database = new Database();
$conn = $database->getConnection();
User::setConnection($conn);


$status = $_GET['status'] ?? null;
$reportType = $_POST['report'] ?? null;

if ($reportType === 'all_users') {
    $users = User::all(); 
    $reportTitle = "All Users Report";
} elseif ($reportType === 'active_users') {
    $users = User::findStatus('Active'); 
    $reportTitle = "Active Users Report";
} elseif ($reportType === 'inactive_users') {
    $users = User::findStatus('Not Active'); 
    $reportTitle = "Inactive Users Report";
} elseif ($status === 'Active' || $status === 'Not Active') {
    $users = User::findStatus($status);
    $reportTitle = ucfirst($status) . " Users Report";
} else {
    $users = User::all();
    $reportTitle = "All Users Report";
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);

// Table Header
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(50, 10, 'Name', 1, 0, 'C');
$pdf->Cell(65, 10, 'Email', 1, 0, 'C');
$pdf->Cell(30, 10, 'Role', 1, 0, 'C');
$pdf->Cell(0, 10, 'Status', 1, 1, 'C');

// Table Content
$pdf->SetFont('Arial', '', 11);

if ($users) {
    $i = 1;
    foreach ($users as $user) {
        $pdf->Cell(10, 10, $i++, 1, 0, 'C');
        $pdf->Cell(50, 10, $user->first_name . " " . $user->last_name, 1, 0, 'C');
        $pdf->Cell(65, 10, $user->email, 1, 0, 'C');
        $pdf->Cell(30, 10, ucfirst($user->role), 1, 0, 'C');
        $pdf->Cell(0, 10, ucfirst($user->status), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No users found for this status.', 1, 1, 'C');
}

$pdf->Output('I', 'users_report.pdf');
