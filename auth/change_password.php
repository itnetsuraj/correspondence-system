<?php

include_once '../config/session.php';
include_once '../config/security_headers.php';

include '../header.php';
include '../config/db.php';


/* Generate CSRF token */

if(!isset($_SESSION['csrf_token'])){

$_SESSION['csrf_token']=
bin2hex(random_bytes(32));

}


/* Save password */

if(isset($_POST['save'])){


/* CSRF validation */

if(
!isset($_POST['csrf_token'])
||
$_POST['csrf_token']!=$_SESSION['csrf_token']
){

die("Invalid Request");

}


$username=$_SESSION['user'];

$oldPassword=$_POST['old_password'];

$newPassword=$_POST['new_password'];

$confirmPassword=$_POST['confirm_password'];


/* Match new password */

if($newPassword!=$confirmPassword){

echo "
<script>
alert('New Password and Confirm Password do not match');
</script>
";

}
else{


/* Get current password */

$stmt=$conn->prepare(
"SELECT password
FROM users
WHERE username=?"
);

$stmt->bind_param(
"s",
$username
);

$stmt->execute();

$result=$stmt->get_result();

$user=$result->fetch_assoc();


if(!$user){

echo "
<script>
alert('User not found');
</script>
";

}
else{


/* Verify old password */

if(
!password_verify(
$oldPassword,
$user['password']
)
){

echo "
<script>
alert('Old Password incorrect');
</script>
";

}
else{


/* Create new secure hash */

$newHash=
password_hash(
$newPassword,
PASSWORD_DEFAULT
);


/* Update */

$update=$conn->prepare(
"UPDATE users
SET password=?
WHERE username=?"
);

$update->bind_param(
"ss",
$newHash,
$username
);

$update->execute();


echo "
<script>
alert('Password changed successfully');
location='../dashboard.php';
</script>
";

}

}

}

}

?>


<div class="center-page">

<div class="box">

<h2 class="form-title">

Change Password

</h2>


<form method="post" class="modern-form">


<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>


<div class="input-group">

<label>

Old Password

</label>

<input
type="password"
name="old_password"
required>

</div>


<div class="input-group">

<label>

New Password

</label>

<input
type="password"
name="new_password"
required>

</div>


<div class="input-group full-width">

<label>

Confirm Password

</label>

<input
type="password"
name="confirm_password"
required>

</div>


<button
class="save-btn"
name="save">

Change Password

</button>

</form>

</div>

</div>
