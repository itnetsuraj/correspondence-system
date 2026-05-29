<?php
include '../config/session.php';
session_start();

if(isset($_GET['lang'])){

    $allowed=['en','mr'];

    if(in_array($_GET['lang'],$allowed)){

        $_SESSION['lang']=$_GET['lang'];

    }

}

// SECURITY FIX: Validate referer URL to prevent open redirect attacks
$referer = $_SERVER['HTTP_REFERER'] ?? '/correspondence-system/dashboard.php';

// Only allow redirects to same domain
$allowed_hosts = [
    $_SERVER['HTTP_HOST'],
    'localhost',
    'localhost:8000'
];

$parsed_url = parse_url($referer);
$referer_host = $parsed_url['host'] ?? '';

if (!in_array($referer_host, $allowed_hosts, true)) {
    // Fallback to dashboard if referer is from different domain
    $referer = '/correspondence-system/dashboard.php';
}

// Additional validation: ensure the path is safe
if (empty($referer) || $referer === '') {
    $referer = '/correspondence-system/dashboard.php';
}

header("Location: " . htmlspecialchars($referer, ENT_QUOTES, 'UTF-8'));
exit;
