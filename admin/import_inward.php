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


include '../config/save_log.php';

if(
!isset($_SESSION['admin_role'])
||
$_SESSION['admin_role']!="admin"
){

header("Location:../dashboard.php");
exit;

}

$success=0;
$skipped=0;
$errors=0;

if(isset($_POST['upload'])){


/* CSRF */

if(
!isset($_POST['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'] ?? '',
$_POST['csrf_token']
)
){

die("Invalid CSRF Token");

}


/* File validation */

if(
isset($_FILES['csvfile'])
&&
$_FILES['csvfile']['error']==0
){

$fileName=$_FILES['csvfile']['name'];

$extension=
strtolower(
pathinfo(
$fileName,
PATHINFO_EXTENSION
)
);

if($extension!="csv"){

die("Only CSV files allowed");

}

$file=$_FILES['csvfile']['tmp_name'];

if(($handle=fopen($file,"r"))!==FALSE){

/* Skip heading */

fgetcsv($handle);


while(
($data=fgetcsv($handle,1000,","))
!==FALSE
){

if(empty(array_filter($data))){
continue;
}


if(count($data)<8){

$errors++;
continue;

}


/* Clean data */

$register_id=(int)trim($data[0]);

$letter_no=trim($data[1]);

$received_from=trim($data[2]);

$department_person=trim($data[3]);

$subject=trim($data[4]);

$remarks=trim($data[5]);

$received_date=trim($data[6]);

$establishment=trim($data[7]);


/* Date conversion */

if(
strpos(
$received_date,
"/"
)!==false
){

$dateParts=
explode(
"/",
$received_date
);

if(count($dateParts)==3){

$received_date=
$dateParts[2]
."-".
$dateParts[1]
."-".
$dateParts[0];

}

}


/* Validate date */

if(
!strtotime(
$received_date
)
){

$errors++;
continue;

}


/* Check establishment */

$stmt=$conn->prepare(

"SELECT id
FROM establishments
WHERE establishment_name=?"

);

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$estCheck=
$stmt->get_result();

$stmt->close();


if(
$estCheck->num_rows==0
){

$errors++;
continue;

}


/* Duplicate check */

$stmt=$conn->prepare(

"SELECT id
FROM inward_letters
WHERE register_id=?
AND letter_no=?"

);

$stmt->bind_param(
"is",
$register_id,
$letter_no
);

$stmt->execute();

$duplicate=
$stmt->get_result();

$stmt->close();


if(
$duplicate->num_rows>0
){

$skipped++;
continue;

}


/* Insert */

$language="en";

$stmt=$conn->prepare(

"INSERT INTO inward_letters
(
register_id,
letter_no,
received_from,
department_person,
subject,
remarks,
received_date,
language,
establishment
)

VALUES
(
?,
?,
?,
?,
?,
?,
?,
?,
?
)"

);


$stmt->bind_param(

"issssssss",

$register_id,
$letter_no,
$received_from,
$department_person,
$subject,
$remarks,
$received_date,
$language,
$establishment

);


if(
$stmt->execute()
){

$success++;

}
else{

$errors++;

}

$stmt->close();

}

fclose($handle);


saveLog(
"Imported Inward Data : "
.$success
." records"
);


echo "

<script>

alert(

'Imported : $success\\n'+
'Skipped : $skipped\\n'+
'Errors : $errors'

);

location='import_inward.php';

</script>

";

}

}

}

?>

<title>Office Inward Outward Management</title>

<div class="center-page">

<div class="box">

<h2>
Import Old Inward Data (CSV)
</h2>

<form
method="post"
enctype="multipart/form-data">

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
>

<input
type="file"
name="csvfile"
accept=".csv"
required>

<br><br>

<button
name="upload"
class="save-btn">

Import Data

</button>

</form>
<br><br>

<h3>

Required CSV Format

</h3>

<table
border="1"
cellpadding="10"
width="100%"
style="
border-collapse:collapse;
text-align:center;
">

<tr style="
background:#2c3e50;
color:white;
">

<th>ID</th>
<th>Letter No</th>
<th>Received From</th>
<th>Department Person</th>
<th>Subject</th>
<th>Remarks</th>
<th>Date</th>
<th>Establishment</th>

</tr>

<tr>

<td>1</td>
<td>LET001</td>
<td>Police Office</td>
<td>John</td>
<td>Case File</td>
<td>Urgent</td>
<td>2026-01-15</td>
<td>District Court</td>

</tr>

</table>

<br>

<b>Notes:</b>

<ul>

<li>Date format supported:
YYYY-MM-DD or DD/MM/YYYY</li>

<li>Establishment name must already exist</li>

<li>Duplicate entries are skipped automatically</li>

<li>Only admin can import</li>

</ul>
</div>

</div>
