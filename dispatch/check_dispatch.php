<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include '../config/db.php';

header('Content-Type: application/json');

$language=
$_SESSION['lang'] ?? 'en';


if(
($_SESSION['admin_role'] ?? '')
==
"admin"
){

$establishment=
$_SESSION['selected_establishment']
?? '';

}else{

$establishment=
$_SESSION['establishment']
?? '';

}

if(empty($establishment)){

echo json_encode([

'error'=>'Establishment missing'

]);

exit;

}


/* keep leading zeros */

$inward_id=
trim(
$_GET['inward_id']
?? ''
);


if(empty($inward_id)){

echo json_encode([

'error'=>'Invalid inward ID'

]);

exit;

}


/* Fetch inward */

$stmt=$conn->prepare("

SELECT

register_id,
letter_no,
quantity

FROM inward_letters

WHERE
TRIM(register_id)=TRIM(?)
AND language=?
AND establishment=?

LIMIT 1

");

$stmt->bind_param(

"sss",

$inward_id,
$language,
$establishment

);

$stmt->execute();

$result=
$stmt->get_result();

$row=
$result->fetch_assoc();


if(!$row){

echo json_encode([

'error'=>'Record not found'

]);

exit;

}


$total=
(int)$row['quantity'];

$letter_no=
$row['letter_no'];

$register_id=
$row['register_id'];


/* Dispatched */

$stmt2=$conn->prepare("

SELECT

COALESCE(
SUM(dispatch_qty),
0
)

AS dispatched

FROM dispatch

WHERE inward_id=?

");

$stmt2->bind_param(

"s",

$register_id

);

$stmt2->execute();

$data=
$stmt2
->get_result()
->fetch_assoc();

$already=
(int)$data['dispatched'];

$pending=
$total-$already;

if($pending<0){

$pending=0;

}


echo json_encode([

'inward_id'=>$register_id,

'letter_no'=>$letter_no,

'total'=>$total,

'dispatched'=>$already,

'pending'=>$pending

]);

exit;

?>
