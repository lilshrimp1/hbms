<?php
require_once '../models/Amenity.php';
require_once '../Database/database.php';
require '../plugins/fpdf186/fpdf.php';

$database = new Database();
$conn = $database->getConnection();
Amenity::setConnection($conn);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);

// Table Header
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(50, 10, 'Name', 1, 0, 'C');
$pdf->Cell(65, 10, 'Price', 1, 0, 'C');
$pdf->Cell(30, 10, 'Description', 1, 0, 'C');
$pdf->Cell(0, 10, 'Status', 1, 1, 'C');

// Table Content
$pdf->SetFont('Arial', '', 11);
    
if ($amenities) {
    $i = 1;
    foreach ($amenities as $amenity) {
        $pdf->Cell(10, 10, $i++, 1, 0, 'C');
        $pdf->Cell(50, 10, $amenity->name, 1, 0, 'C');
        $pdf->Cell(65, 10, $amenity->price, 1, 0, 'C');
        $pdf->Cell(30, 10, $amenity->description, 1, 0, 'C');
        $pdf->Cell(0, 10, ucfirst($amenity->status), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No amenities found for this status.', 1, 1, 'C');
}

$pdf->Output('I', 'amenities_report.pdf');
