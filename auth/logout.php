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



/* Allow only POST logout */

if($_SERVER['REQUEST_METHOD']!=="POST"){

header(
"Location: login.php"
);

exit;

}


/* Validate CSRF */

if(
!isset($_POST['csrf_token'])
||
!isset($_SESSION['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'],
$_POST['csrf_token']
)
){

session_unset();

session_destroy();

header(
"Location: login.php"
);

exit;

}


/* Destroy session */

$_SESSION=[];

if(
ini_get("session.use_cookies")
){

$params=session_get_cookie_params();

setcookie(

session_name(),

'',

time()-42000,

$params["path"],
$params["domain"],
$params["secure"],
$params["httponly"]

);

}

session_destroy();


header(
"Location: login.php"
);

exit;

?>
