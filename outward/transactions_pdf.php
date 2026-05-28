<?php

ob_start();

include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';
include '../config/db.php';

require('../tfpdf/tfpdf.php');


$pdf = new tFPDF(
    'L',
    'mm',
    'A4'
);

$pdf->AddPage();


/* Add Unicode font */

$pdf->AddFont(
    'DejaVu',
    '',
    'DejaVuSans.ttf',
    true
);

$pdf->SetFont(
    'DejaVu',
    '',
    14
);


/* Heading */

$pdf->Cell(
    0,
    12,
    'Amount Transaction Report',
    1,
    1,
    'C'
);

$pdf->Ln(5);


/* Header row */

$pdf->SetFont(
    'DejaVu',
    '',
    10
);

$pdf->Cell(20,10,'ID',1,0,'C');

$pdf->Cell(50,10,'Amount',1,0,'C');

$pdf->Cell(50,10,'Type',1,0,'C');

$pdf->Cell(70,10,'Date',1,1,'C');


/* Data */

$res = $conn->query("
SELECT *
FROM amount_transactions
ORDER BY id ASC
");

while($r = $res->fetch_assoc()){

    $pdf->Cell(
        20,
        10,
        $r['id'],
        1
    );

    $pdf->Cell(
        50,
        10,
        'Rs. '.$r['amount'],
        1
    );

    $pdf->Cell(
        50,
        10,
        $r['type'],
        1
    );

    $pdf->Cell(
        70,
        10,
        $r['created_at'],
        1
    );

    $pdf->Ln();
}

ob_end_clean();

$pdf->Output(
    'I',
    'amount_transaction_report.pdf'
);

exit;

?>
