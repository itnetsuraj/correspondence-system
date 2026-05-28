<?php
include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';
include '../header.php';
include '../config/db.php';

if(
!isset($_SESSION['admin_role'])
||
$_SESSION['admin_role']!="admin"
){

header("Location:../dashboard.php");
exit;

}


/* CSRF Token */

if(empty($_SESSION['csrf_token'])){

$_SESSION['csrf_token']=bin2hex(
random_bytes(32)
);

}

$success=0;
$skipped=0;
$errors=0;


if(isset($_POST['upload'])){


/* Verify CSRF */

if(
!isset($_POST['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'] ?? '',
$_POST['csrf_token']
)
){

die("CSRF Validation Failed");

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


if(count($data)<9){

$errors++;
continue;

}


/* Clean values */

$register_id=
(int)trim($data[0]);

$letter_no=
trim($data[1]);

$sent_to=
trim($data[2]);

$department_person=
trim($data[3]);

$subject=
trim($data[4]);

$remarks=
trim($data[5]);

$sent_date=
trim($data[6]);

$postage=
(float)trim($data[7]);

$establishment=
trim($data[8]);


/* Convert date */

if(
strpos(
$sent_date,
"/"
)!==false
){

$dateParts=
explode(
"/",
$sent_date
);

if(count($dateParts)==3){

$sent_date=
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
$sent_date
)
){

$errors++;
continue;

}


/* Establishment check */

$stmt=
$conn->prepare(

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

$stmt=
$conn->prepare(

"SELECT id
FROM outward_letters
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

$stmt=
$conn->prepare(

"INSERT INTO outward_letters
(
register_id,
letter_no,
sent_to,
department_person,
subject,
remarks,
sent_date,
language,
postage_amount,
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
?,
?
)"

);


$stmt->bind_param(

"isssssssds",

$register_id,
$letter_no,
$sent_to,
$department_person,
$subject,
$remarks,
$sent_date,
$language,
$postage,
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


echo "

<script>

alert(
'Imported : $success\\n'+
'Skipped Duplicate : $skipped\\n'+
'Errors : $errors'
);

location='import_outward.php';

</script>

";

}

}

}

?>

<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h2>Import Old Outward Data (CSV)</h2>

<form
method="post"
enctype="multipart/form-data">

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

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
<th>Sent To</th>
<th>Department Person</th>
<th>Subject</th>
<th>Remarks</th>
<th>Date</th>
<th>Postage</th>
<th>Establishment</th>

</tr>

<tr>

<td>1</td>
<td>OUT001</td>
<td>Police Office</td>
<td>John</td>
<td>Case File</td>
<td>Urgent</td>
<td>2026-01-15</td>
<td>50</td>
<td>District Court</td>

</tr>

</table>

<br>

<b>Notes:</b>

<ul>

<li>Date format: YYYY-MM-DD or DD/MM/YYYY</li>
<li>Establishment must already exist</li>
<li>Duplicate records are skipped</li>
<li>Postage amount is required</li>

</ul>
</div>

</div>
