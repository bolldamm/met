<?php
/* Unregisters the selected METM attendee */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";
	require "../classes/generalUtils.php";

$regid=$_POST["regid"];

$db->callProcedure("CALL ed_pr_unset_conference_registered('".$regid."')");

generalUtils::redirigir("https://www.metmeetings.org/en/their-metm:1410");
?>