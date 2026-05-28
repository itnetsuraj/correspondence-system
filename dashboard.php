<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config/security_headers.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auto_archive.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/header.php';

/** @var mysqli $conn */

$current =
    $_SESSION['lang']
    ?? 'en';

$language = $current;

$role =
    $_SESSION['admin_role']
    ?? '';

$selectedEstablishment =
    $_SESSION['selected_establishment']
    ?? 'ALL';

$condition = '';

/*
|--------------------------------------------------------------------------
| Establishment Filter
|--------------------------------------------------------------------------
*/

if ($role !== 'admin') {

    $establishment =
        $_SESSION['establishment']
        ?? '';

    $condition =
        " AND establishment='"
        . $conn->real_escape_string(
            $establishment
        )
        . "' ";

} else {

    if ($selectedEstablishment !== 'ALL') {

        $condition =
            " AND establishment='"
            . $conn->real_escape_string(
                $selectedEstablishment
            )
            . "' ";
    }
}

/*
|--------------------------------------------------------------------------
| Total Inward
|--------------------------------------------------------------------------
*/

$inQuery = "
SELECT COUNT(*) AS c
FROM inward_letters
WHERE language='$language'
$condition
";

$inResult = $conn->query($inQuery);

$in = $inResult
    ? $inResult->fetch_assoc()
    : ['c' => 0];

/*
|--------------------------------------------------------------------------
| Total Outward
|--------------------------------------------------------------------------
*/

$outQuery = "
SELECT COUNT(*) AS c
FROM outward_letters
WHERE language='$language'
$condition
";

$outResult = $conn->query($outQuery);

$out = $outResult
    ? $outResult->fetch_assoc()
    : ['c' => 0];

/*
|--------------------------------------------------------------------------
| Today's Inward
|--------------------------------------------------------------------------
*/

$todayInQuery = "
SELECT COUNT(*) AS c
FROM inward_letters
WHERE language='$language'
$condition
AND received_date = CURDATE()
";

$todayInResult =
    $conn->query($todayInQuery);

$todayIn = $todayInResult
    ? $todayInResult->fetch_assoc()
    : ['c' => 0];

/*
|--------------------------------------------------------------------------
| Today's Outward
|--------------------------------------------------------------------------
*/

$todayOutQuery = "
SELECT COUNT(*) AS c
FROM outward_letters
WHERE language='$language'
$condition
AND sent_date = CURDATE()
";

$todayOutResult =
    $conn->query($todayOutQuery);

$todayOut = $todayOutResult
    ? $todayOutResult->fetch_assoc()
    : ['c' => 0];

/*
|--------------------------------------------------------------------------
| Dispatch Status
|--------------------------------------------------------------------------
*/

$totalCopies = 0;
$dispatched = 0;
$pending = 0;

/*
|--------------------------------------------------------------------------
| Total inward copies
|--------------------------------------------------------------------------
*/

$totalQuery = "
SELECT
COALESCE(
SUM(quantity),
0
) AS total
FROM inward_letters
WHERE language='$language'
$condition
";

$totalResult =
    $conn->query($totalQuery);

if ($totalResult) {

    $row =
        $totalResult->fetch_assoc();

    $totalCopies =
        (int) ($row['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| Total dispatched copies
|--------------------------------------------------------------------------
*/

$dispatchQuery = "
SELECT
COALESCE(
SUM(dispatch_qty),
0
) AS total
FROM dispatch
WHERE language='$language'
$condition
";

$dispatchResult =
    $conn->query($dispatchQuery);

if ($dispatchResult) {

    $row2 =
        $dispatchResult->fetch_assoc();

    $dispatched =
        (int) ($row2['total'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| Pending copies
|--------------------------------------------------------------------------
*/

$pending =
    $totalCopies - $dispatched;

if ($pending < 0) {

    $pending = 0;
}

/*
|--------------------------------------------------------------------------
| Recent Activity
|--------------------------------------------------------------------------
*/

$recentQuery = "
SELECT
subject,
received_date,
establishment
FROM inward_letters
WHERE language='$language'
$condition
ORDER BY register_id DESC
LIMIT 5
";

$recent =
    $conn->query($recentQuery);

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
Office Inward Outward Management
</title>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>

<style>

.dashboard-container{
padding:20px;
margin:20px;
}

.stats-grid{
display:grid;
grid-template-columns:repeat(5,1fr);
gap:20px;
margin-bottom:25px;
}

.stat-card{
padding:20px;
border-radius:15px;
color:white;
box-shadow:0 4px 10px rgba(0,0,0,.15);
}

.blue{
background:#3498db;
}

.green{
background:#27ae60;
}

.orange{
background:#f39c12;
}

.red{
background:#e74c3c;
}

.purple{
background:#8e44ad;
}

.stat-card i{
font-size:25px;
margin-bottom:10px;
}

.stat-card h2{
margin:5px 0;
font-size:30px;
}

.bottom-section{
display:grid;
grid-template-columns:35% 65%;
gap:20px;
}

.activity-box,
.chart-box{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 4px 10px rgba(0,0,0,.1);
}

.activity-item{
padding:10px;
border-bottom:1px solid #ddd;
}

.activity-item:last-child{
border:none;
}

@media(max-width:768px){

.stats-grid{
grid-template-columns:1fr;
}

.bottom-section{
grid-template-columns:1fr;
}

}

</style>

</head>

<body>

<div class="dashboard-container">

<h1>Dashboard</h1>

<p>

<?= ($role === 'admin')
? 'Viewing : '
. htmlspecialchars(
    $selectedEstablishment,
    ENT_QUOTES,
    'UTF-8'
)
: 'Establishment : '
. htmlspecialchars(
    $_SESSION['establishment'],
    ENT_QUOTES,
    'UTF-8'
)
?>

</p>

<div class="stats-grid">

<div class="stat-card blue">

<i class="fas fa-inbox"></i>

<h2><?= (int) $in['c'] ?></h2>

<p>Total Inward</p>

</div>

<div class="stat-card green">

<i class="fas fa-paper-plane"></i>

<h2><?= (int) $out['c'] ?></h2>

<p>Total Outward</p>

</div>

<div class="stat-card orange">

<i class="fas fa-calendar-day"></i>

<h2><?= (int) $todayIn['c'] ?></h2>

<p>Today's Inward</p>

</div>

<div class="stat-card red">

<i class="fas fa-thumbtack"></i>

<h2><?= (int) $todayOut['c'] ?></h2>

<p>Today's Outward</p>

</div>

<div class="stat-card purple">

<i class="fas fa-truck"></i>

<h2><?= $pending ?></h2>

<p>

Pending: <?= $pending ?>

<br>

Dispatched: <?= $dispatched ?>

</p>

</div>

</div>

<div class="bottom-section">

<div class="activity-box">

<h2>Recent Activity</h2>

<?php if ($recent instanceof mysqli_result) { ?>

<?php while ($row = $recent->fetch_assoc()) { ?>

<div class="activity-item">

<b>

<?= htmlspecialchars(
    (string) $row['subject'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</b>

<br>

<?= htmlspecialchars(
    (string) $row['received_date'],
    ENT_QUOTES,
    'UTF-8'
) ?>

<?php if ($role === 'admin') { ?>

<br>

<small>

<?= htmlspecialchars(
    (string) $row['establishment'],
    ENT_QUOTES,
    'UTF-8'
) ?>

</small>

<?php } ?>

</div>

<?php } ?>

<?php } ?>

</div>

<div class="chart-box">

<h2>Statistics</h2>

<canvas id="lettersChart"></canvas>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(
document.getElementById(
'lettersChart'
),
{
type:'bar',

data:{
labels:[
'Inward',
'Outward',
'Today Inward',
'Today Outward'
],

datasets:[{
data:[
<?= (int) $in['c'] ?>,
<?= (int) $out['c'] ?>,
<?= (int) $todayIn['c'] ?>,
<?= (int) $todayOut['c'] ?>
]
}]
},

options:{
plugins:{
legend:{
display:false
}
}
}
}
);

</script>

</body>

</html>
