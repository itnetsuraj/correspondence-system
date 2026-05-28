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

$language =
    $_SESSION['lang']
    ?? 'en';

$role =
    $_SESSION['admin_role']
    ?? '';

$condition = '';

$params = [];

$types = '';

/*
|--------------------------------------------------------------------------
| Establishment Filter
|--------------------------------------------------------------------------
*/

if ($role !== 'admin') {

    $establishment =
        (string) (
            $_SESSION['establishment']
            ?? ''
        );

    $condition =
        ' AND establishment = ? ';

    $params[] =
        $establishment;

    $types .= 's';

} else {

    $selectedEstablishment =
        (string) (
            $_SESSION['selected_establishment']
            ?? 'ALL'
        );

    if ($selectedEstablishment !== 'ALL') {

        $condition =
            ' AND establishment = ? ';

        $params[] =
            $selectedEstablishment;

        $types .= 's';
    }
}

/*
|--------------------------------------------------------------------------
| Main Query
|--------------------------------------------------------------------------
*/

$query = "
    SELECT
        id,
        letter_no,
        dispatch_qty,
        dispatch_date,
        establishment
    FROM dispatch
    WHERE language = ?
    $condition
    ORDER BY id DESC
";

$stmt = $conn->prepare($query);

if (!$stmt instanceof mysqli_stmt) {

    die('Database query preparation failed');
}

$bindTypes =
    's' . $types;

$bindParams =
    array_merge(
        [$language],
        $params
    );

$stmt->bind_param(
    $bindTypes,
    ...$bindParams
);

if (!$stmt->execute()) {

    $stmt->close();

    die('Failed to execute query');
}

$result =
    $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"
>

<title>

<?= (
    $language === 'mr'
)
? 'पाठवणी यादी'
: 'Dispatch List' ?>

</title>

<style>

.container{
padding:20px;
}

.table-box{
background:white;
padding:20px;
border-radius:10px;
box-shadow:0 4px 10px rgba(0,0,0,.1);
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#2c3e50;
color:white;
padding:12px;
}

td{
padding:10px;
border-bottom:1px solid #ddd;
text-align:center;
}

tr:hover{
background:#f5f5f5;
}

.badge{
padding:6px 10px;
background:#27ae60;
color:white;
border-radius:6px;
}

</style>

</head>

<body>

<div class="container">

<div class="table-box">

<h2>

<?= (
    $language === 'mr'
)
? 'पाठवणी यादी'
: 'Dispatch List' ?>

</h2>

<table>

<thead>

<tr>

<th>ID</th>

<th>

<?= (
    $language === 'mr'
)
? 'पत्र क्रमांक'
: 'Letter No' ?>

</th>

<th>

<?= (
    $language === 'mr'
)
? 'पाठवणी संख्या'
: 'Dispatch Qty' ?>

</th>

<th>

<?= (
    $language === 'mr'
)
? 'दिनांक'
: 'Date' ?>

</th>

<?php if ($role === 'admin') { ?>

<th>

<?= (
    $language === 'mr'
)
? 'संस्था'
: 'Establishment' ?>

</th>

<?php } ?>

<th>

<?= (
    $language === 'mr'
)
? 'स्थिती'
: 'Status' ?>

</th>

</tr>

</thead>

<tbody>

<?php

if (
    $result instanceof mysqli_result
    &&
    $result->num_rows > 0
) {

    while (
        $row = $result->fetch_assoc()
    ) {

?>

<tr>

<td>

<?= (int) $row['id'] ?>

</td>

<td>

<?= htmlspecialchars(
    (string) $row['letter_no'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</td>

<td>

<?= (int) $row['dispatch_qty'] ?>

</td>

<td>

<?= htmlspecialchars(
    (string) $row['dispatch_date'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</td>

<?php if ($role === 'admin') { ?>

<td>

<?= htmlspecialchars(
    (string) $row['establishment'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</td>

<?php } ?>

<td>

<span class="badge">

<?= (
    $language === 'mr'
)
? 'पाठवले'
: 'Dispatched' ?>

</span>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td colspan="<?= (
    $role === 'admin'
)
? '6'
: '5' ?>">

<?= (
    $language === 'mr'
)
? 'पाठवणी नोंदी उपलब्ध नाहीत'
: 'No Dispatch Records Found' ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>

<?php

$stmt->close();

?>
