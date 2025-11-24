<?php
/*
* Display conference options for a particular attendee
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

session_start();

$_SESSION["conference_user"]=$_GET["cmbAttendee"];

// echo $_SESSION["conference_user"];

generalUtils::redirigir("https://www.metmeetings.org/en/conference-back-office:1292#DisplayChoices");
?>