<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del menu
		$idMovimiento=$_POST["hdnIdMovimiento"];
		
		//Iniciar transaccion
		$db->startTransaction();
		$fechaFactura="null";
		$fechaPagoFactura="null";
		if($_POST["txtFechaFactura"]!=""){
			$fechaFactura='"'.generalUtils::conversionFechaFormato($_POST["txtFechaFactura"]).'"';
		}
		if($_POST["txtFechaPagoFactura"]!=""){
			$fechaPagoFactura='"'.generalUtils::conversionFechaFormato($_POST["txtFechaPagoFactura"]).'"';
		}		
		

		
		//Guardamos factura...
		$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_guardar(".$idMovimiento.",".$_SESSION["user"]["id"].",".$fechaFactura.",".$fechaPagoFactura.",'".generalUtils::escaparCadena($_POST["txtNumeroFactura"])."','".generalUtils::escaparCadena($_POST["txtNif"])."','".generalUtils::escaparCadena($_POST["txtNombreCliente"])."','".generalUtils::escaparCadena($_POST["txtNombreEmpresa"])."','".generalUtils::escaparCadena($_POST["txtDireccion"])."','".generalUtils::escaparCadena($_POST["txtCodigoPostal"])."','".generalUtils::escaparCadena($_POST["txtCiudad"])."','".generalUtils::escaparCadena($_POST["txtProvincia"])."','".generalUtils::escaparCadena($_POST["txtPais"])."')");		
		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];
	
		//Linea factura
		$resultadoLineaFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_guardar(".$idFactura.",".$idMovimiento.",'".$_POST["txtImporte"]."')");

		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}

	

	if(!isset($_GET["id_movimiento"]) || !is_numeric($_GET["id_movimiento"])){
		generalUtils::redirigir("main_app.php?section=movement&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/movement/invoice/manage_invoice.html");
	
	
	//Sacamos la informacion del menu en cada idioma
	$resultadoMovimiento=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_obtener_concreta(".$_GET["id_movimiento"].",".$_SESSION["user"]["language_id"].")");
	$datoMovimiento=$db->getData($resultadoMovimiento);
	$conceptoNombre=$datoMovimiento["concepto"];

	//Datos de la factura
	
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_movimiento_obtener_concreto(".$_GET["id_movimiento"].")");
	//Si hay datos, los mostramos por pantalla...
	if($datoFactura=$db->getData($resultadoFactura)){
		if($datoFactura["fecha_factura"]!=""){
			$datoFactura["fecha_factura"]=generalUtils::conversionFechaFormato($datoFactura["fecha_factura"]);
		}
		if($datoFactura["fecha_pago_factura"]!=""){
			$datoFactura["fecha_pago_factura"]=generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"]);
		}
		$subPlantilla->assign("FACTURA_FECHA",$datoFactura["fecha_factura"]);
		$subPlantilla->assign("FACTURA_FECHA_PAGO",$datoFactura["fecha_pago_factura"]);
		$subPlantilla->assign("FACTURA_NUMERO_FACTURA",$datoFactura["numero_factura"]);
		$subPlantilla->assign("FACTURA_NIF_CLIENTE",$datoFactura["nif_cliente_factura"]);
		$subPlantilla->assign("FACTURA_NOMBRE_EMPRESA",$datoFactura["nombre_empresa_factura"]);
		$subPlantilla->assign("FACTURA_NOMBRE_CLIENTE",$datoFactura["nombre_cliente_factura"]);
		$subPlantilla->assign("FACTURA_DIRECCION",$datoFactura["direccion_factura"]);
		$subPlantilla->assign("FACTURA_CODIGO_POSTAL",$datoFactura["codigo_postal_factura"]);
		$subPlantilla->assign("FACTURA_CIUDAD",$datoFactura["ciudad_factura"]);
		$subPlantilla->assign("FACTURA_PROVINCIA",$datoFactura["provincia_factura"]);
		$subPlantilla->assign("FACTURA_PAIS",$datoFactura["pais_factura"]);
		$subPlantilla->assign("FACTURA_IMPORTE",$datoFactura["precio"]);
		$subPlantilla->assign("ID_INVOICE",$datoFactura["id_factura"]);
		
		//Parseamos boton generar pdf
		$subPlantilla->parse("contenido_principal.boton_pdf_factura");
		if($datoFactura["hash_generado"]){
			$subPlantilla->assign("HASH_FACTURA",$datoFactura["hash_generado"]);
			$subPlantilla->parse("contenido_principal.boton_download_factura");
		}
		
	}else{
		$subPlantilla->assign("FACTURA_IMPORTE",$datoMovimiento["importe"]);
		if($datoMovimiento["id_usuario_web"]!=""){
			$resultadoUsuarioConcreto=$db->callProcedure("CALL ed_sp_usuario_web_obtener_concreto(".$datoMovimiento["id_usuario_web"].",".$_SESSION["id_idioma"].")");
			$datoUsuarioConcreto=$db->getData($resultadoUsuarioConcreto);

			$subPlantilla->assign("FACTURA_NIF_CLIENTE",$datoUsuarioConcreto["nif_cliente_factura"]);
			$subPlantilla->assign("FACTURA_NOMBRE_EMPRESA",$datoUsuarioConcreto["nombre_cliente_factura"]);
			$subPlantilla->assign("FACTURA_NOMBRE_CLIENTE",$datoUsuarioConcreto["nombre_empresa_factura"]);
			$subPlantilla->assign("FACTURA_DIRECCION",$datoUsuarioConcreto["direccion_factura"]);
			$subPlantilla->assign("FACTURA_CODIGO_POSTAL",$datoUsuarioConcreto["codigo_postal_factura"]);
			$subPlantilla->assign("FACTURA_CIUDAD",$datoUsuarioConcreto["ciudad_factura"]);
			$subPlantilla->assign("FACTURA_PROVINCIA",$datoUsuarioConcreto["provincia_factura"]);
			$subPlantilla->assign("FACTURA_PAIS",$datoUsuarioConcreto["pais_factura"]);
		}

	}

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_MOVEMENT_VIEW_MOVEMENT_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_MOVEMENT_EDIT_MOVEMENT_LINK."&id_movimiento=".$_GET["id_movimiento"];
	$vectorMigas[2]["texto"]=$conceptoNombre;
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_INVOICE_EDIT_INVOICE_LINK."&id_movimiento=".$_GET["id_movimiento"];
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_INVOICE_EDIT_INVOICE_TEXT;

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_MOVIMIENTO",$_GET["id_movimiento"]);
	$subPlantilla->assign("ACTION","edit");

	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaFactura");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaPagoFactura");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Submenu
	$subPlantilla->parse("contenido_principal.item_submenu");
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>