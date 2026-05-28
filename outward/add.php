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
$_SESSION['selected_establishment']
?? 'ALL';


/* Admin restriction */

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
font-size:18px;
font-weight:bold;
'>

Please select an Establishment before creating Outward Entry

<br><br>

<a href='../dashboard.php'
style='padding:10px 20px;
background:#2c3e50;
color:white;
text-decoration:none;
border-radius:8px;'>

Back To Dashboard

</a>

</div>

";

exit;

}


/* Establishment */

$establishment=
($role=="admin")
?
$selectedEstablishment
:
$_SESSION['establishment'];


/* Marathi restriction */

$marathiOnly='';

if($language=="mr"){

$marathiOnly='oninput="this.value=this.value.replace(/[^\u0900-\u097F0-9\s.,()-]/g,\'\')"';

}


$currentYear=(int)date("Y");


/* Get latest outward register ID */

$stmt=$conn->prepare("

SELECT
COALESCE(MAX(register_id),0) AS max_id

FROM outward_letters

WHERE establishment=?

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$data=
$stmt->get_result()
->fetch_assoc();

$maxID=
$data['max_id'] ?? 0;


/* Get changed outward start ID */

$stmt=$conn->prepare("

SELECT outward_start_id
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
$row['outward_start_id'] ?? 1;


/* Decide next ID */

if($startID>$maxID){

    $next_id=$startID;

}else{

    $next_id=$maxID+1;

}


/* SAVE */

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


$letter_no=
trim($_POST['letter_no']);

$sent_to=
trim($_POST['sent_to']);

$department_person=
trim($_POST['department_person']);

$subject=
trim($_POST['subject']);

$document_type=
trim($_POST['document_type']);

if(
$document_type=="Others"
&&
!empty($_POST['other_document_type'])
){

$document_type=
trim(
$_POST['other_document_type']
);

}

$remarks=
trim($_POST['remarks']);

$sent_date=
trim($_POST['sent_date']);

/* Validation */

$postage_amount=(int)trim($_POST['postage_amount']);

if($postage_amount < 0){

die("Invalid postage amount");

}

$conn->begin_transaction();

try{


/* Generate Register ID */

/* Lock latest outward ID */

$stmt=$conn->prepare("

SELECT
COALESCE(MAX(register_id),0) AS max_id

FROM outward_letters

WHERE establishment=?
FOR UPDATE

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$data=
$stmt->get_result()
->fetch_assoc();

$maxID=
$data['max_id'] ?? 0;


/* Lock outward start ID */

$stmt=$conn->prepare("

SELECT outward_start_id
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
$row['outward_start_id'] ?? 1;


/* Final register ID */

if($startID>$maxID){

$next_id=$startID;

}else{

$next_id=$maxID+1;

}


/* Get Balance */

$stmt=$conn->prepare("

SELECT balance
FROM outward_balance
WHERE establishment=?
FOR UPDATE

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$balanceData=
$stmt
->get_result()
->fetch_assoc();

if(!$balanceData){

throw new Exception(
"Balance record missing"
);

}

$currentBalance=
(int)$balanceData['balance'];


/* Check balance */

if(
$currentBalance
<
$postage_amount
){

throw new Exception(
"Insufficient Balance"
);

}

/* Deduct balance */

$newBalance=
$currentBalance-
$postage_amount;

/* Update balance */

$stmt=$conn->prepare("

UPDATE outward_balance
SET balance=?
WHERE establishment=?

");

$stmt->bind_param(
"is",
$newBalance,
$establishment
);

$stmt->execute();


/* Insert outward */

$stmt=$conn->prepare("

INSERT INTO outward_letters
(

register_id,
letter_no,
sent_to,
department_person,
subject,
document_type,
remarks,
sent_date,
language,
postage_amount,
establishment,
record_year

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
?,
?,
?

)

");


$stmt->bind_param(

"issssssssisi",

$next_id,
$letter_no,
$sent_to,
$department_person,
$subject,
$document_type,
$remarks,
$sent_date,
$language,
$postage_amount,
$establishment,
$currentYear

);

/* Execute insert FIRST */

$stmt->execute();


/* Update next outward start ID */

$newStartID = $next_id + 1;

$stmt2 = $conn->prepare("

UPDATE establishments
SET outward_start_id=?
WHERE establishment_name=?

");

$stmt2->bind_param(
"is",
$newStartID,
$establishment
);

$stmt2->execute();


saveLog(


"Added Outward : ".$letter_no

);


$conn->commit();

echo "

<script>

alert('Saved Successfully');

location='add.php';

</script>

";

exit;

}
catch(Exception $e){

$conn->rollback();

echo "

<script>

alert('".$e->getMessage()."');

</script>

";

}

}

?>

<title>Office Inward Outward Management</title>

<div class="center-page">

<div class="box">

<h1 class="form-title">

<?= $lang[$language]['outward_title'] ?>

</h1>

<form method="post" class="modern-form">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>

<div class="input-group">

<label>
<?= ($language=="mr") ? "जावक क्रमांक" : "Outward No" ?>
</label>

<input
value="<?= $next_id ?>"
readonly>

</div>

<div class="input-group">

<label>
<?= ($language=="mr") ? "पत्र क्रमांक" : "Letter No" ?>
</label>

<div style="
display:flex;
align-items:center;
gap:15px;
width:100%;
">

<input
type="text"
name="letter_no"
required
style="flex:1;">


</div>

</div>


<div class="input-group">

<label>
<?= ($language=="mr") ? "दिनांक" : "Date" ?>
</label>

<input
type="date"
name="sent_date"
required
max="<?= date('Y-m-d') ?>"
onclick="this.showPicker()">

</div>


<div class="input-group">

<label>
<?= ($language=="mr") ? "पाठविले" : "Sent To" ?>
</label>

<input
name="sent_to"
required
<?= $marathiOnly ?>

>

</div>


<div class="input-group full-width">

<label>
<?= ($language=="mr") ? "विषय" : "Subject" ?>
</label>

<input
name="subject"
required
<?= $marathiOnly ?>

>

</div>


<div class="input-group">

<label>
<?= ($language=="mr") ? "विभाग / व्यक्ती" : "Department / Person" ?>
</label>

<input
name="department_person"
required
<?= $marathiOnly ?>

>

</div>


<div class="input-group">

<label>
<?= ($language=="mr") ? "टपाल खर्च" : "Postage Amount" ?>
</label>

<input
type="number"
name="postage_amount"
required
min="0"
step="1"
value="0"
oninput="if(this.value<0)this.value=0;">

</div>

<div class="input-group">

<label>

<?= ($language=="mr")
?
"दस्तऐवज प्रकार"
:
"Document Type"
?>

</label>

<select
name="document_type"
id="document_type"
required
onchange="toggleOtherType()">

<option value="Notice">Notice</option>
<option value="Writing">Writ</option>
<option value="Confidential">Confidential</option>
<option value="R & P">R & P</option>
<option value="Certificate">Certificate</option>
<option value="Order">Order</option>
<option value="Summary">Summary</option>
<option value="Applications">Applications</option>
<option value="Circular">Circular</option>
<option value="Letters">Letters</option>
<option value="Others">Others</option>

</select>

</div>


<div
class="input-group"
id="otherTypeBox"
style="display:none;">

<label>

<?= ($language=="mr")
?
"इतर प्रकार"
:
"Other Type"
?>

</label>

<input
type="text"
name="other_document_type"
id="other_document_type"
placeholder="Enter custom type">

</div>

<div class="input-group full-width">

<label>
<?= ($language=="mr") ? "शेरा" : "Remarks" ?>
</label>

<textarea
name="remarks"
rows="2"
style="
height:60px;
resize:none;
"
<?= $marathiOnly ?>>

</textarea>

</div>

<button
class="save-btn"
name="save">

<?= ($language=="mr") ? "जतन करा" : "Save" ?>

</button>

</form>


<script>

function toggleOtherType(){

let docType=
document.getElementById(
'document_type'
).value;

let box=
document.getElementById(
'otherTypeBox'
);

let input=
document.getElementById(
'other_document_type'
);

if(docType=="Others"){

box.style.display='block';
input.required=true;

}else{

box.style.display='none';
input.required=false;
input.value='';

}

}

</script>
</div>

</div>

