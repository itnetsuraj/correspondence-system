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


include '../config/save_log.php';


/* Establishment */

$role=$_SESSION['admin_role'] ?? '';

$establishment=
($role=="admin")
?
($_SESSION['selected_establishment'] ?? 'ALL')
:
($_SESSION['establishment'] ?? '');


if($establishment=="ALL"){

echo "

<script>

alert('Please select establishment');

window.history.back();

</script>

";

exit;

}


/* CSRF Token */

if(empty($_SESSION['csrf_token'])){

$_SESSION['csrf_token']=
bin2hex(random_bytes(32));

}


/* Create balance row if not exists */

$stmt=
$conn->prepare("

INSERT IGNORE INTO
outward_balance
(
establishment,
balance
)

VALUES
(
?,
0
)

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();


/* Save Transaction */

if(isset($_POST['save'])){


if(

!isset($_POST['csrf_token'])
||
!hash_equals(
$_SESSION['csrf_token'],
$_POST['csrf_token']
)

){

die("Invalid CSRF");

}


$amount=
trim($_POST['amount']);

$type=
trim($_POST['type']);


if(

!preg_match(
'/^[0-9]+$/',
$amount
)

){

echo "

<script>

alert('Only numbers allowed');

location='balance.php';

</script>

";

exit;

}


$amount=
(int)$amount;


if($amount<=0){

echo "

<script>

alert('Amount must be greater than zero');

location='balance.php';

</script>

";

exit;

}


if(

$type!="Credit"
&&
$type!="Debit"

){

die("Invalid Transaction");

}


$conn->begin_transaction();


try{


/* Current balance */

$stmt=
$conn->prepare("

SELECT balance
FROM outward_balance
WHERE establishment=?
FOR UPDATE

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$currentData=
$stmt
->get_result()
->fetch_assoc();

$currentBalance=
(int)
($currentData['balance'] ?? 0);



/* Credit */

if($type=="Credit"){

$newBalance=
$currentBalance+
$amount;

}


/* Debit */

else{


if(

$currentBalance
<
$amount

){

throw new Exception(
"Insufficient Balance"
);

}


$newBalance=
$currentBalance-
$amount;

}


/* Update */

$stmt=
$conn->prepare("

UPDATE outward_balance

SET balance=?

WHERE establishment=?

");

$stmt->bind_param(

"is",

$newBalance,
$establishment

);

$stmt->execute();


/* Transaction Log */

$stmt=
$conn->prepare("

INSERT INTO
amount_transactions

(
amount,
type,
establishment
)

VALUES
(
?,
?,
?
)

");

$stmt->bind_param(

"iss",

$amount,
$type,
$establishment

);

$stmt->execute();


saveLog(

$type.
" ₹".
$amount.
" Establishment : ".
$establishment

);


$conn->commit();


echo "

<script>

alert('Transaction Successful');

location='balance.php';

</script>

";


}
catch(Exception $e){

$conn->rollback();

echo "

<script>

alert('".$e->getMessage()."');

location='balance.php';

</script>

";

}

}



/* Current Balance */

$stmt=
$conn->prepare("

SELECT balance
FROM outward_balance
WHERE establishment=?

");

$stmt->bind_param(
"s",
$establishment
);

$stmt->execute();

$row=
$stmt
->get_result()
->fetch_assoc();

$currentBalance=
(int)
($row['balance'] ?? 0);

?>


<title>Office Inward Outward Management</title>

<div class="center-page">

<div class="box">

<h2>

<?= htmlspecialchars($establishment) ?>

</h2>


<h2>

Outward Balance

</h2>


<h1>

₹ <?= number_format($currentBalance) ?>

</h1>


<form
method="post"
class="modern-form">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>"
>


<div class="input-group full-width">

<label>

Amount

</label>

<input
type="text"
name="amount"
required
maxlength="10"
placeholder="Enter Amount"

oninput="
this.value=
this.value.replace(/[^0-9]/g,'')
">

</div>


<div class="input-group full-width">

<label>

Transaction Type

</label>

<select
name="type"
required>

<option value="Credit">

Credit (+)

</option>

<option value="Debit">

Debit (-)

</option>

</select>

</div>


<button
class="save-btn"
name="save">

Save

</button>

</form>

</div>

</div>
