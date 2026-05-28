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
