<?php
/*
* Change way conference options page is displayed
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";
$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
$row = $result->fetch_assoc();
$idConferencia = $row["current_id_conferencia"];

$dinner=$_GET["dinner"];
if (!$dinner) {
  $dinner=0;
  }
$incompatibility=$_GET["incompatibility"];
if (!$incompatibility) {
  $incompatibility=0;
  }
$offmetm=$_GET["offmetm"];
if (!$offmetm) {
  $offmetm=0;
  }
$freeze=$_GET["freeze"];
if (!$freeze) {
  $freeze=0;
  }
$choir=$_GET["choir"];
if (!$choir) {
  $choir=0;
  }
$raffle=$_GET["raffle"];
if (!$raffle) {
  $raffle=0;
  }
$catchup=$_GET["catchup"];
if (!$catchup) {
  $catchup=0;
  }
$lastminute=$_GET["lastminute"];
if (!$lastminute) {
  $lastminute=0;
  }

$db->callProcedure("CALL ed_pr_set_conference_freeze_data($dinner,$offmetm,$incompatibility,$freeze,$idConferencia,$choir,$raffle,$catchup,$lastminute)");

// generalUtils::redirigir("../conference_backoffice.php");
generalUtils::redirigir("https://www.metmeetings.org/en/conference-back-office:1292");
?>