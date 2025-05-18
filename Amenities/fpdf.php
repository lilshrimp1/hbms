<?php
require_once '../models/Amenity.php';
require_once '../Database/database.php';
require '../plugins/fpdf186/fpdf.php';

$database = new Database();
$conn = $database->getConnection();
Amenity::setConnection($conn);
$amenities = Amenity::all();

$pdf = new FPDF('L', 'mm', 'Legal');
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 20);
$logoWidth = 15;
$text = 'HBMS';
$textWidth = $pdf->GetStringWidth($text) + 2;

// Total width of logo + space + text
$totalWidth = $logoWidth + 5 + $textWidth;

// Centered X position for the whole block
$pageWidth = $pdf->GetPageWidth();
$x = ($pageWidth - $totalWidth) / 2;

// Position image and text
$pdf->SetY(10); // Vertical positioning (adjust as needed)
$pdf->SetX($x);
$pdf->Image('../images/logo.png', $x, 10, $logoWidth); // draw image
$pdf->SetX($x + $logoWidth + 5); // move to right of image
$pdf->Cell($textWidth, 20, $text, 0, 1); // draw text

$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, 'Amenities Report', 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);

// Table Header
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(50, 10, 'Name', 1, 0, 'C');
$pdf->Cell(65, 10, 'Price', 1, 0, 'C');
$pdf->Cell(182, 10, 'Description', 1, 0, 'C');
$pdf->Cell(0, 10, 'Status', 1, 1, 'C');

// Table Content
$pdf->SetFont('Arial', '', 11);
    
if ($amenities = Amenity::all()) {
    $i = 1;
    foreach ($amenities as $amenity) {
        $pdf->Cell(10, 10, $i++, 1, 0, 'C');
        $pdf->Cell(50, 10, $amenity->name, 1, 0, 'C');
        $pdf->Cell(65, 10, $amenity->price, 1, 0, 'C');
        $pdf->Cell(182, 10, $amenity->description, 1, 0, 'C');
        $pdf->Cell(0, 10, ucfirst($amenity->status), 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No amenities found for this status.', 0, 1, 'C');
}

$pdf->Output('I', 'amenities_report.pdf');
