<?php
/*
* Deletes a new member who fails to pay
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$email=$_GET["email"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_delete_failed_member('".$email."')";
$resultado=$db->callProcedure($codeProcedure);

echo "Failed member with email ";
echo $email;
echo " succesfully deleted.";
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>