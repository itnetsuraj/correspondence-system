<?php

declare(strict_types=1);

if (
    !isset($_SESSION['user'])
    ||
    empty($_SESSION['user'])
) {

    header(
        'Location: /correspondence-system/auth/login.php'
    );

    exit;
}
