<?php
	require "../includes/load_main_components.inc.php";
	
	$esValido=true;
		
	
	switch($_GET["c"]){
		case 1:
			//Obtenemos un subconcepto
			echo generalUtils::construirCombo($db, "CALL ed_sp_web_concepto_movimiento_obtener_combo(".$_GET["id"].",".$_SESSION["id_idioma"].")", "cmbSubConcepto", "cmbSubConcepto", -1, "nombre", "id_concepto_movimiento", STATIC_EXPENSE_FORM_SUBTYPE_EXPENSE, -1,"","class='form-control required'");
			break;
	}
?>