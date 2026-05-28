<?php
include '../config/session.php';
session_start();

if(isset($_GET['lang'])){

    $allowed=['en','mr'];

    if(in_array($_GET['lang'],$allowed)){

        $_SESSION['lang']=$_GET['lang'];

    }

}

header("Location: ".$_SERVER['HTTP_REFERER']);
exit;
