<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){

	//Combo situacion adicional
	$plantillaFormulario->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ed_sp_web_situacion_adicional_obtener_combo(".$_SESSION["id_idioma"].")", "cmbSituacionAdicional", "cmbSituacionAdicional", $_SESSION["met_user"]["id_situacion_adicional"], "nombre", "id_situacion_adicional", "Standard", -1, 'class="form-control"' ));

	//Combo billing country
	$plantillaFormulario->assign("COMBO_BILLING_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "billing_country", "billing_country", -1, "nombre_original", "id_pais", STATIC_FORM_PROFILE_BILLING_COUNTRY, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

	//Combo tax ID country (with ISO codes)
	$plantillaFormulario->assign("COMBO_TAX_ID_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo_iso()", "tax_id_country", "tax_id_country", -1, "nombre_con_iso", "id_pais", STATIC_FORM_PROFILE_BILLING_TAX_ID_COUNTRY, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

	//Combo tax ID type
	$plantillaFormulario->assign("COMBO_TAX_ID_TYPE", generalUtils::construirCombo($db, "CALL ed_sp_web_tax_id_type_obtener_combo()", "tax_id_type", "tax_id_type", -1, "description", "id_tax_id_type", STATIC_FORM_PROFILE_BILLING_TAX_ID_TYPE, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

		$plantillaFormulario->parse("contenido_principal.bloque_formulario.bloque_individual");

		/*$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_30_RENEW);
		if($_SESSION["met_user"]["id_situacion_adicional"]==SITUACION_ADICIONAL_JUBILADO){
			$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_15_RENEW);
		}*/
		
		
	}else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
		//Combo billing country
		$plantillaFormulario->assign("COMBO_BILLING_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "billing_country", "billing_country", -1, "nombre_original", "id_pais", STATIC_FORM_PROFILE_BILLING_COUNTRY, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

		//Combo tax ID country (with ISO codes)
		$plantillaFormulario->assign("COMBO_TAX_ID_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo_iso()", "tax_id_country", "tax_id_country", -1, "nombre_con_iso", "id_pais", STATIC_FORM_PROFILE_BILLING_TAX_ID_COUNTRY, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

		//Combo tax ID type
		$plantillaFormulario->assign("COMBO_TAX_ID_TYPE", generalUtils::construirCombo($db, "CALL ed_sp_web_tax_id_type_obtener_combo()", "tax_id_type", "tax_id_type", -1, "tax_id_type", "id_tax_id_type", STATIC_FORM_PROFILE_BILLING_TAX_ID_TYPE, -1, 'class="required form-control" style="width:100%; color:lightslategray;" '));

		$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_INSTIT_COST_100);
		$plantillaFormulario->parse("contenido_principal.bloque_formulario.bloque_institucion");
	}
	
	$plantillaFormulario->assign("DISPLAY_BLOQUE_INVOICE","style='display:none'");


	//Valores por defecto NOT NEEDED
/*
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER",STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY",STATIC_FORM_PROFILE_BILLING_NAME_COMPANY);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS",STATIC_FORM_PROFILE_BILLING_ADDRESS);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE",STATIC_FORM_PROFILE_BILLING_ZIPCODE);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY",STATIC_FORM_PROFILE_BILLING_CITY);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE",STATIC_FORM_PROFILE_BILLING_PROVINCE);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY",STATIC_FORM_PROFILE_BILLING_COUNTRY);
*/
	
	//Obtener los datos por defecto
	$resultadoUsuarioConcreto=$db->callProcedure("CALL ed_sp_web_usuario_web_datos_factura_obtener(".$_SESSION["met_user"]["id"].")");
	$datoUsuarioConcreto=$db->getData($resultadoUsuarioConcreto);
	
	if($datoUsuarioConcreto["nif_cliente_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",$datoUsuarioConcreto["nif_cliente_factura"]);
	}
	
	if($datoUsuarioConcreto["nombre_cliente_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER",$datoUsuarioConcreto["nombre_cliente_factura"]);
	}
	
	if($datoUsuarioConcreto["nombre_empresa_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY",$datoUsuarioConcreto["nombre_empresa_factura"]);
	}
	
	if($datoUsuarioConcreto["direccion_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS",$datoUsuarioConcreto["direccion_factura"]);
	}
	
	if($datoUsuarioConcreto["codigo_postal_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE",$datoUsuarioConcreto["codigo_postal_factura"]);
	}
	
	if($datoUsuarioConcreto["ciudad_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY",$datoUsuarioConcreto["ciudad_factura"]);
	}
	
	if($datoUsuarioConcreto["provincia_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE",$datoUsuarioConcreto["provincia_factura"]);
	}
	
	if($datoUsuarioConcreto["pais_factura"]!=""){
		$plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY",$datoUsuarioConcreto["pais_factura"]);
	}
	


	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.validate_renew_membership_form_TEST");
?>