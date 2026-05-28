<?php
include_once __DIR__.'/config/session.php';
include_once __DIR__.'/config/auth_check.php';
include 'header.php';

include_once __DIR__.'/config/security_headers.php';
include 'config/db.php';
include 'lang.php';

$language=$_SESSION['lang'] ?? 'en';
$role=$_SESSION['admin_role'] ?? '';

$selectedEstablishment=
$_SESSION['selected_establishment']
?? 'ALL';

$condition="";

if($role!="admin"){

$establishment=
mysqli_real_escape_string(
$conn,
$_SESSION['establishment']
);

$condition="
AND establishment='$establishment'
";

}
else{

if($selectedEstablishment!="ALL"){

$selectedEstablishment=
mysqli_real_escape_string(
$conn,
$selectedEstablishment
);

$condition="
AND establishment='$selectedEstablishment'
";

}

}


$in=$conn->query("
SELECT COUNT(*) c
FROM inward_letters
WHERE language='$language'
$condition
")->fetch_assoc();

$out=$conn->query("
SELECT COUNT(*) c
FROM outward_letters
WHERE language='$language'
$condition
")->fetch_assoc();

$todayIn=$conn->query("
SELECT COUNT(*) c
FROM inward_letters
WHERE language='$language'
$condition
AND received_date=CURDATE()
")->fetch_assoc();

$todayOut=$conn->query("
SELECT COUNT(*) c
FROM outward_letters
WHERE language='$language'
$condition
AND sent_date=CURDATE()
")->fetch_assoc();


/* Dispatch status */

$totalCopies=0;
$dispatched=0;
$pending=0;


/* Total inward copies */

$result=$conn->query("

SELECT
COALESCE(
SUM(quantity),
0
) AS total

FROM inward_letters

WHERE language='$language'
$condition

");

if($result){

$row=
$result->fetch_assoc();

$totalCopies=
(int)($row['total'] ?? 0);

}


/* Total dispatched copies */

$result2=$conn->query("

SELECT
COALESCE(
SUM(dispatch_qty),
0
) AS total

FROM dispatch

WHERE language='$language'
$condition

");

if($result2){

$row2=
$result2->fetch_assoc();

$dispatched=
(int)($row2['total'] ?? 0);

}


/* Pending copies */

$pending=
$totalCopies-
$dispatched;

if($pending<0){

$pending=0;

}



/* Recent */

$recent=$conn->query("
SELECT
subject,
received_date,
establishment
FROM inward_letters
WHERE language='$language'
$condition
ORDER BY register_id DESC
LIMIT 5
");

?>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

<title>Office Inward Outward Management</title>

<div class="dashboard-container">

<h1>Dashboard</h1>

<p>

<?=($role=="admin")
?"Viewing : ".$selectedEstablishment
:"Establishment : ".$_SESSION['establishment']
?>

</p>

<div class="stats-grid">

<div class="stat-card blue">

<i class="fas fa-inbox"></i>

<h2><?= $in['c'] ?></h2>

<p>Total Inward</p>

</div>


<div class="stat-card green">

<i class="fas fa-paper-plane"></i>

<h2><?= $out['c'] ?></h2>

<p>Total Outward</p>

</div>


<div class="stat-card orange">

<i class="fas fa-calendar-day"></i>

<h2><?= $todayIn['c'] ?></h2>

<p>Today's Inward</p>

</div>


<div class="stat-card red">

<i class="fas fa-thumbtack"></i>

<h2><?= $todayOut['c'] ?></h2>

<p>Today's Outward</p>

</div>


<div class="stat-card purple">

<i class="fas fa-truck"></i>

<h2><?= $pending ?></h2>

<p>

Pending Dispatched: <?= $pending ?>

<br>

Dispatched: <?= $dispatched ?>

</p>

</div>

</div>


<div class="bottom-section">

<div class="activity-box">

<h2>Recent Activity</h2>

<?php while($row=$recent->fetch_assoc()){ ?>

<div class="activity-item">

<b>

<?= htmlspecialchars(
$row['subject']
) ?>

</b>

<br>

<?= htmlspecialchars(
$row['received_date']
) ?>

<?php if($role=="admin"){ ?>

<br>

<small>

<?= htmlspecialchars(
$row['establishment']
) ?>

</small>

<?php } ?>

</div>

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

<?= $in['c']?>,
<?= $out['c']?>,
<?= $todayIn['c']?>,
<?= $todayOut['c']?>

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
