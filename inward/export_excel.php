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

$displayEstablishment=
"All Establishments";

}


/* Excel Download */

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=inward_report.xls");

echo "\xEF\xBB\xBF";


/* Report Information */

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
FROM inward_letters
WHERE language='$language'
AND YEAR(received_date)='$selectedYear'
";


if(!empty($from)&&!empty($to)){

$sql.="
AND received_date
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

$sql.=" ORDER BY register_id DESC";

$res=$conn->query($sql);


/* Column Headers */

if($language=="mr"){

echo
"क्रमांक\t".
"पत्र क्र.\t".
"दिनांक\t".
"कडून प्राप्त\t".
"विषय\t".
"विभाग\t".
"शेरा\n";

}
else{

echo
"ID\t".
"Letter No\t".
"Date\t".
"Received From\t".
"Subject\t".
"Department\t".
"Remarks\n";

}


/* Data Rows */

while($r=$res->fetch_assoc()){

echo
$r['register_id']."\t".
$r['letter_no']."\t".
$r['received_date']."\t".
$r['received_from']."\t".
$r['subject']."\t".
$r['department_person']."\t".
$r['remarks']."\n";

}

exit;

?>
