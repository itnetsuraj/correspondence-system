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


/* Admin only */

if($_SESSION['admin_role']!="admin"){

die("Access Denied");

}

$language=$_SESSION['lang'] ?? 'en';

$id=(int)($_GET['id'] ?? 0);

if($id<=0){

die("Invalid ID");

}


/* Fetch record */

$stmt=$conn->prepare(

"SELECT *
FROM inward_letters
WHERE id=?"

);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

die("Record not found");

}

$row=$result->fetch_assoc();


/* Update */

/* Update */

if($_SERVER['REQUEST_METHOD']=="POST"){

$letter_no=trim($_POST['letter_no']);

$received_date=trim($_POST['received_date']);

$subject=trim($_POST['subject']);

$received_from=trim($_POST['received_from']);

$department_person=trim($_POST['department_person']);

$quantity=(int)$_POST['quantity'];

$document_type=trim($_POST['document_type']);

$other_document_type=trim($_POST['other_document_type']);

$remarks=trim($_POST['remarks']);


$sql="

UPDATE inward_letters

SET

letter_no=?,
received_date=?,
subject=?,
received_from=?,
department_person=?,
quantity=?,
document_type=?,
other_document_type=?,
remarks=?

WHERE id=?

";

$update=$conn->prepare($sql);

if($update===false){

die(
"Prepare Error : ".
$conn->error
);

}

$update->bind_param(

"sssssisssi",

$letter_no,
$received_date,
$subject,
$received_from,
$department_person,
$quantity,
$document_type,
$other_document_type,
$remarks,
$id

);

if($update->execute()){

echo "

<script>

alert('Updated Successfully');

window.location='list.php';

</script>

";

exit;

}else{

die(
"Execute Error : ".
$update->error
);

}

echo "

<script>

alert('Updated Successfully');

window.location='list.php';

</script>

";

exit;

}

?>

<style>

.edit-box{

width:700px;
margin:auto;
background:white;
padding:25px;
border-radius:10px;
box-shadow:0 0 10px rgba(0,0,0,.2);

}

.form-group{

margin-bottom:15px;

}

label{

font-weight:bold;
display:block;
margin-bottom:5px;

}

input,
textarea,
select{

width:100%;
padding:10px;
border:1px solid #ccc;
border-radius:5px;

}

button{

padding:12px 25px;
background:#2c3e50;
color:white;
border:none;
border-radius:5px;
cursor:pointer;

}

</style>


<div class="edit-box">

<h2>

<?=($language=="mr")
?
"आवक पत्र संपादन"
:
"Edit Inward Letter"
?>

</h2>


<form method="POST">

<div class="form-group">

<label>Letter No</label>

<input
type="text"
name="letter_no"
value="<?= htmlspecialchars($row['letter_no']) ?>"
required>

</div>


<div class="form-group">

<label>Date</label>

<input
type="date"
name="received_date"
value="<?= $row['received_date'] ?>"
required>

</div>


<div class="form-group">

<label>Subject</label>

<input
type="text"
name="subject"
value="<?= htmlspecialchars($row['subject']) ?>"
required>

</div>


<div class="form-group">

<label>Received From</label>

<input
type="text"
name="received_from"
value="<?= htmlspecialchars($row['received_from']) ?>">

</div>


<div class="form-group">

<label>Department Person</label>

<input
type="text"
name="department_person"
value="<?= htmlspecialchars($row['department_person']) ?>">

</div>


<div class="form-group">

<label>Copies</label>

<input
type="number"
name="quantity"
min="1"
value="<?= $row['quantity'] ?>">

</div>


<div class="form-group">

<label>

<?=($language=="mr")
?
"दस्तऐवज प्रकार"
:
"Document Type"
?>

</label>

<select
name="document_type"
id="document_type"
onchange="toggleOtherType()"
required>

<?php

$types=[

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

foreach($types as $type){

?>

<option
value="<?= $type ?>"
<?=($row['document_type']==$type)?'selected':''?>>

<?= $type ?>

</option>

<?php } ?>

</select>

</div>


<div
class="form-group"
id="otherTypeDiv"
style="<?=($row['document_type']=="Others")?'':'display:none;'?>">

<label>

<?=($language=="mr")
?
"इतर प्रकार"
:
"Other Document Type"
?>

</label>

<input
type="text"
name="other_document_type"
value="<?= htmlspecialchars($row['other_document_type']) ?>">

</div>


<div class="form-group">

<label>Remarks</label>

<textarea
name="remarks"><?= htmlspecialchars($row['remarks']) ?></textarea>

</div>


<button type="submit">

<?=($language=="mr")
?
"अद्यतन करा"
:
"Update"
?>

</button>

</form>

</div>


<script>

function toggleOtherType(){

let type=
document.getElementById(
'document_type'
).value;

let div=
document.getElementById(
'otherTypeDiv'
);

if(type==="Others"){

div.style.display="block";

}else{

div.style.display="none";

}

}

</script>
