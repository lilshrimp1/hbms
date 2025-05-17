<?php
require_once '../Database/database.php';
require_once '../models/Room.php';
require_once '../models/RoomType.php';
require '../plugins/fpdf186/fpdf.php';

$database = new Database();
$db = $database->getConnection();
Room::setConnection($db);

RoomType::setConnection($db);


$pdf = new FPDF();
$pdf->AddPage('P', 'Legal', 0);
$pdf->SetFont('Arial', 'B', 25);
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(25, 10, 'Room Number', 1, 0, 'C');
$pdf->Cell(70, 10, 'Room Type', 1, 0, 'C');
$pdf->Cell(40, 10, 'Price', 1, 0, 'C');
$pdf->Cell(30, 10, 'Capacity', 1, 0, 'C');
$pdf->Cell(0, 10, 'Status', 1, 1, 'C');
$pdf->Cell(0, 10, 'Description', 1, 1, 'C');


$pdf->SetFont('Times', 'I', 11);

if ($rooms && count($rooms) > 0) {
    $i = 1;
    foreach ($rooms as $room) {
        $room_type = RoomType::find($room->type_id);
        $pdf->Cell(10, 10, $i++, 1, 0, 'C');
        $pdf->Cell(25, 10, $room->room_number, 1, 0, 'C');
        $pdf->Cell(70, 10, $room_type->name, 1, 0, 'C');
        $pdf->Cell(40, 10, $room->price, 1, 0, 'C');
        $pdf->Cell(30, 10, $room->capacity, 1, 0, 'C');
        $pdf->Cell(0, 10, $room->status, 1, 1, 'C');
        $pdf->Cell(0, 10, $room->description, 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No rooms available', 0, 1, 'C');
}

$pdf->Output('I', 'rooms.pdf');
