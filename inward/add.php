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


include '../lang.php';
include '../config/save_log.php';

$language=$_SESSION['lang'] ?? 'en';
$role=$_SESSION['admin_role'] ?? '';

$selectedEstablishment=
$_SESSION['selected_establishment'] ?? 'ALL';

if(
$role=="admin"
&&
$selectedEstablishment=="ALL"
){

echo "

<div style='
background:#ffe6e6;
padding:20px;
margin:20px;
border-radius:10px;
border:2px solid red;
text-align:center;
font-weight:bold;
'>

Please select an Establishment first

<br><br>

<a href='../dashboard.php'
style='
background:#2c3e50;
padding:10px 20px;
color:white;
text-decoration:none;
border-radius:8px;'>

Back To Dashboard

</a>

</div>";

exit;
}

$establishment=
($role=="admin")
?
$selectedEstablishment
:
$_SESSION['establishment'];

$marathiOnly='';

if($language=="mr"){

$marathiOnly=
'oninput="this.value=this.value.replace(/[^\u0900-\u097F\s.,()-]/g,\'\')"';

}

$currentYear=date("Y");

/* Get latest register ID from inward table */

$stmt=$conn->prepare("

SELECT
COALESCE(MAX(register_id),0) AS max_id

FROM inward_letters

WHERE language=?
AND establishment=?
AND YEAR(received_date)=?

");

$stmt->bind_param(
"ssi",
$language,
$establishment,
$currentYear
);

$stmt->execute();

$data=
$stmt->get_result()
->fetch_assoc();

$maxID=
$data['max_id'] ?? 0;


/* Get changed start ID from establishment */

$stmt=$conn->prepare("

SELECT inward_start_id
FROM establishments
WHERE establishment_name=?

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$row=
$stmt->get_result()
->fetch_assoc();

$startID=
$row['inward_start_id'] ?? 1;


/* Decide next ID */

if($startID>$maxID){

    $next_id=$startID;

}
else{

    $next_id=$maxID+1;

}

if(isset($_POST['save'])){

if(
!isset($_POST['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'],
$_POST['csrf_token']
)
){

die("Invalid CSRF Token");

}

$letter_no=trim($_POST['letter_no']);
$received_date=trim($_POST['received_date']);
$received_from=trim($_POST['received_from']);
$subject=trim($_POST['subject']);
$department_person=trim($_POST['department_person']);
$quantity=(int)$_POST['quantity'];
$remarks=trim($_POST['remarks']);
$document_type=trim($_POST['document_type']);

if(
$document_type=="Others"
&&
!empty($_POST['other_document_type'])
){

$document_type=
trim($_POST['other_document_type']);

}

$conn->begin_transaction();

try{


/* Lock and regenerate latest ID before save */

$stmt=$conn->prepare("

SELECT
COALESCE(MAX(register_id),0) AS max_id

FROM inward_letters

WHERE language=?
AND establishment=?
AND YEAR(received_date)=?
FOR UPDATE

");

$stmt->bind_param(
"ssi",
$language,
$establishment,
$currentYear
);

$stmt->execute();

$data=
$stmt->get_result()
->fetch_assoc();

$maxID=
$data['max_id'] ?? 0;


/* Get establishment start ID */

$stmt=$conn->prepare("

SELECT inward_start_id
FROM establishments
WHERE establishment_name=?
FOR UPDATE

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$row=
$stmt->get_result()
->fetch_assoc();

$startID=
$row['inward_start_id'] ?? 1;


/* Generate actual next ID */

if($startID>$maxID){

$next_id=$startID;

}
else{

$next_id=$maxID+1;

}


/* Insert record */

$stmt=$conn->prepare("

INSERT INTO inward_letters(

register_id,
letter_no,
received_from,
department_person,
quantity,
subject,
document_type,
remarks,
received_date,
language,
establishment

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?

)

");

$stmt->bind_param(

"isssissssss",

$next_id,
$letter_no,
$received_from,
$department_person,
$quantity,
$subject,
$document_type,
$remarks,
$received_date,
$language,
$establishment

);

$stmt->execute();

/* Update next inward ID in establishments */

$newStartID=$next_id+1;

$stmt=$conn->prepare("

UPDATE establishments
SET inward_start_id=?
WHERE establishment_name=?

");

$stmt->bind_param(
"is",
$newStartID,
$establishment
);

$stmt->execute();

saveLog(
"Added Inward Entry - ".$letter_no
);

$conn->commit();
echo "
<script>
alert('Saved Successfully');
location='add.php';
</script>
";

exit;

}catch(Exception $e){

$conn->rollback();
die($e->getMessage());

}

}

?>

<style>

.form-title{
text-align:center;
margin-bottom:25px;
color:#2c3e50;
}

.modern-form{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:15px;
}

.full-width{
grid-column:1/span 2;
}

.input-group{
display:flex;
flex-direction:column;
}

.input-group label{
font-weight:bold;
margin-bottom:8px;
}

.input-group input,
.input-group select,
.input-group textarea{

padding:12px;
border:1px solid #ccc;
border-radius:8px;
font-size:14px;

}

.save-btn{

grid-column:1/span 2;
padding:14px;
background:#4faef5;
color:white;
border:none;
border-radius:8px;
font-size:16px;
cursor:pointer;

}

</style>

<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h1 class="form-title">

<?=($language=="mr")
?
"आवक नोंद"
:
"Inward Entry"?>

</h1>


<form method="post" class="modern-form">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token']?>">


<div class="input-group">

<label><?=($language=="mr")?"आवक क्रमांक":"Inward No"?></label>

<input value="<?= $next_id ?>" readonly>

</div>


<div class="input-group">

<label><?=($language=="mr")?"पत्र क्रमांक":"Letter No"?></label>

<input
name="letter_no"
required>

</div>


<div class="input-group">

<label><?=($language=="mr")?"दिनांक":"Date"?></label>

<input
type="date"
name="received_date"
required>

</div>


<div class="input-group">

<label><?=($language=="mr")?"कोणाकडून प्राप्त":"Received From"?></label>

<input
name="received_from"
required
<?= $marathiOnly ?>>

</div>


<div class="input-group full-width">

<label><?=($language=="mr")?"विषय":"Subject"?></label>

<input
name="subject"
required
<?= $marathiOnly ?>>

</div>


<div class="input-group">

<label><?=($language=="mr")?"विभाग / व्यक्ती":"Department / Person"?></label>

<input
name="department_person"
required
<?= $marathiOnly ?>>

</div>


<div class="input-group">

<label><?=($language=="mr")?"प्रती":"Copies"?></label>

<input
type="number"
name="quantity"
value="1"
min="1"
required>

</div>


<div class="input-group">

<label><?=($language=="mr")?"दस्तऐवज प्रकार":"Document Type"?></label>

<select
name="document_type"
id="document_type"
onchange="toggleOtherType()">

<?php

$documentTypes=[

"Notice",
"Writ",
"Confidential",
"R & P",
"Certificate",
"Order",
"Summary",
"Applications",
"Circular",
"Letters",
"Others"

];

foreach($documentTypes as $type){

?>

<option value="<?= $type ?>">

<?= $type ?>

</option>

<?php } ?>

</select>

</div>


<div
class="input-group"
id="otherTypeBox"
style="display:none;">

<label>

<?=($language=="mr")
?
"इतर प्रकार"
:
"Other Type"?>

</label>

<input
name="other_document_type"
id="other_document_type">

</div>


<div class="input-group full-width">

<label><?=($language=="mr")?"शेरा":"Remarks"?></label>

<textarea
name="remarks"
rows="3"
<?= $marathiOnly ?>></textarea>

</div>


<button
class="save-btn"
name="save">

<?=($language=="mr")
?
"जतन करा"
:
"Save"?>

</button>

</form>

</div>
</div>


<script>

function toggleOtherType(){

let value=
document.getElementById(
"document_type"
).value;

let box=
document.getElementById(
"otherTypeBox"
);

let input=
document.getElementById(
"other_document_type"
);

if(value==="Others"){

box.style.display="block";
input.required=true;

}else{

box.style.display="none";
input.required=false;
input.value="";

}

}

</script>
