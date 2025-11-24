<?php
/*
change the total amount paid for a particular METM registration (Reg ID), as recorded in the list of registered participants in the Conference section of the Easygestor (which is separate from the amount recorded in the Movements section)
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$newamount=$_GET["amount"];
$regid=$_GET["id"];

$resultado = $db->callProcedure("CALL ed_pr_change_conference_amount_paid('".$newamount."','".$regid."')");

if ($resultado!=""){
echo "Success!";
} else {
echo "It didn't work.";
}
?>