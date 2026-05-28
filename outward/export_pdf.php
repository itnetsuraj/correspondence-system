<?php

ob_start();

error_reporting(E_ALL);
ini_set('display_errors',0);

include_once '../config/session.php';
include_once __DIR__.'/config/auth_check.php';
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
'sent_date',
'subject',
'sent_to',
'department_person',
'postage_amount',
'document_type'

];

if(!in_array($sort,$allowedSort)){
    $sort='register_id';
}

$order=
($order=="ASC")
?
"ASC"
:
"DESC";


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
FROM outward_letters
WHERE language='$language'
AND YEAR(sent_date)='$selectedYear'
";


if(!empty($from) && !empty($to)){

    $sql.="
    AND sent_date
    BETWEEN '$from'
    AND '$to'
    ";
}


/* Establishment filter */

if($role!="admin"){

    $est=mysqli_real_escape_string(
        $conn,
        $_SESSION['establishment']
    );

    $sql.=" AND establishment='$est'";

}
else{

    if(
    isset($_SESSION['selected_establishment'])
    &&
    $_SESSION['selected_establishment']!="ALL"
    ){

        $est=mysqli_real_escape_string(
            $conn,
            $_SESSION['selected_establishment']
        );

        $sql.=" AND establishment='$est'";
    }

}


/* Apply selected sorting */

$sql.=" ORDER BY $sort $order";


$res=$conn->query($sql);


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
"जावक अहवाल"
:
"Outward Report";

$pdf->Cell(
0,
10,
$title,
0,
1,
'C'
);

$pdf->Ln(5);


/* Header row */

$pdf->SetFont(
'NotoSans',
'',
9
);

if($language=="mr"){

$headers=[

"आयडी",
"पत्र क्र.",
"दिनांक",
"पाठविले",
"विषय",
"विभाग",
"टपाल",
"दस्तऐवज",
"शेरा"

];

}
else{

$headers=[

"ID",
"Letter",
"Date",
"Sent To",
"Subject",
"Department",
"Postage",
"Document",
"Remarks"

];

}


$pdf->Cell(15,10,$headers[0],1);
$pdf->Cell(25,10,$headers[1],1);
$pdf->Cell(25,10,$headers[2],1);
$pdf->Cell(35,10,$headers[3],1);
$pdf->Cell(50,10,$headers[4],1);
$pdf->Cell(35,10,$headers[5],1);
$pdf->Cell(20,10,$headers[6],1);
$pdf->Cell(35,10,$headers[7],1);
$pdf->Cell(40,10,$headers[8],1);

$pdf->Ln();


/* Data */

$pdf->SetFont(
'NotoSans',
'',
8
);

while($r=$res->fetch_assoc()){


/* Handle Others document type */

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


$pdf->Cell(
15,
8,
$r['register_id'],
1
);

$pdf->Cell(
25,
8,
$r['letter_no'],
1
);

$pdf->Cell(
25,
8,
$r['sent_date'],
1
);

$pdf->Cell(
35,
8,
$r['sent_to'],
1
);

$pdf->Cell(
50,
8,
$r['subject'],
1
);

$pdf->Cell(
35,
8,
$r['department_person'],
1
);

$pdf->Cell(
20,
8,
"₹ ".$r['postage_amount'],
1
);

$pdf->Cell(
35,
8,
$documentType,
1
);

$pdf->Cell(
40,
8,
$r['remarks'],
1
);

$pdf->Ln();

}

ob_end_clean();

$pdf->Output(
'I',
'outward_report.pdf'
);

exit;

?>
