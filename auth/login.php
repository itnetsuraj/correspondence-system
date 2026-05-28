<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include_once '../config/security_headers.php';
include '../config/db.php';
include '../config/save_log.php';


/* Generate CSRF token */

if(empty($_SESSION['csrf_token'])){

$_SESSION['csrf_token']=
bin2hex(random_bytes(32));

}


/* Generate captcha */

if(!isset($_SESSION['captcha'])){

$_SESSION['captcha']=rand(1000,9999);

}


/* Login */

if(isset($_POST['login'])){


/* CSRF validation */

if(
!isset($_POST['csrf_token'])
||
$_POST['csrf_token']!=$_SESSION['csrf_token']
){

die("Invalid Request (CSRF Failed)");

}


$username=trim($_POST['username']);

$password=$_POST['password'];

$captcha=$_POST['captcha'];


/* Verify captcha */

if($captcha!=$_SESSION['captcha']){

echo "
<script>
alert('Invalid Captcha');
</script>
";

$_SESSION['captcha']=rand(1000,9999);

}

else{


$stmt=$conn->prepare(
"SELECT * FROM users WHERE username=?"
);

$stmt->bind_param(
"s",
$username
);

$stmt->execute();

$res=$stmt->get_result();


if($res->num_rows>0){

$row=$res->fetch_assoc();


if(
password_verify(
$password,
$row['password']
)
){

session_regenerate_id(true);


/* Session values */

$_SESSION['user']=$row['username'];

$_SESSION['admin_role']=$row['admin_role'];

$_SESSION['establishment']=$row['establishment'];

$_SESSION['selected_establishment']="ALL";


/* Last login */

$loginTime=date("Y-m-d H:i:s");

$updateLogin=
$conn->prepare(
"UPDATE users
SET last_login=?
WHERE username=?"
);

$updateLogin->bind_param(
"ss",
$loginTime,
$username
);

$updateLogin->execute();

$_SESSION['last_login']=$loginTime;


/* Save log */

saveLog('User Login');


/* Refresh captcha */

unset($_SESSION['captcha']);

$_SESSION['csrf_token']=
bin2hex(random_bytes(32));


header(
"Location: ../dashboard.php"
);

exit;

}

else{

echo "
<script>
alert('Invalid Password');
</script>
";

$_SESSION['captcha']=rand(1000,9999);

}

}

else{

echo "
<script>
alert('User not found');
</script>
";

$_SESSION['captcha']=rand(1000,9999);

}

}

}

?>

<style>

body{
margin:0;
padding-top:15px;
font-family:Arial;
background:#eaeaea;
display:flex;
flex-direction:column;
align-items:center;
}

.logo-box{
margin-bottom:10px;
}

.logo{
width:300px;
height:auto;
}

.main-title{
font-size:32px;
font-weight:bold;
color:#2c3e50;
margin:5px 0;
}

.heading{
font-size:24px;
font-weight:bold;
color:#2c3e50;
margin:5px 0 20px 0;
}

.box{
padding:30px;
border:3px solid #2c3e50;
border-radius:10px;
background:white;
width:320px;
text-align:center;
box-shadow:0px 4px 10px rgba(0,0,0,.2);
}

input{
width:90%;
padding:10px;
border:1px solid #ccc;
border-radius:5px;
}

.captcha-box{
background:#2c3e50;
color:white;
font-size:25px;
font-weight:bold;
letter-spacing:5px;
padding:10px;
margin:10px auto;
width:120px;
border-radius:8px;
}

button{
padding:10px;
background:#2c3e50;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
width:100%;
}

button:hover{
background:#34495e;
}

.footer{
margin-top:30px;
text-align:center;
font-size:13px;
}

</style>

<?php
if(isset($_GET['expired'])){
?>

<div style="
background:#f8d7da;
color:#721c24;
padding:12px;
border-radius:8px;
margin-bottom:15px;
">

Session expired due to inactivity.
Please login again.

</div>

<?php
}
?>
<title>Login | Office Inward Outward Management</title>

<body>

<div class="logo-box">

<img
src="../images/logo.png"
class="logo"
alt="Logo">

</div>

<h1 class="main-title">
District and Sessions Court
</h1>

<div class="heading">

</div>

<div class="box">

<h2>Login</h2>

<form method="post">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>

<input
name="username"
placeholder="Username"
required>

<br><br>

<input
type="password"
name="password"
placeholder="Password"
required>

<br><br>

<div class="captcha-box">

<?= $_SESSION['captcha'] ?>

</div>

<input
name="captcha"
placeholder="Enter Captcha"
required>

<br><br>

<button
name="login">

Login

</button>

</form>

</div>

<div class="footer">

<br>

© <?= date("Y") ?>

District and Sessions Court, Hingoli

<br><br>

Created & Maintained By:
<b>Vishnu Gadekar / Computer Section</b>

<br><br>

Last Updated:
<b><?= date("d-m-Y") ?></b>

</div>

</body>
