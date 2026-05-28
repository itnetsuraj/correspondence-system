<?php
include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';

if($_SESSION['admin_role']!='admin'){

die("Access Denied");

}

include '../config/db.php';

if($_SESSION['admin_role']!="admin"){

die("Access denied");

}

$id=(int)$_GET['id'];

$stmt=$conn->prepare(
"DELETE FROM inward_letters WHERE id=?"
);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();
header("Location:list.php");
?>
