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

$language=$_SESSION['lang'] ?? 'en';

$establishment=
($_SESSION['admin_role']=="admin")
?
($_SESSION['selected_establishment'] ?? 'ALL')
:
$_SESSION['establishment'];


/* Stop if ALL Establishments selected */

if(
$_SESSION['admin_role']=="admin"
&&
(
$establishment=="ALL"
||
$establishment=="All Establishments"
)
){

echo "

<div style='
background:#ffe6e6;
padding:20px;
margin:20px auto;
width:700px;
border-radius:10px;
border:2px solid red;
text-align:center;
font-size:18px;
font-weight:bold;
'>";

echo ($language=="mr")
?
"कृपया प्रथम कार्यालय निवडा"
:
"Please Select Establishment First";

echo "

<br><br>

<a
href='../dashboard.php'
style='
padding:10px 20px;
background:#2c3e50;
color:white;
text-decoration:none;
border-radius:8px;
'>";

echo ($language=="mr")
?
"डॅशबोर्ड"
:
"Dashboard";

echo "

</a>
</div>";

exit;

}


/* SAVE */

if(isset($_POST['save'])){

$new_id=(int)$_POST['register_id'];

$update=$conn->prepare("

UPDATE establishments
SET outward_start_id=?
WHERE establishment_name=?

");

$update->bind_param(
"is",
$new_id,
$establishment
);

$update->execute();

echo "

<script>

alert('Updated Successfully');

location='change_outward_id.php';

</script>

";

exit;

}



/* FETCH CURRENT REGISTER ID */

$get=$conn->prepare("

SELECT
MAX(register_id) AS max_id

FROM outward_letters

WHERE establishment=?
AND language=?

");

$get->bind_param(
"ss",
$establishment,
$language
);

$get->execute();

$data=
$get->get_result()
->fetch_assoc();

$maxID=
$data['max_id'] ?? 0;


/* Existing outward entries */

if($maxID>0){

$currentID=$maxID;

}

/* No entries → use starting ID */

else{

$fallback=$conn->prepare("

SELECT outward_start_id
FROM establishments
WHERE establishment_name=?

");

$fallback->bind_param(
"s",
$establishment
);

$fallback->execute();

$row=
$fallback->get_result()
->fetch_assoc();

$currentID=
($row['outward_start_id'] ?? 1)-1;

}

?>

<title>

<?=($language=="mr")
?
"जावक आयडी बदला"
:
"Change Outward ID"?>

</title>


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
margin-bottom:20px;
}

label{
font-weight:bold;
display:block;
margin-bottom:8px;
font-size:16px;
}

input{
width:100%;
padding:12px;
border:1px solid #ccc;
border-radius:6px;
font-size:15px;
}

.info-box{
background:#f8f9fa;
padding:12px;
border-left:5px solid #2c3e50;
margin-bottom:20px;
border-radius:6px;
}

button{
padding:12px 25px;
background:#2c3e50;
color:white;
border:none;
border-radius:6px;
cursor:pointer;
}

</style>


<div class="edit-box">

<h2>

<?=($language=="mr")
?
"जावक नोंद आयडी बदला"
:
"Change Outward Register ID"?>

</h2>


<div class="info-box">

<?php if($language=="mr"){ ?>

सध्याचा जावक आयडी:

<b><?= $currentID ?></b>

<br><br>

जर फिजिकल नोंद 2500 पर्यंत असेल
तर नवीन क्रमांक 2501 द्या.

<?php } else { ?>

Current Outward Register ID:

<b><?= $currentID ?></b>

<br><br>

If physical entries exist till 2500,
enter 2501.

<?php } ?>

</div>

<title>Office Inward Outward Management</title>

<form method="post">

<div class="input-group">

<label>

<?=($language=="mr")
?
"नवीन आयडी"
:
"New Register ID will be"?>

</label>

<input
type="number"
name="register_id"
value="<?= $currentID+1 ?>"
required
min="1">

</div>

<button
type="submit"
name="save">

<?=($language=="mr")
?
"अद्यतन करा"
:
"Update"?>

</button>

</form>

</div>
