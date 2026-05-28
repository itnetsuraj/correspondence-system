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
$_SESSION['selected_establishment'] ?? 'ALL';


$allowedSort=[

'register_id',
'letter_no',
'sent_date',
'subject',
'sent_to',
'department_person',
'postage_amount',
'document_type'

];

if(!in_array($sort,$allowedSort)){
    $sort='register_id';
}

$order=($order=="ASC")?"ASC":"DESC";


if($role=="admin"){
    $displayEstablishment=$selectedEstablishment;
}else{
    $displayEstablishment=$_SESSION['establishment'];
}

if($displayEstablishment=="ALL"){
$displayEstablishment=
"All Establishments";
}


$sql="
SELECT *
FROM outward_letters
WHERE language=?
AND YEAR(sent_date)=?
";

$params=[];
$types="si";

$params[]=$language;
$params[]=$selectedYear;


if($role!="admin"){

$sql.=" AND establishment=?";
$types.="s";
$params[]=$_SESSION['establishment'];

}else{

if($selectedEstablishment!="ALL"){

$sql.=" AND establishment=?";
$types.="s";
$params[]=$selectedEstablishment;

}

}


if(!empty($from) && !empty($to)){

$sql.=" AND sent_date BETWEEN ? AND ?";

$types.="ss";

$params[]=$from;
$params[]=$to;

}


if(!empty($search)){

$sql.="

AND(

letter_no LIKE ?
OR subject LIKE ?
OR sent_to LIKE ?
OR department_person LIKE ?
OR document_type LIKE ?
OR other_document_type LIKE ?

)

";

$searchText="%".$search."%";

$types.="ssssss";

for($i=0;$i<6;$i++){

$params[]=$searchText;

}

}

$sql.=" ORDER BY $sort $order";

$stmt=$conn->prepare($sql);

$stmt->bind_param($types,...$params);

$stmt->execute();

$result=$stmt->get_result();


function sortLink(
$field,
$title,
$sort,
$order,
$selectedYear,
$from,
$to,
$search
){

$newOrder=
($sort==$field && $order=="ASC")
?
"DESC"
:
"ASC";

echo '<th><a href="?sort='.$field.
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

?>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

table{
width:100%;
border-collapse:collapse;
background:white;
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
border:1px solid #ddd;
vertical-align:middle;
}

table tr:hover td{
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

<h2><?= htmlspecialchars($displayEstablishment) ?></h2>

<h3>
<?=($language=="mr")?"जावक पत्रे ":"Outward Letters"?>
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

<?php for($y=date("Y");$y>=2020;$y--){ ?>

<option
value="<?=$y?>"
<?=($selectedYear==$y)?'selected':''?>>

<?=$y?>

</option>

<?php } ?>

</select>

<input
type="date"
name="from"
value="<?=$from?>">

<input
type="date"
name="to"
value="<?=$to?>">

<input
type="text"
name="search"
placeholder="Search"
value="<?=htmlspecialchars($search)?>">


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


<a href="export_pdf.php?year=<?=$selectedYear?>&from=<?=$from?>&to=<?=$to?>&sort=<?=$sort?>&order=<?=$order?>" target="_blank">

<button
type="button"
class="btn"
style="background:#27ae60">

PDF

</button>

</a>


<a href="export_excel.php?year=<?=$selectedYear?>">

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

sortLink(
"register_id",
($language=="mr")?" जावक क्रमांक":"Outward No",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"letter_no",
($language=="mr")?"पत्र क्रमांक":"Letter No",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"sent_date",
($language=="mr")?"दिनांक":"Date",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"subject",
($language=="mr")?"विषय":"Subject",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"sent_to",
($language=="mr")?"पाठविले":"Sent To",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"department_person",
($language=="mr")?"विभाग":"Department",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"postage_amount",
($language=="mr")?"टपाल खर्च":"Postage",
$sort,$order,$selectedYear,$from,$to,$search
);

sortLink(
"document_type",
($language=="mr")
?
"दस्तऐवज प्रकार"
:
"Document Type",
$sort,$order,$selectedYear,$from,$to,$search
);

?>

<th>
<?=($language=="mr")?"शेरा":"Remarks"?>
</th>

<th class="action-column">
<?=($language=="mr")?"कृती":"Action"?>
</th>

</tr>


<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?=$row['register_id']?></td>

<td><?=htmlspecialchars($row['letter_no'])?></td>

<td><?=htmlspecialchars($row['sent_date'])?></td>

<td><?=htmlspecialchars($row['subject'])?></td>

<td><?=htmlspecialchars($row['sent_to'])?></td>

<td><?=htmlspecialchars($row['department_person'])?></td>

<td>₹ <?=$row['postage_amount']?></td>

<td>

<?php

if(
$row['document_type']=="Others"
&&
!empty($row['other_document_type'])
){

echo htmlspecialchars(
$row['other_document_type']
);

}else{

echo htmlspecialchars(
$row['document_type']
);

}

?>

</td>

<td><?=htmlspecialchars($row['remarks'])?></td>

<td>

<?php if($role=="admin"){ ?>

<div class="action-buttons">

<a
href="edit.php?id=<?=$row['id']?>"
class="action-btn edit-btn"
title="Edit">

<i class="fa-solid fa-pen"></i>

</a>

<a
href="delete.php?id=<?=$row['id']?>"
class="action-btn delete-btn"
onclick="return confirm('Delete?')"
title="Delete">

<i class="fa-solid fa-trash"></i>

</a>

</div>

<?php } else { ?>

<?=($language=="mr")
?
"परवानगी नाही"
:
"No Permission"?>

<?php } ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10" class="no-record">

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
