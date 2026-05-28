<?php

include_once '../config/session.php';
include_once '../config/security_headers.php';
include_once __DIR__.'/config/auth_check.php';
include '../header.php';
include '../config/db.php';

$selectedYear=$_GET['year'] ?? date('Y')-1;

?>

<title>Office Inward Outward Management</title>
<div class="center-page">

<div class="box">

<h2>Archived Records</h2>

<form method="GET" class="modern-form">

<div class="input-group">

<label>Select Archive Year</label>

<select name="year">

<?php

$currentYear=date("Y");

for(
$y=$currentYear-1;
$y>=2020;
$y--
){

$selected=
($selectedYear==$y)
?
"selected"
:
"";

?>

<option
value="<?= $y ?>"
<?= $selected ?>

>

<?= $y ?>

</option>

<?php } ?>

</select>

</div>

<button
class="save-btn"
type="submit">

Load Records

</button>


</form>

<br>

<a href="export_archive_inward_pdf.php?year=<?= $selectedYear ?>"
target="_blank">

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
">

Export Archived Inward PDF

</button>

</a>


<a href="export_archive_outward_pdf.php?year=<?= $selectedYear ?>"
target="_blank">

<button
type="button"
style="
padding:10px;
background:#2980b9;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
">

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

$res=$conn->query("

SELECT *
FROM inward_archive
WHERE YEAR(received_date)='$selectedYear'
ORDER BY register_id ASC

");

if($res->num_rows>0){

while($r=$res->fetch_assoc()){

?>

<tr>

<td><?= $r['register_id'] ?></td>
<td><?= $r['letter_no'] ?></td>
<td><?= $r['received_date'] ?></td>
<td><?= $r['received_from'] ?></td>
<td><?= $r['subject'] ?></td>
<td><?= $r['department_person'] ?></td>
<td><?= $r['remarks'] ?></td>

</tr>

<?php
}

}else{

?>

<tr>

<td colspan="7">

No archived inward records

</td>

</tr>

<?php } ?>

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

$res2=$conn->query("

SELECT *
FROM outward_archive
WHERE YEAR(sent_date)='$selectedYear'
ORDER BY register_id ASC

");

if($res2->num_rows>0){

while($r2=$res2->fetch_assoc()){

?>

<tr>

<td><?= $r2['register_id'] ?></td>
<td><?= $r2['letter_no'] ?></td>
<td><?= $r2['sent_date'] ?></td>
<td><?= $r2['sent_to'] ?></td>
<td><?= $r2['subject'] ?></td>
<td><?= $r2['department_person'] ?></td>
<td>₹ <?= $r2['postage_amount'] ?></td>
<td><?= $r2['remarks'] ?></td>

</tr>

<?php
}

}else{

?>

<tr>

<td colspan="8">

No archived outward records

</td>

</tr>

<?php } ?>

</table>

</div>

</div>
