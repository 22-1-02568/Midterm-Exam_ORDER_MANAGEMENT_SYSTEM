<?php
require 'session.php';
check_role(['admin', 'superadmin']);
require 'db.php';
require 'lib/fpdf.php'; // Include FPDF library

// Get dates (same logic as reports.php)
$date_start = $_GET['date_start'] ?? date('Y-m-d');
$date_end = $_GET['date_end'] ?? date('Y-m-d');
$date_end_for_query = $date_end . ' 23:59:59';

// Fetch data (same query as reports.php)
$params = [$date_start, $date_end_for_query];
$sql = "SELECT o.id, o.total_amount, o.date_added, u.username
        FROM orders o
        JOIN users u ON o.cashier_id = u.id
        WHERE o.date_added BETWEEN ? AND ?
        ORDER BY o.date_added ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();
$total_sum = array_sum(array_column($transactions, 'total_amount'));

// Create PDF
class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Blend S Coffee Sales Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, 'Report Date: ' . date('Y-m-d'), 0, 1, 'C');
        $this->Ln(5);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Report Title
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Filtered Report: " . htmlspecialchars($date_start) . " to " . htmlspecialchars($date_end), 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(30, 10, 'Order ID', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Transaction Date', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Cashier', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Total (PHP)', 1, 1, 'C', true);

// Table Rows
$pdf->SetFont('Arial', '', 10);
foreach ($transactions as $t) {
    $pdf->Cell(30, 8, $t['id'], 1);
    $pdf->Cell(60, 8, $t['date_added'], 1);
    $pdf->Cell(50, 8, htmlspecialchars($t['username']), 1);
    $pdf->Cell(50, 8, number_format($t['total_amount'], 2), 1, 1, 'R');
}

// Total Sum
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(140, 10, 'Total Sum', 1, 0, 'R', true);
$pdf->Cell(50, 10, number_format($total_sum, 2), 1, 1, 'R', true);

// Output the PDF
$pdf->Output('D', 'Blend_S_Report_' . $date_start . '_to_' . $date_end . '.pdf');
exit;
?>
