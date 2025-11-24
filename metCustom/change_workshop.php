<?php
/* Moves an attendee to a different workshop */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$regid=$_GET["regid"];
$id=$_GET["id"];
$fromws=$_GET["fromws"];

// echo "CALL ed_pr_change_workshop(".$id.",".$regid.")";

// $resultado = $db->callProcedure("CALL ed_pr_change_workshop(".$id.",".$regid.")");
$resultado = $db->callProcedure("CALL ed_pr_change_workshop(".$id.",".$regid.",".$fromws.")");

if ($resultado!=""){
echo "Success!";
} else {
echo "It didn't work.";
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";
?>