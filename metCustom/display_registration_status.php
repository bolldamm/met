<?php
/*
display on-screen the status (confirmed, pending, discarded, cancelled) of a specified membership, conference or workshop registration (Reg ID)
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$regid=$_GET["id"];
$regtype=$_GET["type"];

switch($regtype) {
case "membership":
$resultado_membership = $db->callProcedure("CALL ed_pr_display_registration_status_membership($regid)");
$result = $db->getData($resultado_membership);
break;
case "conference":
$resultado_conference = $db->callProcedure("CALL ed_pr_display_registration_status_conference($regid)");
$result = $db->getData($resultado_conference);
break;
case "workshop":
$resultado_workshop = $db->callProcedure("CALL ed_pr_display_registration_status_workshop($regid)");
$result = $db->getData($resultado_workshop);
break;
}

if ($result != "") {
foreach($result as $status => $value){
echo "Status of registration ".$regid." is <b>".$value.": ";
switch($value){
case "1":
echo "Pending";
break;
case "2":
echo "Confirmed";
break;
case "3":
echo "Discarded";
break;
case "4":
echo "Cancelled";
break;
}
echo "</b>";
}
} else {
echo "It didn't work. Check the Reg ID.";
}

?>