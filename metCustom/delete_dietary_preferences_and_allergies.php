<?php
/* Deletes dietary preferences and allergies for gdpr purposes */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$resultado = $db->callProcedure("CALL ed_pr_delete_dietary_preferences_and_allergies()");

if ($resultado!=""){
echo "Success!";
} else {
echo "It didn't work.";
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";
?>