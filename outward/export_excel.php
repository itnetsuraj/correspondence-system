<?php

include_once '../config/session.php';
include_once __DIR__.'/config/auth_check.php';
include '../config/db.php';

$language=$_SESSION['lang'] ?? 'en';
$role=$_SESSION['admin_role'] ?? '';

$from=$_GET['from'] ?? '';
$to=$_GET['to'] ?? '';
$selectedYear=$_GET['year'] ?? date('Y');


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


/* Excel download */

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=outward_report.xls");

echo "\xEF\xBB\xBF";


/* Report info */

if($language=="mr"){

echo "कार्यालय\t".$displayEstablishment."\n";
echo "वर्ष\t".$selectedYear."\n";

if(!empty($from)&&!empty($to)){

echo "दिनांक\t".$from." ते ".$to."\n";

}

}
else{

echo "Establishment\t".$displayEstablishment."\n";
echo "Year\t".$selectedYear."\n";

if(!empty($from)&&!empty($to)){

echo "Date Range\t".$from." To ".$to."\n";

}

}

echo "\n";


/* Query */

$sql="
SELECT *
FROM outward_letters
WHERE language='$language'
AND YEAR(sent_date)='$selectedYear'
";


if(!empty($from)&&!empty($to)){

$sql.="
AND sent_date
BETWEEN '$from'
AND '$to'
";

}


if($role!="admin"){

$est=mysqli_real_escape_string(
$conn,
$_SESSION['establishment']
);

$sql.=" AND establishment='$est'";

}
else{

if($_SESSION['selected_establishment']!="ALL"){

$est=mysqli_real_escape_string(
$conn,
$_SESSION['selected_establishment']
);

$sql.=" AND establishment='$est'";

}

}

$sql.=" ORDER BY register_id DESC";

$res=$conn->query($sql);


/* Column headers */

if($language=="mr"){

echo
"क्रमांक\t".
"पत्र क्र.\t".
"दिनांक\t".
"पाठविले\t".
"विषय\t".
"विभाग\t".
"टपाल खर्च\t".
"शेरा\n";

}
else{

echo
"ID\t".
"Letter No\t".
"Date\t".
"Sent To\t".
"Subject\t".
"Department\t".
"Postage\t".
"Remarks\n";

}


/* Data rows */

while($r=$res->fetch_assoc()){

echo
$r['register_id']."\t".
$r['letter_no']."\t".
$r['sent_date']."\t".
$r['sent_to']."\t".
$r['subject']."\t".
$r['department_person']."\t".
$r['postage_amount']."\t".
$r['remarks']."\n";

}

exit;

?>
