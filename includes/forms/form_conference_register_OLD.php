<?php
	/**
	 * 
	 * Presentamos por pantalla este formulario
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	//Combo de las asociaciones hermanas
	$plantillaFormulario->assign("COMBO_ASOCIACIONES", generalUtils::construirCombo($db, "CALL ed_sp_web_asociacion_hermana_obtener_combo()", "cmbAsociacionHermana", "cmbAsociacionHermana", -1, "descripcion", "id_asociacion_hermana", STATIC_GLOBAL_COMBO_DEFAULT, -1, "onchange='reinicializarPrecioTotal()'" ,'class="inputText left" style="width:auto;"'));

        $plantillaFormulario->parse("contenido_principal.bloque_sister_association");

	//Obtenemos el conference de esta temporada
	$resultadoConferencia=$db->callProcedure("CALL ed_sp_web_conferencia_actual()");
	
	//Obtenemos precio base segun early or not
	$datoConferencia=$db->getData($resultadoConferencia);

	/************ INICIO: MIEMBRO O NO MIEMBRO ************/

	if(isset($_SESSION["met_user"])){
		//Aun estamos en early date
		if($datoConferencia["es_early"]<=0){
			$precioBase=$datoConferencia["precio_early_completo"];
			$precioBaseSinDesayuno=$datoConferencia["precio_early_basico"];
		}else{
			//Ya estamos en late date
			$precioBase=$datoConferencia["precio_late_completo"];
			$precioBaseSinDesayuno=$datoConferencia["precio_late_basico"];
		}//end else
		
		//Diferencia descuento
		$precioBaseDescuento=$precioBase-$datoConferencia["precio_descuento_completo"];
		$ifmember='display:none;';
	}else{
		$ifmember='display:block;';
		//Aun estamos en early date
		if($datoConferencia["es_early"]<=0){
			$precioBase=$datoConferencia["precio_no_socio_early_completo"];
			$precioBaseSinDesayuno=$datoConferencia["precio_no_socio_early_basico"];
		}else{
			//Ya estamos en late date
			$precioBase=$datoConferencia["precio_no_socio_late_completo"];
			$precioBaseSinDesayuno=$datoConferencia["precio_no_socio_late_basico"];
		}
		
		//Diferencia descuento
		$precioBaseDescuento=$precioBase-$datoConferencia["precio_no_socio_descuento_completo"];
	}//end else
	
	
	//Diferencia descuento desayuno
	$precioDesayunoDescuento=$precioBase-$precioBaseSinDesayuno;


	$plantillaFormulario->assign("FORM_CONFERENCE_PRECIO_TOTAL",sprintf("%.0f",$precioBase));
	$plantillaFormulario->assign("SPEAKER_IMPORTE_DESCUENTO",$precioBaseDescuento);
	$plantillaFormulario->assign("DESAYUNO_IMPORTE_DESCUENTO",$precioDesayunoDescuento);
	
	
	
	/************ FIN: MIEMBRO O NO MIEMBRO ************/
	
	
	
	/************ INICIO: EXCLUSIVO PARA ASOCIACIONES ************/
	#Aun estamos en early date
	if($datoConferencia["es_early"]<=0){
		$precioBaseAsociacion=$datoConferencia["precio_asociacion_early_completo"];
		$precioBaseSinDesayunoAsociacion=$datoConferencia["precio_asociacion_early_basico"];
	}else{
		//Ya estamos en late date
		$precioBaseAsociacion=$datoConferencia["precio_asociacion_late_completo"];
		$precioBaseSinDesayunoAsociacion=$datoConferencia["precio_asociacion_late_basico"];
	}//end else
	

	
	//Diferencia descuento
	$precioBaseDescuentoAsociacion=$precioBaseAsociacion-$datoConferencia["precio_asociacion_descuento_completo"];
	
	
	//Diferencia descuento desayuno
	$precioDesayunoDescuentoAsociacion=$precioBaseAsociacion-$precioBaseSinDesayunoAsociacion;
	
	$plantillaFormulario->assign("FORM_CONFERENCE_PRECIO_TOTAL_ASOCIACION",$precioBaseAsociacion);
	$plantillaFormulario->assign("SPEAKER_IMPORTE_DESCUENTO_ASOCIACION",$precioBaseDescuentoAsociacion);
	$plantillaFormulario->assign("DESAYUNO_IMPORTE_DESCUENTO_ASOCIACION",$precioDesayunoDescuentoAsociacion);
	
	/************ FIN: EXCLUSIVO PARA ASOCIACIONES ************/
	
	
	
	
	require "includes/load_format_date.inc.php";
		
	//Obtenemos workshops de conferencia actuales
	$resultadoTallerConferencia=$db->callProcedure("CALL ed_sp_web_taller_conferencia_obtener_concreto(".$_SESSION["id_idioma"].",-1)");
	
	$fechaActual="";
	$esMini=false;
	while($datoTallerConferencia=$db->getData($resultadoTallerConferencia)){
		
		if($fechaActual!="" && $fechaActual!=$datoTallerConferencia["fecha"]){
			
			$fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
			$mesTaller = explode("/", $fechaTaller);
		
			//Proceso para obtener dia de la semana
			$fechaTrozeada=explode("-",$fechaActual);
			
			
			
			$fechaTimeStamp=mktime(0,0,0,$mesTaller[1],$mesTaller[0],$mesTaller[2]);
			$diaSemana=$vectorSemana[date("N",$fechaTimeStamp)];
			
			$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE", $diaSemana.", ".intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]]);
			
			//Miramos si hay mini...
			if($esMini){
				$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
			}//end if
			
			//$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",$fechaActual);
			$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller");
			$esMini=false;
		}
		
		$literalFull="";
		
		if($datoTallerConferencia["total_inscritos"]>=$datoTallerConferencia["plazas"]){
			$literalFull="<span style='color:#8B1513'> FULL!</span>";
			$plantillaFormulario->assign("ITEM_TALLER_DISABLED","disabled='true'");
		}else{
			$plantillaFormulario->assign("ITEM_TALLER_DISABLED","");
		}
		
		$plantillaFormulario->assign("CONFERENCIA_TALLER",$datoTallerConferencia["nombre"].$literalFull);
		$plantillaFormulario->assign("CONFERENCIA_FECHA",$datoTallerConferencia["fecha"]);
		$plantillaFormulario->assign("CONFERENCIA_ID_TALLER_FECHA",$datoTallerConferencia["id_taller_fecha"]);
		$plantillaFormulario->assign("CONFERENCIA_ID_TALLER",$datoTallerConferencia["id_taller"]);
		$plantillaFormulario->assign("CONFERENCIA_PRECIO_TALLER",$datoTallerConferencia["id_taller_fecha"]);
		
		//Precios
		$plantillaFormulario->assign("CONFERENCIA_PRECIO_MIEMBRO_TALLER_FECHA",$datoTallerConferencia["precio"]);
		$plantillaFormulario->assign("CONFERENCIA_PRECIO_ASOCIACION_TALLER_FECHA",$datoTallerConferencia["precio_asociacion"]);
		$plantillaFormulario->assign("CONFERENCIA_PRECIO_NO_MIEMBRO_ID_TALLER_FECHA",$datoTallerConferencia["precio_no_socio"]);

		if($datoTallerConferencia["es_mini"]==1){
			$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini.fila_conferencia_taller_mini");
			$esMini=true;
		}else{
			$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.fila_conferencia_taller");
		}
		

		$fechaActual=$datoTallerConferencia["fecha"];
	}
	
	if($fechaActual!=""){
		$fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
		$mesTaller = explode("/", $fechaTaller);
	
		//Proceso para obtener dia de la semana
		$fechaTrozeada=explode("-",$fechaActual);
		$fechaTimeStamp=mktime(0,0,0,$mesTaller[1],$mesTaller[0],$mesTaller[2]);
		$diaSemana=$vectorSemana[date("N",$fechaTimeStamp)];
		
		$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE", $diaSemana.", ".intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]]);
		
		//Miramos si hay mini...
		if($esMini){
			$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
		}//end if
		
		//$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",$fechaActual);
		$plantillaFormulario->parse("contenido_principal.bloque_fecha_taller");
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
	
	
		
		
	if(isset($_SESSION["met_user"])){
		$resultadoImagen=$db->callProcedure("CALL ed_sp_obtener_imagen(".$_SESSION["met_user"]["id"].")");
		$datoImagen=$db->getData($resultadoImagen);
		$imagen_miembro="http://metmeetings.org/files/members/thumb/".$datoImagen["imagen"];
		
		$resultadoUsuarioIndividual=$db->callProcedure("CALL ed_sp_web_usuario_web_individual_obtener_concreto(".$_SESSION["met_user"]["id"].")");
		
		$datoUsuarioIndividual=$db->getData($resultadoUsuarioIndividual);	
	
		$idTratamientoUsuarioWeb=$datoUsuarioIndividual["id_tratamiento_usuario_web"];
		$plantillaFormulario->assign("FORM_MEMBERSHIP_IMAGE",$imagen_miembro);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME",$datoUsuarioIndividual["nombre"]);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES",$datoUsuarioIndividual["apellidos"]);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL",$datoUsuarioIndividual["correo_electronico"]);
		if($datoUsuarioIndividual["telefono_casa"]!=""){
			$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",$datoUsuarioIndividual["telefono_casa"]);
		}else{
			
		
			$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO);
		}
		
		
		//Obtener los datos por defecto (billing information)
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

		//Input login
		$plantillaFormulario->parse("contenido_principal.input_logueado");
		
	}else{
		$idTratamientoUsuarioWeb=-1;
		$plantillaFormulario->assign("FORM_MEMBERSHIP_FIRST_NAME",STATIC_FORM_MEMBERSHIP_FIRST_NAME);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_LAST_NAMES",STATIC_FORM_MEMBERSHIP_LAST_NAMES);
		$plantillaFormulario->assign("FORM_MEMBERSHIP_EMAIL",STATIC_FORM_MEMBERSHIP_EMAIL);
		$plantillaFormulario->assign("FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO",STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO);

			$imagen_miembro="http://metmeetings.org/files/members/default.jpg";
		$plantillaFormulario->assign("FORM_MEMBERSHIP_IMAGE",$imagen_miembro);
			
		//Combo paises
		$plantillaFormulario->assign("COMBO_PAIS", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbPais", "cmbPais", -1, "nombre_original", "id_pais", STATIC_FORM_MEMBERSHIP_COUNTRY_OF_RESIDENCE, -1, 'class="inputText left required" style="width:283px;"'));

		$plantillaFormulario->parse("contenido_principal.bloque_pais");
		$plantilla->parse("contenido_principal.validar_conference_register.validacion_pais");
	}
	
		
		
	
	
	
	//Combo guest
	$matrizOrden[0]["descripcion"]=0;
	$matrizOrden[1]["descripcion"]=1;
	$matrizOrden[2]["descripcion"]=2;
	$matrizOrden[3]["descripcion"]=3;

	
	$plantillaFormulario->assign("COMBO_GUEST",generalUtils::construirComboMatriz($matrizOrden,"cmbInvitados","cmbInvitados",-1,0,-1,"",'onclick="reinicializarPrecioTotal()" class="inputText" style="width:63px;"'));
	
	$plantillaFormulario->assign("COMBO_TITULOS", generalUtils::construirCombo($db, "CALL ed_sp_web_tratamiento_usuario_web_obtener_combo(".$_SESSION["id_idioma"].")", "cmbTitulo", "cmbTitulo",$idTratamientoUsuarioWeb, "nombre", "id_tratamiento_usuario_web", STATIC_FORM_MEMBERSHIP_TITLE, -1, 'class="inputText left" style="width:63px;"'));
	
	$subPlantilla->assign("SESSION_ID", md5(uniqid(time())));
$plantillaFormulario->assign("IFMEMBER",$ifmember);
	$plantilla->parse("contenido_principal.validar_conference_register");
	
	
	
?>

