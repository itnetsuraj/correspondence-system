<?php

/* Prevent duplicate headers */

if(headers_sent()){
    return;
}

/* Remove server technology disclosure */

header_remove("X-Powered-By");

/* Detect HTTPS */

$isHttps=
(
!empty($_SERVER['HTTPS'])
&&
$_SERVER['HTTPS']!=='off'
);


/* Security Headers */

header(
"X-Frame-Options: SAMEORIGIN"
);

header(
"X-Content-Type-Options: nosniff"
);

header(
"X-XSS-Protection: 1; mode=block"
);

header(
"Referrer-Policy: strict-origin-when-cross-origin"
);

header(
"Permissions-Policy: geolocation=(), microphone=(), camera=()"
);


/* HSTS only for HTTPS */

if($isHttps){

header(
"Strict-Transport-Security: max-age=31536000; includeSubDomains"
);

}


/* Content Security Policy */

header(

"Content-Security-Policy: ".
"default-src 'self'; ".
"script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline'; ".
"style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline'; ".
"font-src 'self' https://cdnjs.cloudflare.com data:; ".
"img-src 'self' data:; ".
"connect-src 'self'; ".
"object-src 'none'; ".
"media-src 'self'; ".
"frame-src 'self'; ".
"worker-src 'self'; ".
"frame-ancestors 'self'; ".
"base-uri 'self'; ".
"form-action 'self'; ".
"upgrade-insecure-requests; ".
"block-all-mixed-content;"
);

?>
