<?php
include_once __DIR__.'/session.php';
function saveLog($activity){

global $conn;

/* Get session values */

$username = isset($_SESSION['user'])
? $_SESSION['user']
: '';

$role = isset($_SESSION['admin_role'])
? $_SESSION['admin_role']
: '';

$establishment = isset($_SESSION['establishment'])
? $_SESSION['establishment']
: '';


$sql="
INSERT INTO activity_logs
(
username,
role,
establishment,
activity,
log_time
)

VALUES
(
'$username',
'$role',
'$establishment',
'$activity',
NOW()
)
";

$conn->query($sql);

}
?>
