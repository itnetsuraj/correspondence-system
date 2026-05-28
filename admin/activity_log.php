<?php
include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';
include '../header.php';
include '../config/db.php';


/* Selected user filter */

$selectedUser=$_GET['username'] ?? '';


/* Get users list for dropdown */

$userQuery=$conn->query("
SELECT DISTINCT username
FROM activity_logs
WHERE username IS NOT NULL
AND username!=''
ORDER BY username ASC
");


/* Main query */

$sql="
SELECT *
FROM activity_logs
WHERE 1=1
";

if(!empty($selectedUser)){

$username=
mysqli_real_escape_string(
$conn,
$selectedUser
);

$sql.="
AND username='$username'
";

}

$sql.="
ORDER BY log_time DESC
";

$result=$conn->query($sql);

?>

<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h1 class="form-title">

System Activity Log

</h1>


<!-- Filter -->

<form
method="GET"
style="
display:flex;
gap:10px;
align-items:center;
margin-bottom:20px;
flex-wrap:wrap;
">

<label>

<b>User :</b>

</label>

<select
name="username"
style="
padding:8px;
border-radius:5px;
min-width:200px;
">

<option value="">

All Users

</option>

<?php

while($u=$userQuery->fetch_assoc()){

$selected=
(
$selectedUser==$u['username']
)
?
"selected"
:
"";

?>

<option
value="<?= htmlspecialchars($u['username']) ?>"
<?= $selected ?>

>

<?= htmlspecialchars($u['username']) ?>

</option>

<?php } ?>

</select>


<button
type="submit"
style="
padding:10px;
background:#2c3e50;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
">

Filter

</button>


<a href="activity_log.php">

<button
type="button"
style="
padding:10px;
background:red;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
">

Reset

</button>

</a>

</form>


<table>

<tr>

<th>ID</th>
<th>User</th>
<th>Role</th>
<th>Establishment</th>
<th>Activity</th>
<th>Date & Time</th>

</tr>

<?php

if($result && $result->num_rows>0){

while($row=$result->fetch_assoc()){

$username=
!empty($row['username'])
?
htmlspecialchars($row['username'])
:
"Unknown User";

$role=
!empty($row['role'])
?
htmlspecialchars($row['role'])
:
"Unknown Role";

$establishment=
!empty($row['establishment'])
?
htmlspecialchars($row['establishment'])
:
"Unknown Establishment";

$activity=
!empty($row['activity'])
?
htmlspecialchars($row['activity'])
:
"-";

$logtime=
!empty($row['log_time'])
?
date(
"d-m-Y h:i A",
strtotime($row['log_time'])
)
:
"-";

?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= $username ?></td>

<td><?= $role ?></td>

<td><?= $establishment ?></td>

<td><?= $activity ?></td>

<td><?= $logtime ?></td>

</tr>

<?php

}

}else{

?>

<tr>

<td
colspan="6"
style="
text-align:center;
font-weight:bold;
">

No activity found

</td>

</tr>

<?php } ?>

</table>

</div>

</div>
