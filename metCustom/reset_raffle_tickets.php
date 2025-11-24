<?php
/* Resets raffle tickets */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

$resultado = $db->callProcedure("CALL ed_pr_reset_raffle_tickets($idConferencia)");

echo "Success!";
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>