<?php

/* Use centralized session handling */

include_once __DIR__.'/config/session.php';


/* Default language */

if(!isset($_SESSION['lang'])){

    $_SESSION['lang']="en";

}


$current=$_SESSION['lang'];


$lang=[

'en'=>[

'dashboard'=>'Dashboard',
'inward'=>'Inward Letters',
'outward'=>'Outward Letters',
'outward_balance'=>'Outward Balance',
'logout'=>'Logout',

'id'=>'ID',
'action'=>'Action',

'inward_title'=>'Inward Entry',
'inward_id'=>'Inward ID',
'letter_no'=>'Letter No',
'received_from'=>'Received From',
'department_person'=>'Department / Person',
'subject'=>'Subject',
'remarks'=>'Remarks',
'date'=>'Date',

'outward_title'=>'Outward Entry',
'outward_id'=>'Outward ID',
'sent_to'=>'Sent To',

'save'=>'Save',
'edit'=>'Edit',
'delete'=>'Delete',
'search'=>'Search'

],


'mr'=>[

'dashboard'=>'डॅशबोर्ड',
'inward'=>'आवक पत्रे',
'outward'=>'जावक पत्रे',
'outward_balance'=>'जावक शिल्लक',
'logout'=>'बाहेर पडा',

'id'=>'क्रमांक',
'action'=>'क्रिया',

'inward_title'=>'आवक नोंद',
'inward_id'=>'आवक क्रमांक',
'letter_no'=>'पत्र क्रमांक',
'received_from'=>'प्राप्त झाले',
'department_person'=>'विभाग / व्यक्ती',
'subject'=>'विषय',
'remarks'=>'शेरा',
'date'=>'दिनांक',

'outward_title'=>'जावक नोंद',
'outward_id'=>'जावक क्रमांक',
'sent_to'=>'पाठविले',

'save'=>'जतन करा',
'edit'=>'संपादन',
'delete'=>'हटवा',
'search'=>'शोधा'

]

];

?>
