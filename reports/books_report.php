<?php
require_once '../database.php';
require_once '../models/Book.php';
require '../plugins/fpdf186/fpdf.php';

$database = new Database();
$db = $database->getConnection();
Book::setConnection($db);

$reportType = $_POST['report'] ?? null;

if ($reportType === 'all_books') {
    $books = Book::all(); 
    $reportTitle = "All Books List";
} elseif ($reportType === 'books_by_year') {
    $year = $_POST['year'] ?? null;
    if ($year) {
        $books = Book::filterByYear($year); 
        $reportTitle = "Books Published in " . $year;
    } else {
        die('Year is required for this report.');
    }
} else {
    die('Invalid report type.');
}

$pdf = new FPDF();
$pdf->AddPage('P', 'Legal', 0);
$pdf->SetFont('Arial', 'B', 25);
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(10, 10, '#', 1, 0, 'C');
$pdf->Cell(25, 10, 'SKU', 1, 0, 'C');
$pdf->Cell(70, 10, 'Title', 1, 0, 'C');
$pdf->Cell(40, 10, 'Author', 1, 0, 'C');
$pdf->Cell(30, 10, 'Genre', 1, 0, 'C');
$pdf->Cell(0, 10, 'Year', 1, 1, 'C');

$pdf->SetFont('Times', 'I', 11);

if ($books && count($books) > 0) {
    $i = 1;
    foreach ($books as $book) {
        $pdf->Cell(10, 10, $i++, 1, 0, 'C');
        $pdf->Cell(25, 10, $book->sku, 1, 0, 'C');
        $pdf->Cell(70, 10, $book->title, 1, 0, 'C');
        $pdf->Cell(40, 10, $book->author, 1, 0, 'C');
        $pdf->Cell(30, 10, $book->genre, 1, 0, 'C');
        $pdf->Cell(0, 10, $book->year_published, 1, 1, 'C');
    }
} else {
    $pdf->Cell(0, 10, 'No books available', 0, 1, 'C');
}

$pdf->Output('I', 'books.pdf');
