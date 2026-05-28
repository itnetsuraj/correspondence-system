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



/* Archive inward records */

$conn->query("
INSERT INTO inward_archive
SELECT *
FROM inward_letters i
WHERE YEAR(i.received_date) < YEAR(CURDATE())
AND NOT EXISTS
(
SELECT 1
FROM inward_archive a
WHERE a.register_id=i.register_id
)
");


/* Remove archived inward from main table */

$conn->query("
DELETE
FROM inward_letters
WHERE YEAR(received_date) < YEAR(CURDATE())
");


/* Archive outward records */

$conn->query("
INSERT INTO outward_archive
SELECT *
FROM outward_letters o
WHERE YEAR(o.sent_date) < YEAR(CURDATE())
AND NOT EXISTS
(
SELECT 1
FROM outward_archive a
WHERE a.register_id=o.register_id
)
");


/* Remove archived outward from main table */

$conn->query("
DELETE
FROM outward_letters
WHERE YEAR(sent_date) < YEAR(CURDATE())
");


echo "
<script>

alert('Archive completed successfully');

location='../dashboard.php';

</script>
";

?>
