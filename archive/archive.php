<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once '../config/session.php';
require_once '../config/auth_check.php';
require_once '../config/security_headers.php';
require_once '../config/db.php';

/** @var mysqli $conn */

require_once '../header.php';
require_once '../lang.php';

$selectedYear =
    (int) (
        $_GET['year']
        ?? (date('Y') - 1)
    );

?>

<title>
Office Inward Outward Management
</title>

<div class="center-page">

<div class="box">

<h2>Archived Records</h2>

<form
method="GET"
class="modern-form"
>

<div class="input-group">

<label>
Select Archive Year
</label>

<select name="year">

<?php

$currentYear =
    (int) date('Y');

for (
    $y = $currentYear - 1;
    $y >= 2020;
    $y--
) {

?>

<option
value="<?= $y ?>"
<?= ($selectedYear === $y)
? 'selected'
: '' ?>
>

<?= $y ?>

</option>

<?php } ?>

</select>

</div>

<button
class="save-btn"
type="submit"
>

Load Records

</button>

</form>

<br>

<a
href="export_archive_inward_pdf.php?year=<?= urlencode((string) $selectedYear) ?>"
target="_blank"
>

<button
type="button"
style="
padding:10px;
background:#27ae60;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
margin-right:10px;
"
>

Export Archived Inward PDF

</button>

</a>

<a
href="export_archive_outward_pdf.php?year=<?= urlencode((string) $selectedYear) ?>"
target="_blank"
>

<button
type="button"
style="
padding:10px;
background:#2980b9;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
"
>

Export Archived Outward PDF

</button>

</a>

<br><br>

<h3>Archived Inward Letters</h3>

<table border="1" width="100%">

<tr>

<th>ID</th>
<th>Letter No</th>
<th>Date</th>
<th>Received From</th>
<th>Subject</th>
<th>Department</th>
<th>Remarks</th>

</tr>

<?php

$inwardQuery = "
SELECT *
FROM inward_archive
WHERE YEAR(received_date) = ?
ORDER BY register_id ASC
";

$stmt = $conn->prepare($inwardQuery);

if ($stmt) {

    $stmt->bind_param(
        'i',
        $selectedYear
    );

    $stmt->execute();

    $res =
        $stmt->get_result();

    if (
        $res instanceof mysqli_result
        &&
        $res->num_rows > 0
    ) {

        while (
            $r = $res->fetch_assoc()
        ) {

?>

<tr>

<td>
<?= htmlspecialchars(
    (string) $r['register_id'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['letter_no'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['received_date'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['received_from'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['subject'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['department_person'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r['remarks'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

</tr>

<?php

        }

    } else {

?>

<tr>

<td colspan="7">

No archived inward records

</td>

</tr>

<?php

    }

    $stmt->close();
}

?>

</table>

<br><br>

<h3>Archived Outward Letters</h3>

<table border="1" width="100%">

<tr>

<th>ID</th>
<th>Letter No</th>
<th>Date</th>
<th>Sent To</th>
<th>Subject</th>
<th>Department</th>
<th>Postage</th>
<th>Remarks</th>

</tr>

<?php

$outwardQuery = "
SELECT *
FROM outward_archive
WHERE YEAR(sent_date) = ?
ORDER BY register_id ASC
";

$stmt2 =
    $conn->prepare($outwardQuery);

if ($stmt2) {

    $stmt2->bind_param(
        'i',
        $selectedYear
    );

    $stmt2->execute();

    $res2 =
        $stmt2->get_result();

    if (
        $res2 instanceof mysqli_result
        &&
        $res2->num_rows > 0
    ) {

        while (
            $r2 = $res2->fetch_assoc()
        ) {

?>

<tr>

<td>
<?= htmlspecialchars(
    (string) $r2['register_id'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r2['letter_no'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r2['sent_date'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r2['sent_to'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r2['subject'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>
<?= htmlspecialchars(
    (string) $r2['department_person'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

<td>

₹ <?= htmlspecialchars(
    (string) $r2['postage_amount'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</td>

<td>
<?= htmlspecialchars(
    (string) $r2['remarks'],
    ENT_QUOTES,
    'UTF-8'
) ?>
</td>

</tr>

<?php

        }

    } else {

?>

<tr>

<td colspan="8">

No archived outward records

</td>

</tr>

<?php

    }

    $stmt2->close();
}

?>

</table>

</div>

</div>
