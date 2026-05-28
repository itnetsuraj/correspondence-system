<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once '../config/session.php';
require_once '../config/auth_check.php';
require_once '../config/security_headers.php';
require_once '../config/db.php';

/** @var mysqli $conn */

include '../header.php';
include '../lang.php';


require '../fpdf/fpdf.php';

$from=$_GET['from'];
$to=$_GET['to'];

$pdf=new FPDF('L','mm','A4');

$pdf->AddPage();

$pdf->SetFont('Arial','B',16);

$pdf->Cell(
0,
12,
'Archived Outward Report',
1,
1,
'C'
);

$pdf->Ln(3);

$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,10,'ID',1);
$pdf->Cell(20,10,'Letter No',1);
$pdf->Cell(30,10,'Date',1);
$pdf->Cell(40,10,'Sent To',1);
$pdf->Cell(50,10,'Subject',1);
$pdf->Cell(40,10,'Department',1);
$pdf->Cell(25,10,'Postage',1);
$pdf->Cell(50,10,'Remarks',1);

$pdf->Ln();

$pdf->SetFont('Arial','',8);

$res=$conn->query("
SELECT *
FROM outward_archive
WHERE sent_date
BETWEEN '$from'
AND '$to'
");

while($r=$res->fetch_assoc()){

$pdf->Cell(15,10,$r['register_id'],1);
$pdf->Cell(20,10,$r['letter_no'],1);
$pdf->Cell(30,10,$r['sent_date'],1);
$pdf->Cell(40,10,$r['sent_to'],1);
$pdf->Cell(50,10,$r['subject'],1);
$pdf->Cell(40,10,$r['department_person'],1);
$pdf->Cell(25,10,$r['postage_amount'],1);
$pdf->Cell(50,10,$r['remarks'],1);

$pdf->Ln();

}

$pdf->Output();

?>
