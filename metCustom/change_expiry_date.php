<?php
/*
change the expiry date of a specified member, by changing the expiry date of a specified registration ("regid" in the script or id_inscripcion in the database) of that member
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$newdate=$_GET["newdate"];
$regid=$_GET["id"];

$resultado = $db->callProcedure("CALL ed_pr_change_expiry_date('".$newdate."','".$regid."')");

if ($resultado!=""){
echo "Success!";
} else {
echo "It doesn't seem to have worked.";
}
?>