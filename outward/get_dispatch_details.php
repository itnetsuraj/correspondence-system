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



$letter_no=$_GET['letter_no'];

$sql="
SELECT copies
FROM inward_letters
WHERE letter_no='$letter_no'
LIMIT 1
";

$result=
$conn->query($sql);

$row=
$result->fetch_assoc();

$total=
(int)$row['copies'];

$sql2="
SELECT COUNT(*) c
FROM outward_letters
WHERE letter_no='$letter_no'
AND dispatch='1'
";

$r2=
$conn->query($sql2);

$dispatch=
$r2->fetch_assoc()['c'];

$pending=
$total-$dispatch;

echo json_encode([

'total'=>$total,
'dispatched'=>$dispatch,
'pending'=>$pending

]);
