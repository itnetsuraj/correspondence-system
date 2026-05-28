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



if($_SESSION['admin_role']!="admin"){

header("Location:../dashboard.php");
exit;

}

?>

<title>Office Inward Outward Management</title>

<div class="center-page">

<div class="box">

<h2
style="
text-align:center;
margin-bottom:30px;
">

Import Old Data

</h2>

<div
style="
display:flex;
justify-content:center;
gap:30px;
flex-wrap:wrap;
margin-top:40px;
">

<a
href="import_inward.php"
style="
text-decoration:none;
background:#2c3e50;
color:white;
padding:30px;
width:250px;
text-align:center;
border-radius:15px;
font-size:20px;
font-weight:bold;
box-shadow:0 4px 10px rgba(0,0,0,.2);
">

📥

<br><br>

Import Inward Data

</a>


<a
href="import_outward.php"
style="
text-decoration:none;
background:#27ae60;
color:white;
padding:30px;
width:250px;
text-align:center;
border-radius:15px;
font-size:20px;
font-weight:bold;
box-shadow:0 4px 10px rgba(0,0,0,.2);
">

📤

<br><br>

Import Outward Data

</a>

</div>

</div>

</div>
