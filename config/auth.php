<?php

include_once __DIR__.'/session.php';

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
