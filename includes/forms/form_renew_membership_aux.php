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
	$plantillaFormulario->assign("COMBO_SITUACION_ADICIONAL", generalUtils::construirCombo($db, "CALL ed_sp_web_situacion_adicional_obtener_combo(".$_SESSION["id_idioma"].")", "cmbSituacionAdicional", "cmbSituacionAdicional", $_SESSION["met_user"]["id_situacion_adicional"], "nombre", "id_situacion_adicional", STATIC_GLOBAL_COMBO_DEFAULT, -1, 'class="inputText"' ));
		
		
		$plantillaFormulario->parse("contenido_principal.bloque_formulario.bloque_individual");

		/*$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_30_RENEW);
		if($_SESSION["met_user"]["id_situacion_adicional"]==SITUACION_ADICIONAL_JUBILADO){
			$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_MEMBERSHIP_COSTS_15_RENEW);
		}*/
		
		
	}else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
		$plantillaFormulario->assign("LITERAL_PRECIO_RENOVACION",STATIC_FORM_MEMBERSHIP_INSTIT_COST_100);
		$plantillaFormulario->parse("contenido_principal.bloque_formulario.bloque_institucion");
	}
	
	$plantillaFormulario->assign("DISPLAY_BLOQUE_INVOICE","style='display:none'");


	//Valores por defecto
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER",STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY",STATIC_FORM_PROFILE_BILLING_NAME_COMPANY);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS",STATIC_FORM_PROFILE_BILLING_ADDRESS);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE",STATIC_FORM_PROFILE_BILLING_ZIPCODE);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY",STATIC_FORM_PROFILE_BILLING_CITY);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE",STATIC_FORM_PROFILE_BILLING_PROVINCE);
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY",STATIC_FORM_PROFILE_BILLING_COUNTRY);
	
	
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
	$plantilla->parse("contenido_principal.validar_membership_renew");
?>