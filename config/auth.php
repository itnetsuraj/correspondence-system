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



if(
!isset($_SESSION['user'])
||
empty($_SESSION['user'])
){

header(
"Location:/correspondence-system/auth/login.php"
);

exit;

}
