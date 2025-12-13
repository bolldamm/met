<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_format_date.inc.php";

	
	//Listado de talleres
	$resultTalleres = $db->callProcedure("CALL ed_sp_web_taller_obtener_listado(".$_SESSION["id_idioma"].")");
	while($dataTaller = $db->getData($resultTalleres)) {
		$plantillaFormulario->assign("ITEM_TALLER_ID", $dataTaller["id_taller_fecha"]);
		
		$literalMember="";
		$literalFull="";
		if($dataTaller["es_socio"]==1){
			$literalMember="<span style='color:blue'> (members only) </span>";
		}
		if($dataTaller["total_inscritos"]>=$dataTaller["plazas"]){
			$literalFull="<span style='color:#8B1513'> FULL!</span>";
			$plantillaFormulario->assign("ITEM_TALLER_DISABLED","disabled='true'");
		}else{
			$plantillaFormulario->assign("ITEM_TALLER_DISABLED","");
		}
		$plantillaFormulario->assign("ITEM_TALLER_DESCRIPCION", $dataTaller["nombre"].$literalMember.$literalFull);
		
		if($dataTaller["es_socio"]==1){
			$plantillaFormulario->assign("ITEM_TALLER_MIEMBRO_REQUERIDO", STATIC_GLOBAL_BUTTON_YES);
		}else{
			$plantillaFormulario->assign("ITEM_TALLER_MIEMBRO_REQUERIDO", STATIC_GLOBAL_BUTTON_NO);
		}

		$plantillaFormulario->assign("ITEM_TALLER_PRECIO",$dataTaller["precio"]);
		$plantillaFormulario->assign("ITEM_TALLER_PRECIO_SISTER",$dataTaller["precio_asociacion"]);
		$plantillaFormulario->assign("ITEM_TALLER_PRECIO_NON_MEMBER",$dataTaller["precio_no_socio"]);
		//$plantillaFormulario->assign("ITEM_TALLER_FECHA", generalUtils::conversionFechaFormato($dataTaller["fecha"]));
		
		$fechaTaller = generalUtils::conversionFechaFormato($dataTaller["fecha"], "-", "/");
		$mesTaller = explode("/", $fechaTaller);
		
		//Proceso para obtener dia de la semana
		$fechaTrozeada=explode("-",$dataTaller["fecha"]);
		$plantillaFormulario->assign("ITEM_TALLER_FECHA", intval($fechaTrozeada[2])."&nbsp;".$vectorMes[$fechaTrozeada[1]]);

		
		$plantillaFormulario->parse("contenido_principal.item_taller");
	}
	
	$plantillaFormulario->assign("DISPLAY_BLOQUE_INVOICE","style='display:none'");


	//Valores por defecto
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_CUSTOMER","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_NAME_COMPANY","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ADDRESS","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_ZIPCODE","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_CITY","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_PROVINCE","");
	$plantillaFormulario->assign("FORM_PROFILE_BILLING_COUNTRY","");
	$plantillaFormulario->assign("FORM_GDPR_NOTE",STATIC_FORM_MEMBERSHIP_PRIVACY_NOTE_1);
	$plantillaFormulario->assign("SISTER_ASSOCIATION_TEXT",STATIC_FORM_WORKSHOP_REGISTER_SISTER_ASSOCIATION_MEMBERSHIP_TEXT_1);
	
	if(isset($_SESSION["met_user"]) && !generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"])){
		$resultadoUsuarioIndividual=$db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(".$_SESSION["met_user"]["id"].")");
		$datoUsuarioIndividual=$db->getData($resultadoUsuarioIndividual);	
		
		$idTratamientoUsuarioWeb=$datoUsuarioIndividual["id_tratamiento_usuario_web"];
		$plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME",$datoUsuarioIndividual["nombre"]);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES",$datoUsuarioIndividual["apellidos"]);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL",$datoUsuarioIndividual["correo_electronico"]);
		if($datoUsuarioIndividual["telefono_casa"]!=""){
			$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",$datoUsuarioIndividual["telefono_casa"]);
		}else{
			$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO","");
		}
		
		//Obtener los datos por defecto (billing information)
		$resultadoUsuarioConcreto=$db->callProcedure("CALL ed_sp_web_usuario_web_datos_factura_obtener(".$_SESSION["met_user"]["id"].")");
		$datoUsuarioConcreto=$db->getData($resultadoUsuarioConcreto);
		
		// Pre-populate tax ID with fallback: prefer tax_id_number, fallback to nif_cliente_factura
		$preFillTaxId = !empty($datoUsuarioConcreto["tax_id_number"])
			? $datoUsuarioConcreto["tax_id_number"]
			: ($datoUsuarioConcreto["nif_cliente_factura"] ?? "");
		if($preFillTaxId!=""){
			$plantillaFormulario->assign("FORM_PROFILE_BILLING_CUSTOMER_NIF",$preFillTaxId);
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

		//Parse hidden input with ID hdnIdLogin for use in form validation
		$plantillaFormulario->parse("contenido_principal.input_logueado");

		$ifmember='display:none;';
        $ifnotmember="";

	}else{

		$ifmember='display:block;';
        $ifnotmember="onclick='areyoumember()'";
		$idTratamientoUsuarioWeb=-1;
		$plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME","");
		$plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES","");
		$plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL","");
		$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO","");
	}

//Validation now via event listener, instead of "onchange"
$eventoAsociaciones="";
	//Combo de las asociaciones de miembros hermanas
	$plantillaFormulario->assign("COMBO_ASOCIACIONES", generalUtils::construirCombo($db, "CALL ed_sp_web_asociacion_hermana_obtener_combo()", "cmbAsociacionHermana", "cmbAsociacionHermana", -1, "descripcion", "id_asociacion_hermana", STATIC_GLOBAL_COMBO_DEFAULT, -1, 'class="form-control" style="color:slategray;"',$eventoAsociaciones));
	
	$plantillaFormulario->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(".$_SESSION["id_idioma"].")", "cmbTitulo", "cmbTitulo",$idTratamientoUsuarioWeb, "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="form-control" style="color:slategray;"'));
	
	$subPlantilla->assign("SESSION_ID", md5(uniqid(time())));
        $plantillaFormulario->assign("IFMEMBER",$ifmember);
        $plantillaFormulario->assign("IFNOTMEMBER",$ifnotmember);
	$plantilla->parse("contenido_principal.validate_workshop_registration_form");
?>