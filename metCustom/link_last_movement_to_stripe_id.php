<?php
/*
* Link the last movement created in EG with Stripe registration ID
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$RegNo=$_GET["registration_id"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_link_last_movement_to_stripe_id('".$RegNo."')";
$resultado=$db->callProcedure($codeProcedure);

// echo $RegNo;
echo "DONE!";
echo "<br><br>You may have to reload the window after returning to easyGestor (F5).";
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>