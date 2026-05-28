<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include_once '../config/auth_check.php';
include_once '../config/security_headers.php';

include '../header.php';
include '../config/db.php';
include '../lang.php';
include '../config/save_log.php';

$language=$_SESSION['lang'] ?? 'en';

$establishment=
($_SESSION['admin_role'] ?? '')=="admin"
?
($_SESSION['selected_establishment'] ?? '')
:
($_SESSION['establishment'] ?? '');

if(empty($establishment)){
die("Establishment missing");
}

if(!isset($_SESSION['csrf_token'])){
$_SESSION['csrf_token']=bin2hex(random_bytes(32));
}

$title=
($language=="mr")
?
"पाठवणी"
:
"Dispatch";

if(isset($_POST['save'])){

if(
!isset($_POST['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'],
$_POST['csrf_token']
)
){
die("Invalid CSRF");
}

$inward_id=
trim($_POST['inward_id']);

$dispatch_quantity=
(int)$_POST['dispatch_quantity'];

if(
empty($inward_id)
||
$dispatch_quantity<=0
){
die("Invalid data");
}

$conn->begin_transaction();

try{

$stmt=$conn->prepare("

SELECT
register_id,
letter_no,
quantity

FROM inward_letters

WHERE
TRIM(register_id)=TRIM(?)
AND language=?
AND establishment=?

FOR UPDATE

");

$stmt->bind_param(
"sss",
$inward_id,
$language,
$establishment
);

$stmt->execute();

$row=
$stmt
->get_result()
->fetch_assoc();

if(!$row){

throw new Exception(
"Inward not found"
);

}

$letter_no=
$row['letter_no'];

$total=
(int)$row['quantity'];


/* already dispatched */

$stmt2=$conn->prepare("

SELECT
COALESCE(
SUM(dispatch_qty),
0
)
AS dispatched

FROM dispatch

WHERE inward_id=?

FOR UPDATE

");

$stmt2->bind_param(
"s",
$inward_id
);

$stmt2->execute();

$data=
$stmt2
->get_result()
->fetch_assoc();

$already=
(int)$data['dispatched'];

$pending=
$total-$already;

if($dispatch_quantity>$pending){

throw new Exception(
"Quantity exceeds pending"
);

}

$stmt3=$conn->prepare("

INSERT INTO dispatch(

inward_id,
letter_no,
dispatch_qty,
dispatch_date,
establishment,
language

)

VALUES(

?,
?,
?,
CURDATE(),
?,
?

)

");

$stmt3->bind_param(

"ssiss",

$inward_id,
$letter_no,
$dispatch_quantity,
$establishment,
$language

);

$stmt3->execute();

saveLog(
"Dispatch : ".$letter_no
);

$conn->commit();

echo "

<script>

alert('Saved');

location='add.php';

</script>

";

exit;

}
catch(Exception $e){

$conn->rollback();

echo "<script>alert('".$e->getMessage()."')</script>";

}

}

?>

<title>Dispatch</title>

<div class="center-page">

<div class="box">

<h1 class="form-title">

<?= htmlspecialchars($title ?? '') ?>

</h1>

<form method="post" class="modern-form">

<input
type="hidden"
name="csrf_token"
value="<?=htmlspecialchars($_SESSION['csrf_token'])?>"
>

<div class="input-group">

<label>

<?=($language=="mr")
?
"आवक क्रमांक"
:
"Inward ID"?>

</label>

<input
type="text"
name="inward_id"
id="inward_id"
required>

</div>


<div class="input-group">

<label>

<?=($language=="mr")
?
"पत्र क्रमांक"
:
"Letter No"?>

</label>

<input
type="text"
id="letter_no"
readonly>

</div>


<div
class="input-group full-width"
id="dispatchInfo"
style="display:none;">

<div style="padding:15px;background:#f5f5f5;border-radius:10px;">

<div>

<?=($language=="mr")
?
"एकूण प्रती"
:
"Total Copies"?>

:

<span id="total">

0

</span>

</div>

<br>

<div>

<?=($language=="mr")
?
"आधी पाठवलेल्या"
:
"Already Dispatched"?>

:

<span id="already">

0

</span>

</div>

<br>

<div>

<?=($language=="mr")
?
"बाकी"
:
"Pending"?>

:

<span id="pending">

0

</span>

</div>

</div>

</div>


<div class="input-group">

<label>

<?=($language=="mr")
?
"पाठवणी प्रमाण"
:
"Dispatch Quantity"?>

</label>

<input
type="number"
name="dispatch_quantity"
id="dispatch_quantity"
value="1"
min="1"
required>

</div>

<button
class="save-btn"
type="submit"
name="save">

<?= ($language=="mr")
? "पाठवणी जतन करा"
: "Save Dispatch" ?>

</button>

</form>

</div>

</div>
<script>

document
.getElementById('inward_id')
.addEventListener(
'blur',
function(){

let inwardID=
this.value.trim();

if(inwardID==""){
return;
}

fetch(
'check_dispatch.php?inward_id='
+
encodeURIComponent(inwardID)
)

.then(
response=>response.json()
)

.then(data=>{

if(data.error){

alert(data.error);

document.getElementById(
'letter_no'
).value='';

document.getElementById(
'total'
).innerText='0';

document.getElementById(
'already'
).innerText='0';

document.getElementById(
'pending'
).innerText='0';

return;

}

document.getElementById(
'letter_no'
).value=
data.letter_no;

document.getElementById(
'total'
).innerText=
data.total;

document.getElementById(
'already'
).innerText=
data.dispatched;

document.getElementById(
'pending'
).innerText=
data.pending;

document.getElementById(
'dispatch_quantity'
).max=
data.pending;

document.getElementById(
'dispatchInfo'
).style.display=
'block';

})

.catch(error=>{

console.log(error);

alert("Error loading data");

});

});

</script>
