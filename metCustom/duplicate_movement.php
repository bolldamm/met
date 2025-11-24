<?php
/*
duplicate a specified movement, assigning a new id_movimiento
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$movid=$_GET["movid"];

$resultado = $db->callProcedure("CALL ed_pr_duplicate_movement('".$movid."')");

if ($resultado!=""){
echo "Success!";
} else {
echo "It doesn't seem to have worked.";
}
?>