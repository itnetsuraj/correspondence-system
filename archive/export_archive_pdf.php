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
'Archive Report',
1,
1,
'C'
);

$pdf->Ln(5);


/* Inward */

$pdf->SetFont('Arial','B',12);

$pdf->Cell(
0,
10,
'Archived Inward',
0,
1
);

$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,10,'ID',1);

$pdf->Cell(25,10,'Letter No',1);

$pdf->Cell(25,10,'Date',1);

$pdf->Cell(45,10,'Received From',1);

$pdf->Cell(60,10,'Subject',1);

$pdf->Cell(35,10,'Department',1);

$pdf->Cell(50,10,'Remarks',1);

$pdf->Ln();

$pdf->SetFont('Arial','',8);

$res=$conn->query("
SELECT *
FROM inward_archive
WHERE received_date
BETWEEN '$from'
AND '$to'
");

while($r=$res->fetch_assoc()){

$pdf->Cell(15,10,$r['register_id'],1);

$pdf->Cell(25,10,$r['letter_no'],1);

$pdf->Cell(25,10,$r['received_date'],1);

$pdf->Cell(45,10,$r['received_from'],1);

$pdf->Cell(60,10,$r['subject'],1);

$pdf->Cell(35,10,$r['department_person'],1);

$pdf->Cell(50,10,$r['remarks'],1);

$pdf->Ln();

}


/* Outward */

$pdf->Ln(8);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(
0,
10,
'Archived Outward',
0,
1
);

$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,10,'ID',1);

$pdf->Cell(25,10,'Letter No',1);

$pdf->Cell(25,10,'Date',1);

$pdf->Cell(45,10,'Sent To',1);

$pdf->Cell(60,10,'Subject',1);

$pdf->Cell(35,10,'Department',1);

$pdf->Cell(25,10,'Postage',1);

$pdf->Cell(40,10,'Remarks',1);

$pdf->Ln();

$pdf->SetFont('Arial','',8);

$res2=$conn->query("
SELECT *
FROM outward_archive
WHERE sent_date
BETWEEN '$from'
AND '$to'
");

while($r2=$res2->fetch_assoc()){

$pdf->Cell(15,10,$r2['register_id'],1);

$pdf->Cell(25,10,$r2['letter_no'],1);

$pdf->Cell(25,10,$r2['sent_date'],1);

$pdf->Cell(45,10,$r2['sent_to'],1);

$pdf->Cell(60,10,$r2['subject'],1);

$pdf->Cell(35,10,$r2['department_person'],1);

$pdf->Cell(25,10,$r2['postage_amount'],1);

$pdf->Cell(40,10,$r2['remarks'],1);

$pdf->Ln();

}

$pdf->Output();

?>
