<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	//Combo paises
	$plantillaFormulario->assign("COMBO_PAIS", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbPais", "cmbPais", -1, "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE, -1, 'class="inputText left required" style="width:284px;"'));


	
	$plantillaFormulario->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(".$_SESSION["id_idioma"].")", "cmbTitulo", "cmbTitulo", -1, "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="inputText left" style="width:63px;"'));
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	
	$plantillaFormulario->assign("USUARIO_MODALIDAD",MODALIDAD_USUARIO_INSTITUTIONAL);
	$plantillaFormulario->assign("DISPLAY_BLOQUE_INVOICE","style='display:none'");
	
	$plantilla->parse("contenido_principal.validar_membership_institucion");
?>