<?php
/*
* Get an email address
* associated with 
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$RegNo=$_GET["registration_id"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_email_from_stripe_id('".$RegNo."')";
$resultado=$db->callProcedure($codeProcedure);
$row = $resultado->fetch_assoc();
$email = $row["correo_electronico"];

echo $email;

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>