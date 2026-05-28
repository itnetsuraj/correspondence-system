<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include '../config/db.php';

header('Content-Type: application/json');

$letter_no=$_GET['letter_no'] ?? '';

if(empty($letter_no)){

echo json_encode([
'error'=>'Letter number missing'
]);

exit;

}

$stmt=$conn->prepare("
SELECT
letter_no,
quantity
FROM inward_letters
WHERE letter_no=?
LIMIT 1
");

$stmt->bind_param(
"s",
$letter_no
);

$stmt->execute();

$row=
$stmt->get_result()->fetch_assoc();

if(!$row){

echo json_encode([
'error'=>'Letter not found'
]);

exit;

}

$total=(int)$row['quantity'];

$stmt2=$conn->prepare("
SELECT
COALESCE(
SUM(dispatch_quantity),
0
) dispatched

FROM outward_letters

WHERE inward_ref=?
");

$stmt2->bind_param(
"s",
$letter_no
);

$stmt2->execute();

$row2=
$stmt2->get_result()->fetch_assoc();

$dispatched=
(int)$row2['dispatched'];

$pending=
$total-$dispatched;

if($pending<0){

$pending=0;

}

echo json_encode([

'total'=>$total,
'dispatched'=>$dispatched,
'pending'=>$pending

]);
