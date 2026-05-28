<?php

/* Start secure session only once */

if(session_status()!==PHP_SESSION_ACTIVE){

$isHttps=

(
!empty($_SERVER['HTTPS'])
&&
$_SERVER['HTTPS']!=='off'
);


/* Session security */

ini_set(
'session.use_only_cookies',
'1'
);

ini_set(
'session.use_strict_mode',
'1'
);

ini_set(
'session.cookie_httponly',
'1'
);

ini_set(
'session.cookie_secure',
$isHttps ? '1':'0'
);


/* Cookie settings */

session_set_cookie_params([

'lifetime'=>0,

'path'=>'/',

'domain'=>'',

'secure'=>$isHttps,

'httponly'=>true,

'samesite'=>'Strict'

]);

session_start();

}


/* Prevent session fixation */

if(empty($_SESSION['initiated'])){

session_regenerate_id(true);

$_SESSION['initiated']=true;

}


/* Auto logout after 10 minutes */

$timeout=600;

if(

isset($_SESSION['LAST_ACTIVITY'])

&&

(time()-$_SESSION['LAST_ACTIVITY'])>$timeout

){

$_SESSION=[];


/* Remove session cookie */

if(ini_get('session.use_cookies')){

$params=
session_get_cookie_params();

setcookie(

session_name(),

'',

[

'expires'=>time()-42000,

'path'=>$params['path'],

'domain'=>$params['domain'],

'secure'=>$params['secure'],

'httponly'=>$params['httponly'],

'samesite'=>'Strict'

]

);

}

session_destroy();

header(
"Location:/correspondence-system/auth/login.php?expired=1"
);

exit;

}


/* Update activity time */

$_SESSION['LAST_ACTIVITY']=time();

?>
