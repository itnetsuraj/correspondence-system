<?php
include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';
include '../header.php';
include '../config/db.php';
include '../config/save_log.php';

if(
!isset($_SESSION['admin_role'])
||
$_SESSION['admin_role']!="admin"
){

header("Location: ../dashboard.php");
exit;

}


/* Add Establishment */

if(isset($_POST['add'])){


/* CSRF */

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


$name=
trim($_POST['name']);

if(!empty($name)){


/* Duplicate check */

$stmt=
$conn->prepare("

SELECT id
FROM establishments
WHERE LOWER(TRIM(establishment_name))
=
LOWER(TRIM(?))

");

$stmt->bind_param(
"s",
$name
);

$stmt->execute();

$check=
$stmt->get_result();


if(
$check->num_rows==0
){

$insert=
$conn->prepare("

INSERT INTO establishments
(
establishment_name
)

VALUES
(?)

");

$insert->bind_param(
"s",
$name
);

$insert->execute();


saveLog(
"Added Establishment : ".$name
);


echo "

<script>

alert(
'Establishment Added Successfully'
);

location='establishments.php';

</script>

";

exit;

}
else{

echo "

<script>

alert(
'Establishment already exists'
);

</script>

";

}

}

}


/* Delete */

if(isset($_POST['delete'])){


/* CSRF */

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


$id=
(int)$_POST['delete'];


/* Get establishment name */

$get=
$conn->prepare("
SELECT establishment_name
FROM establishments
WHERE id=?
");

$get->bind_param(
"i",
$id
);

$get->execute();

$res=
$get->get_result();

$data=
$res->fetch_assoc();

$name=
$data['establishment_name']
?? 'Unknown';


/* Delete */

$stmt=
$conn->prepare("

DELETE
FROM establishments
WHERE id=?

");

$stmt->bind_param(
"i",
$id
);

$stmt->execute();


saveLog(
"Deleted Establishment : ".$name
);


echo "

<script>

alert(
'Establishment Deleted Successfully'
);

location='establishments.php';

</script>

";

exit;

}


/* Load */

$result=
$conn->query("

SELECT *
FROM establishments
ORDER BY establishment_name ASC

");

?>

<div class="center-page">

<div class="box">

<h2>

Manage Establishments

</h2>

<title>Office Inward Outward Management</title>

<form method="post">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>

<input
type="text"
name="name"
placeholder="Enter Establishment Name"
required
style="
padding:10px;
width:300px;
border-radius:8px;
border:1px solid #ccc;
">

<button
type="submit"
name="add"
style="
padding:10px 20px;
background:#27ae60;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
">

Add Establishment

</button>

</form>

<br><br>


<table
border="1"
cellpadding="10"
width="100%"
style="
border-collapse:collapse;
text-align:center;
">

<tr
style="
background:#2c3e50;
color:white;
">

<th>ID</th>
<th>Establishment Name</th>
<th>Action</th>

</tr>


<?php

while(
$row=
$result->fetch_assoc()
){

?>

<tr>

<td>

<?= $row['id'] ?>

</td>

<td>

<?= htmlspecialchars(
$row['establishment_name']
) ?>

</td>

<td>

<form
method="post"
style="display:inline;">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>

<button
name="delete"
value="<?= $row['id'] ?>"
onclick="
return confirm(
'Delete this establishment?'
)
"
style="
background:red;
color:white;
border:none;
padding:8px;
border-radius:5px;
cursor:pointer;
">

Delete

</button>

</form>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>
