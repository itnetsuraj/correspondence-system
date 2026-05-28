<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include_once '../config/auth_check.php';
include_once '../config/security_headers.php';

include '../header.php';
include '../config/db.php';
include '../lang.php';

$language=$_SESSION['lang'] ?? 'en';

$id=(int)($_GET['id'] ?? 0);

if($id<=0){

die("Invalid ID");

}

/* Fetch record */

$stmt=$conn->prepare("

SELECT *
FROM outward_letters
WHERE id=?
LIMIT 1

");

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

if(isset($_POST['update'])){

$letter_no=
trim($_POST['letter_no']);

$subject=
trim($_POST['subject']);

$sent_to=
trim($_POST['sent_to']);

$department_person=
trim($_POST['department_person']);

$postage_amount=
(float)$_POST['postage_amount'];

$document_type=
trim($_POST['document_type']);

$other_document_type=
trim($_POST['other_document_type']);

$remarks=
trim($_POST['remarks']);

$sent_date=
$_POST['sent_date'];


$stmt2=$conn->prepare("

UPDATE outward_letters

SET

letter_no=?,
subject=?,
sent_to=?,
department_person=?,
postage_amount=?,
document_type=?,
other_document_type=?,
remarks=?,
sent_date=?

WHERE id=?

");

$stmt2->bind_param(

"ssssdssssi",

$letter_no,
$subject,
$sent_to,
$department_person,
$postage_amount,
$document_type,
$other_document_type,
$remarks,
$sent_date,
$id

);

if($stmt2->execute()){

echo "

<script>

alert('Updated Successfully');

location='list.php';

</script>

";

exit;

}else{

echo "

<script>

alert('Update failed');

</script>

";

}

}

?>

<title>Edit Outward</title>

<style>

.edit-box{

width:700px;
margin:auto;
background:white;
padding:25px;
border-radius:10px;
box-shadow:0 0 10px rgba(0,0,0,.2);

}

.input-group{

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

<?= ($language=="mr")
?
"बाह्य पत्र संपादन"
:
"Edit Outward Letter"
?>

</h2>

<form method="post">


<div class="input-group">

<label>

<?= ($language=="mr")
?
"पत्र क्रमांक"
:
"Letter No"
?>

</label>

<input
type="text"
name="letter_no"
value="<?= htmlspecialchars($row['letter_no']) ?>"
required>

</div>


<div class="input-group">

<label>

<?= ($language=="mr")
?
"विषय"
:
"Subject"
?>

</label>

<textarea
name="subject"
required><?= htmlspecialchars($row['subject']) ?></textarea>

</div>


<div class="input-group">

<label>

<?= ($language=="mr")
?
"पाठविले"
:
"Sent To"
?>

</label>

<input
type="text"
name="sent_to"
value="<?= htmlspecialchars($row['sent_to']) ?>"
required>

</div>


<div class="input-group">

<label>

<?= ($language=="mr")
?
"विभाग / व्यक्ती"
:
"Department Person"
?>

</label>

<input
type="text"
name="department_person"
value="<?= htmlspecialchars($row['department_person']) ?>">

</div>


<div class="input-group">

<label>

<?= ($language=="mr")
?
"टपाल खर्च"
:
"Postage"
?>

</label>

<input
type="number"
step="0.01"
name="postage_amount"
value="<?= htmlspecialchars($row['postage_amount']) ?>">

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
class="input-group"
id="otherTypeDiv"
style="<?=($row['document_type']=="Others")?'':'display:none;'?>">

<label>

<?= ($language=="mr")
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


<div class="input-group">

<label>

<?= ($language=="mr")
?
"दिनांक"
:
"Date"
?>

</label>

<input
type="date"
name="sent_date"
value="<?= htmlspecialchars($row['sent_date']) ?>">

</div>


<div class="input-group">

<label>

<?= ($language=="mr")
?
"शेरा"
:
"Remarks"
?>

</label>

<textarea
name="remarks"><?= htmlspecialchars($row['remarks']) ?></textarea>

</div>


<button
type="submit"
name="update">

<?= ($language=="mr")
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

}
else{

div.style.display="none";

}

}

</script>
