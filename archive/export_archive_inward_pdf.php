<?php

ob_start();

include_once '../config/session.php';
include_once __DIR__.'/config/auth_check.php';
include '../config/db.php';

require('../tfpdf/tfpdf.php');

$year=$_GET['year'] ?? date('Y')-1;

$pdf=new tFPDF('L','mm','A4');

$pdf->AddPage();

$pdf->AddFont(
'NotoSans',
'',
'NotoSansDevanagari-Regular.ttf',
true
);

$pdf->SetFont(
'NotoSans',
'',
12
);

$pdf->Cell(
0,
10,
"Archived Inward Report - ".$year,
0,
1,
'C'
);

$pdf->Ln(5);

$pdf->SetFont(
'NotoSans',
'',
8
);

$pdf->Cell(15,10,'ID',1);
$pdf->Cell(30,10,'Letter',1);
$pdf->Cell(25,10,'Date',1);
$pdf->Cell(50,10,'Received',1);
$pdf->Cell(55,10,'Subject',1);
$pdf->Cell(40,10,'Department',1);
$pdf->Cell(55,10,'Remarks',1);

$pdf->Ln();

$res=$conn->query("

SELECT *
FROM inward_archive
WHERE YEAR(received_date)='$year'
ORDER BY register_id ASC

");

while($r=$res->fetch_assoc()){

$pdf->Cell(15,8,$r['register_id'],1);
$pdf->Cell(30,8,$r['letter_no'],1);
$pdf->Cell(25,8,$r['received_date'],1);
$pdf->Cell(50,8,$r['received_from'],1);
$pdf->Cell(55,8,$r['subject'],1);
$pdf->Cell(40,8,$r['department_person'],1);
$pdf->Cell(55,8,$r['remarks'],1);

$pdf->Ln();

}

ob_end_clean();

$pdf->Output(
'I',
'archive_inward.pdf'
);

exit;

?>
