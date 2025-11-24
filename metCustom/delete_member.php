<?php
/* Deletes the personal information of selected members */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$regid=$_GET["regid"];
$id=$_GET["id"];

$resultado = $db->callProcedure("CALL ed_pr_delete_member('".$regid."','".$id."')");

if ($resultado!=""){
echo "Success!";
} else {
echo "It didn't work.";
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";
?>