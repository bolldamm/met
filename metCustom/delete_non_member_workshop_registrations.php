<?php
/* Deletes non member workshop registrations for gdpr purposes */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

// $regid=$_GET["regid"];

$resultado = $db->callProcedure("CALL ed_pr_delete_non_member_workshop()");

if ($resultado!=""){
echo "Success!";
} else {
echo "It didn't work.";
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";
?>