<?php

ob_start();

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include_once __DIR__.'/../config/auth_check.php';
include '../config/db.php';

require('../tfpdf/tfpdf.php');

$language=$_SESSION['lang'] ?? 'en';
$role=$_SESSION['admin_role'] ?? '';

$from=$_GET['from'] ?? '';
$to=$_GET['to'] ?? '';
$selectedYear=$_GET['year'] ?? date('Y');

/* Sorting */
$sort=$_GET['sort'] ?? 'register_id';
$order=$_GET['order'] ?? 'DESC';

$allowedSort=[

'register_id',
'letter_no',
'received_date',
'subject',
'received_from',
'department_person',
'quantity',
'document_type'

];

if(!in_array($sort,$allowedSort)){

$sort='register_id';

}

$order=($order=="ASC") ? "ASC" : "DESC";


/* Establishment */

if($role=="admin"){

    $displayEstablishment=
    $_SESSION['selected_establishment']
    ?? "ALL";

}else{

    $displayEstablishment=
    $_SESSION['establishment'];

}

if($displayEstablishment=="ALL"){

    $displayEstablishment="All Establishments";

}


/* Query */

$sql="
SELECT *
FROM inward_letters
WHERE language=?
AND YEAR(received_date)=?
";

$params=[];
$types="si";

$params[]=$language;
$params[]=$selectedYear;


/* Date filter */

if(!empty($from) && !empty($to)){

$sql.=" AND received_date BETWEEN ? AND ?";

$types.="ss";

$params[]=$from;
$params[]=$to;

}


/* Establishment */

if($role!="admin"){

$sql.=" AND establishment=?";

$types.="s";

$params[]=$_SESSION['establishment'];

}

$sql.=" ORDER BY $sort $order";


$stmt=$conn->prepare($sql);

$stmt->bind_param($types,...$params);

$stmt->execute();

$res=$stmt->get_result();


/* PDF */

$pdf=new tFPDF(
'L',
'mm',
'A4'
);

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


/* Heading */

$pdf->Cell(
0,
10,
$displayEstablishment,
0,
1,
'C'
);

$title=
($language=="mr")
?
"आवक अहवाल"
:
"Inward Report";

$pdf->Cell(
0,
10,
$title,
0,
1,
'C'
);

$pdf->Ln(5);


/* Headers */

if($language=="mr"){

$headers=[

"क्रमांक",
"पत्र क्र.",
"दिनांक",
"कडून प्राप्त",
"विषय",
"विभाग",
"प्रती",
"दस्तऐवज प्रकार",
"शेरा"

];

}else{

$headers=[

"ID",
"Letter No",
"Date",
"Received From",
"Subject",
"Department",
"Copies",
"Document Type",
"Remarks"

];

}


/* Header row */

$pdf->SetFont(
'NotoSans',
'',
9
);

$pdf->Cell(15,10,$headers[0],1);
$pdf->Cell(25,10,$headers[1],1);
$pdf->Cell(22,10,$headers[2],1);
$pdf->Cell(35,10,$headers[3],1);
$pdf->Cell(55,10,$headers[4],1);
$pdf->Cell(35,10,$headers[5],1);
$pdf->Cell(15,10,$headers[6],1);
$pdf->Cell(35,10,$headers[7],1);
$pdf->Cell(38,10,$headers[8],1);

$pdf->Ln();


/* Data */

$pdf->SetFont(
'NotoSans',
'',
8
);

while($r=$res->fetch_assoc()){


$documentType=

(
isset($r['document_type'])
&&
$r['document_type']=="Others"
&&
!empty($r['other_document_type'])
)

?

$r['other_document_type']

:

$r['document_type'];


$pdf->Cell(15,8,$r['register_id'],1);
$pdf->Cell(25,8,$r['letter_no'],1);
$pdf->Cell(22,8,$r['received_date'],1);
$pdf->Cell(35,8,$r['received_from'],1);
$pdf->Cell(55,8,$r['subject'],1);
$pdf->Cell(35,8,$r['department_person'],1);
$pdf->Cell(15,8,$r['quantity'],1);
$pdf->Cell(35,8,$documentType,1);
$pdf->Cell(38,8,$r['remarks'],1);

$pdf->Ln();

}

ob_end_clean();

$pdf->Output(
'I',
'inward_report.pdf'
);

exit;

?>
