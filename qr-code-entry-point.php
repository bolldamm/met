<?php

	require "includes/load_main_components.inc.php";
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];

	if ($_SESSION["conference_user"]) {
      $idUser = $_SESSION["conference_user"];
    } else {
      $idUser = 0;
    }

//<html>
//  <p>This page will automatically redirect to any MET website page we like and count the number of times the QR code is used. That way we can see if it is worth including a QR code in future.</p>
// </html>

$db->callProcedure("CALL ed_pr_add_click($idUser,$idConferencia)");
generalUtils::redirigir(STATIC_METM_PROGRAMME);
?>
