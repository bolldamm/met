<?php
/*
* Sets a Stripe payment to id_estado_inscripcion = 2
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$RegNo=$_GET["registration_id"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_set_confirmed_from_stripe_id('".$RegNo."')";
$resultado=$db->callProcedure($codeProcedure);

echo "Registration N. ";
echo $RegNo;
echo " succesfully set to confirmed.";
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>