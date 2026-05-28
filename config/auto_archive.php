<?php

if(!isset($conn)){
return;
}

$currentYear=date("Y");

$previousYear=
$currentYear-1;


/* prevent duplicate archive process */

$check=$conn->query("
SELECT id
FROM archive_log
WHERE archived_year='$previousYear'
");


if(
$check
&&
$check->num_rows==0
){

/* inward archive */

$conn->query("
INSERT INTO inward_archive
SELECT *
FROM inward_letters
WHERE YEAR(received_date)='$previousYear'
");

$conn->query("
DELETE
FROM inward_letters
WHERE YEAR(received_date)='$previousYear'
");


/* outward archive */

$conn->query("
INSERT INTO outward_archive
SELECT *
FROM outward_letters
WHERE YEAR(sent_date)='$previousYear'
");

$conn->query("
DELETE
FROM outward_letters
WHERE YEAR(sent_date)='$previousYear'
");


/* log archive year */

$conn->query("
INSERT INTO archive_log
(
archived_year
)
VALUES
(
'$previousYear'
)
");

}

?>
