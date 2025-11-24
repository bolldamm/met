<?php
/* Resets click counter */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";
	require "../classes/generalUtils.php";

$resultado = $db->callProcedure("CALL ed_pr_delete_clicks()");

generalUtils::redirigir("/en/registration-stats:1409");

?>