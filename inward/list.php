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


include '../lang.php';

$language=$_SESSION['lang'] ?? 'en';
$role=$_SESSION['admin_role'] ?? '';

$from=$_GET['from'] ?? '';
$to=$_GET['to'] ?? '';
$selectedYear=$_GET['year'] ?? date("Y");
$search=$_GET['search'] ?? '';

$sort=$_GET['sort'] ?? 'register_id';
$order=$_GET['order'] ?? 'DESC';

$selectedEstablishment=
$_SESSION['selected_establishment']
?? 'ALL';

$allowedSort=[

'register_id',
'letter_no',
'received_date',
'subject',
'received_from',
'department_person',
'quantity',
'document_type'

];

if(!in_array($sort,$allowedSort)){

$sort='register_id';

}

$order=
($order=="ASC")
?
"ASC"
:
"DESC";


if($role=="admin"){

$displayEstablishment=
$selectedEstablishment;

}else{

$displayEstablishment=
$_SESSION['establishment'];

}

if($displayEstablishment=="ALL"){

$displayEstablishment=
"All Establishments";

}


/* Query */

$sql="
SELECT *
FROM inward_letters
WHERE language=?
AND YEAR(received_date)=?
";

$params=[];
$types="si";

$params[]=$language;
$params[]=$selectedYear;


/* Establishment */

if($role!="admin"){

$sql.=" AND establishment=?";

$types.="s";

$params[]=
$_SESSION['establishment'];

}
else{

if($selectedEstablishment!="ALL"){

$sql.=" AND establishment=?";

$types.="s";

$params[]=
$selectedEstablishment;

}

}


/* Date */

if(
!empty($from)
&&
!empty($to)
){

$sql.="

AND received_date
BETWEEN ? AND ?

";

$types.="ss";

$params[]=$from;
$params[]=$to;

}


/* Search */

/* Search */

if(!empty($search)){

$sql.="

AND(

letter_no LIKE ?
OR subject LIKE ?
OR received_from LIKE ?
OR department_person LIKE ?
OR document_type LIKE ?
OR other_document_type LIKE ?

)

";

$searchText="%".$search."%";

$types.="ssssss";

$params[]=$searchText;
$params[]=$searchText;
$params[]=$searchText;
$params[]=$searchText;
$params[]=$searchText;
$params[]=$searchText;

}

/* Sorting */

$sql.=" ORDER BY $sort $order";

$stmt=
$conn->prepare($sql);

$stmt->bind_param(
$types,
...$params
);

$stmt->execute();

$result=
$stmt->get_result();

?>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

table{
width:100%;
border-collapse:collapse;
}

table th{
background:#2c3e50;
color:white !important;
padding:12px;
text-align:center;
}

table th a{
color:white !important;
text-decoration:none;
display:block;
}

table td{
padding:10px;
text-align:center;
border-bottom:1px solid #ddd;
vertical-align:middle;
}

table tr:hover{
background:#f5f5f5;
}

.btn{
padding:10px;
border:none;
border-radius:5px;
color:white;
cursor:pointer;
}

.action-column{
width:130px;
}

.action-buttons{
display:flex;
justify-content:center;
gap:8px;
}

.action-btn{
width:35px;
height:35px;
display:flex;
align-items:center;
justify-content:center;
border-radius:5px;
text-decoration:none;
color:white;
}

.edit-btn{
background:#3498db;
}

.delete-btn{
background:red;
}

</style>
<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h2>

<?= htmlspecialchars($displayEstablishment) ?>

</h2>

<h3>

<?= $lang[$current]['inward'] ?>

</h3>

<form
method="GET"
style="
display:flex;
gap:10px;
flex-wrap:wrap;
margin-bottom:20px;
align-items:center;
">

<select name="year">

<?php

for(
$y=date("Y");
$y>=2020;
$y--
){

?>

<option
value="<?= $y ?>"
<?=($selectedYear==$y)?'selected':''?>>

<?= $y ?>

</option>

<?php } ?>

</select>

<input
type="date"
name="from"
value="<?= $from ?>"
onclick="this.showPicker()">

<input
type="date"
name="to"
value="<?= $to ?>"
onclick="this.showPicker()">

<input
type="text"
name="search"
placeholder="Search..."
value="<?= htmlspecialchars($search) ?>">

<button
class="btn"
style="background:#2c3e50">

Search

</button>

<a href="list.php">

<button
type="button"
class="btn"
style="background:red">

Reset

</button>

</a>

<a href="export_pdf.php?year=<?=$selectedYear?>&sort=<?=$sort?>&order=<?=$order?>" target="_blank">

<button
type="button"
class="btn"
style="background:#27ae60">

PDF

</button>

</a>

<a
href="export_excel.php?year=<?=urlencode($selectedYear)?>&from=<?=urlencode($from)?>&to=<?=urlencode($to)?>&search=<?=urlencode($search)?>">

<button
type="button"
class="btn"
style="background:#16a085">

Excel

</button>

</a>

</form>

<table>

<tr>

<?php

function sortLink($field,$title){

global $sort,$order,$selectedYear,$from,$to,$search;

$newOrder=
($sort==$field && $order=="ASC")
?
"DESC"
:
"ASC";

echo '<th>
<a href="?sort='.$field.
'&order='.$newOrder.
'&year='.$selectedYear.
'&from='.$from.
'&to='.$to.
'&search='.$search.'">';

echo $title;

if($sort==$field){

echo ($order=="ASC")
?
" ↑"
:
" ↓";

}

echo '</a></th>';

}


sortLink(
"register_id",
($language=="mr")
?
"आवक क्रमांक"
:
"Outward No"
);

sortLink(
"letter_no",
($language=="mr")
?
"पत्र क्रमांक"
:
"Letter No"
);

sortLink(
"received_date",
($language=="mr")
?
"दिनांक"
:
"Date"
);

sortLink(
"subject",
($language=="mr")
?
"विषय"
:
"Subject"
);

sortLink(
"received_from",
($language=="mr")
?
"प्राप्त"
:
"Received From"
);

sortLink(
"department_person",
($language=="mr")
?
"विभाग"
:
"Department"
);

sortLink(
"quantity",
($language=="mr")
?
"प्रती"
:
"Copies"
);

sortLink(
"document_type",
($language=="mr")
?
"दस्तऐवज प्रकार"
:
"Document Type"
);

?>

<th>

<?=($language=="mr")
?
"शेरा"
:
"Remarks"?>

</th>

<th class="action-column">

<?=($language=="mr")
?
"कृती"
:
"Action"?>

</th>

</tr>

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?= $row['register_id'] ?></td>

<td><?= htmlspecialchars($row['letter_no']) ?></td>

<td><?= htmlspecialchars($row['received_date']) ?></td>

<td><?= htmlspecialchars($row['subject']) ?></td>

<td><?= htmlspecialchars($row['received_from']) ?></td>

<td><?= htmlspecialchars($row['department_person']) ?></td>

<td><?= $row['quantity'] ?></td>

<td>

<?php

if(
isset($row['document_type'])
&&
(
$row['document_type']=="Others"
||
$row['document_type']=="इतर"
)
&&
!empty($row['other_document_type'])
){

echo htmlspecialchars(
$row['other_document_type']
);

}
else{

echo htmlspecialchars(
$row['document_type'] ?? ''
);

}

?>

</td>

<td>

<?= htmlspecialchars($row['remarks']) ?>

</td>

<td>

<?php if($role=="admin"){ ?>

<div class="action-buttons">

<a
href="edit.php?id=<?= $row['id'] ?>"
class="action-btn edit-btn"
title="<?=($language=="mr")?'संपादन':'Edit'?>">

<i class="fa-solid fa-pen"></i>

</a>

<a
href="delete.php?id=<?= $row['id'] ?>"
class="action-btn delete-btn"
onclick="return confirm('<?=($language=="mr")?'हटवायचे आहे का?':'Delete?'?>')"
title="<?=($language=="mr")?'हटवा':'Delete'?>">

<i class="fa-solid fa-trash"></i>

</a>

</div>

<?php } else { ?>

<?=($language=="mr")
?
"परवानगी नाही"
:
"No Permission" ?>

<?php } ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10">

<?=($language=="mr")
?
"नोंदी आढळल्या नाहीत"
:
"No Records Found"?>

</td>

</tr>

<?php } ?>

</table>
</div>
</div>
