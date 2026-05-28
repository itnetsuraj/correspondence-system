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


$res=$conn->query("
SELECT *
FROM amount_transactions
ORDER BY id DESC
");

?>

<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h2>Amount Transactions</h2>

<a href="transactions_pdf.php" target="_blank">

<button type="button">

Export PDF

</button>

</a>

<br><br>

<table>

<tr>

<th>ID</th>

<th>Amount</th>

<th>Type</th>

<th>Date</th>

</tr>

<?php

while($r=$res->fetch_assoc()){

?>

<tr>

<td><?= $r['id'] ?></td>

<td>₹ <?= $r['amount'] ?></td>

<td><?= $r['type'] ?></td>

<td><?= $r['entry_date'] ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>
