<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once '../config/session.php';
include_once '../config/auth_check.php';
include_once '../config/security_headers.php';

include '../header.php';
include '../config/db.php';
include '../lang.php';

$language = $_SESSION['lang'] ?? 'en';
$role = $_SESSION['admin_role'] ?? '';

$condition="";

if($role!="admin"){

    $establishment=mysqli_real_escape_string(
        $conn,
        $_SESSION['establishment']
    );

    $condition="
    AND establishment='$establishment'
    ";

}else{

    $selectedEstablishment=
    $_SESSION['selected_establishment']
    ?? 'ALL';

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

$query="

SELECT
id,
letter_no,
dispatch_qty,
dispatch_date,
establishment

FROM dispatch

WHERE language='$language'
$condition

ORDER BY id DESC

";

$result=$conn->query($query);

if(!$result){

die(
"SQL Error : ".
$conn->error
);

}

?>

<title>
<?=($language=="mr") ? "पाठवणी यादी" : "Dispatch List"?>
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

<div class="container">

<div class="table-box">

<h2>

<?=($language=="mr")
? "पाठवणी यादी"
: "Dispatch List"?>

</h2>

<table>

<thead>

<tr>

<th>ID</th>

<th>
<?=($language=="mr")
? "पत्र क्रमांक"
: "Letter No"?>
</th>

<th>
<?=($language=="mr")
? "पाठवणी संख्या"
: "Dispatch Qty"?>
</th>

<th>
<?=($language=="mr")
? "दिनांक"
: "Date"?>
</th>

<?php if($role=="admin"){ ?>

<th>
<?=($language=="mr")
? "संस्था"
: "Establishment"?>
</th>

<?php } ?>

<th>
<?=($language=="mr")
? "स्थिती"
: "Status"?>
</th>

</tr>

</thead>

<tbody>

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?= $row['id'] ?></td>

<td>
<?= htmlspecialchars($row['letter_no']) ?>
</td>

<td>
<?= $row['dispatch_qty'] ?>
</td>

<td>
<?= $row['dispatch_date'] ?>
</td>

<?php if($role=="admin"){ ?>

<td>
<?= htmlspecialchars($row['establishment']) ?>
</td>

<?php } ?>

<td>

<span class="badge">

<?=($language=="mr")
? "पाठवले"
: "Dispatched"?>

</span>

</td>

</tr>

<?php
}

}else{
?>

<tr>

<td colspan="<?=($role=="admin") ? 6 : 5?>">

<?=($language=="mr")
? "पाठवणी नोंदी उपलब्ध नाहीत"
: "No Dispatch Records Found"?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>
