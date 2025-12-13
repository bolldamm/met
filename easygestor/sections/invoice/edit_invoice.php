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
		$idFactura=$_POST["hdnIdFactura"];
		$borrar=1;
		
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
		
		$visibleNombreCliente=1;
		if(!isset($_POST["chkVisibleNombreCliente"])){
			$visibleNombreCliente=0;
		}
		$visibleNombreEmpresa=1;
		if(!isset($_POST["chkVisibleNombreEmpresa"])){
			$visibleNombreEmpresa=0;
		}
//		$esProforma=1;
//		if(!isset($_POST["checkProformaFactura"])){
//			$esProforma=0;
//		}
		

		//Get Verifactu tax ID fields from form
		$taxIdCountry = isset($_POST["cmbTaxIdCountry"]) ? generalUtils::escaparCadena($_POST["cmbTaxIdCountry"]) : "";
		$taxIdType = isset($_POST["cmbTaxIdType"]) ? generalUtils::escaparCadena($_POST["cmbTaxIdType"]) : "";
		$taxIdNumber = isset($_POST["txtTaxIdNumber"]) ? generalUtils::escaparCadena($_POST["txtTaxIdNumber"]) : "";
		$tipoFacturaVerifactu = isset($_POST["cmbTipoFacturaVerifactu"]) ? generalUtils::escaparCadena($_POST["cmbTipoFacturaVerifactu"]) : "";

		// Consolidate tax ID: if txtTaxIdNumber is empty but txtNif has a value, use txtNif
		// This ensures tax_id_number is always populated for edited invoices
		$legacyNif = isset($_POST["txtNif"]) ? generalUtils::escaparCadena($_POST["txtNif"]) : "";
		if (empty($taxIdNumber) && !empty($legacyNif)) {
			$taxIdNumber = $legacyNif;
		}

		//Guardamos factura...
		$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_editar(".$idFactura.",".$fechaFactura.",".$fechaPagoFactura.",'".generalUtils::escaparCadena($_POST["txtNumeroFactura"])."','".generalUtils::escaparCadena($_POST["txtNif"])."','".generalUtils::escaparCadena($_POST["txtNombreCliente"])."',".$visibleNombreCliente.",'".generalUtils::escaparCadena($_POST["txtNombreEmpresa"])."',".$visibleNombreEmpresa.",'".generalUtils::escaparCadena($_POST["txtDireccion"])."','".generalUtils::escaparCadena($_POST["txtCodigoPostal"])."','".generalUtils::escaparCadena($_POST["txtCiudad"])."','".generalUtils::escaparCadena($_POST["txtProvincia"])."','".generalUtils::escaparCadena($_POST["txtPais"])."','".$_POST["checkProformaFactura"]."','".$taxIdCountry."','".$taxIdType."','".$taxIdNumber."','".$tipoFacturaVerifactu."')");
		require "line_invoice.php";


		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=invoice&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=invoice&action=edit&id_factura=".$idFactura);
		}
	}

	

	if(!isset($_GET["id_factura"]) || !is_numeric($_GET["id_factura"])){
		generalUtils::redirigir("main_app.php?section=invoice&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/invoice/manage_invoice.html");
	
	

	//Datos de la factura
	
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto(".$_GET["id_factura"].")");
	//Si hay datos, los mostramos por pantalla...
	$datoFactura=$db->getData($resultadoFactura);
	if($datoFactura["fecha_factura"]!=""){
		$datoFactura["fecha_factura"]=generalUtils::conversionFechaFormato($datoFactura["fecha_factura"]);
	}
	if($datoFactura["fecha_pago_factura"]!=""){
		$datoFactura["fecha_pago_factura"]=generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"]);
	}
	$subPlantilla->assign("FACTURA_FECHA",$datoFactura["fecha_factura"]);
	$subPlantilla->assign("FACTURA_FECHA_PAGO",$datoFactura["fecha_pago_factura"]);
	$subPlantilla->assign("FACTURA_NUMERO_FACTURA",$datoFactura["numero_factura"]);
	// Display tax ID with fallback: prefer tax_id_number, fallback to nif_cliente_factura for old invoices
	$displayNif = !empty($datoFactura["tax_id_number"])
		? $datoFactura["tax_id_number"]
		: ($datoFactura["nif_cliente_factura"] ?? "");
	$subPlantilla->assign("FACTURA_NIF_CLIENTE", $displayNif);
	$subPlantilla->assign("FACTURA_NOMBRE_EMPRESA",$datoFactura["nombre_empresa_factura"]);
	$subPlantilla->assign("FACTURA_NOMBRE_CLIENTE",$datoFactura["nombre_cliente_factura"]);
	$subPlantilla->assign("FACTURA_DIRECCION",$datoFactura["direccion_factura"]);
	$subPlantilla->assign("FACTURA_CODIGO_POSTAL",$datoFactura["codigo_postal_factura"]);
	$subPlantilla->assign("FACTURA_CIUDAD",$datoFactura["ciudad_factura"]);
	$subPlantilla->assign("FACTURA_PROVINCIA",$datoFactura["provincia_factura"]);
	$subPlantilla->assign("FACTURA_PAIS",$datoFactura["pais_factura"]);
	$subPlantilla->assign("FACTURA_IMPORTE",isset($datoFactura["precio"]) ? $datoFactura["precio"] : "");
	$subPlantilla->assign("ID_INVOICE",$datoFactura["id_factura"]);

	//Verifactu tax ID fields - dropdown selects
	$subPlantilla->assign("FACTURA_TAX_ID_NUMBER", isset($datoFactura["tax_id_number"]) ? $datoFactura["tax_id_number"] : "");

	//Combo tax ID country - uses ISO2 code as value
	$selectedCountry = isset($datoFactura["tax_id_country"]) ? $datoFactura["tax_id_country"] : "";
	$subPlantilla->assign("COMBO_TAX_ID_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_obtener_combo()", "cmbTaxIdCountry", "cmbTaxIdCountry", $selectedCountry, "nombre_original", "iso2", "-- Country --", -1, 'style="width:150px;"'));

	//Combo tax ID type
	$selectedType = isset($datoFactura["tax_id_type"]) ? $datoFactura["tax_id_type"] : "";
	$subPlantilla->assign("COMBO_TAX_ID_TYPE", generalUtils::construirCombo($db, "CALL ed_sp_web_tax_id_type_obtener_combo()", "cmbTaxIdType", "cmbTaxIdType", $selectedType, "description", "tax_id_type", "-- ID Type --", -1, 'style="width:150px;"'));

	//Combo Verifactu invoice type - full list matching create_invoice.php
	$tipoFacturaValue = isset($datoFactura["tipo_factura_verifactu"]) ? $datoFactura["tipo_factura_verifactu"] : "F1";
	$comboTipoFactura = '<select name="cmbTipoFacturaVerifactu" id="cmbTipoFacturaVerifactu" style="width:350px;">';
	$comboTipoFactura .= '<option value="F1"' . ($tipoFacturaValue == "F1" ? " selected" : "") . '>F1 - Standard Invoice</option>';
	$comboTipoFactura .= '<option value="F2"' . ($tipoFacturaValue == "F2" ? " selected" : "") . '>F2 - Simplified Invoice</option>';
	$comboTipoFactura .= '<option value="F3"' . ($tipoFacturaValue == "F3" ? " selected" : "") . '>F3 - Invoice replacing simplified invoices</option>';
	$comboTipoFactura .= '<optgroup label="Rectificativa (Corrective Invoices)">';
	$comboTipoFactura .= '<option value="R1"' . ($tipoFacturaValue == "R1" ? " selected" : "") . '>R1 - Rectificativa (Art. 80.1, 80.2, 80.6 LIVA)</option>';
	$comboTipoFactura .= '<option value="R2"' . ($tipoFacturaValue == "R2" ? " selected" : "") . '>R2 - Rectificativa (Art. 80.3 LIVA - Bankruptcy)</option>';
	$comboTipoFactura .= '<option value="R3"' . ($tipoFacturaValue == "R3" ? " selected" : "") . '>R3 - Rectificativa (Art. 80.4 LIVA - Bad debt)</option>';
	$comboTipoFactura .= '<option value="R4"' . ($tipoFacturaValue == "R4" ? " selected" : "") . '>R4 - Rectificativa (Other causes)</option>';
	$comboTipoFactura .= '<option value="R5"' . ($tipoFacturaValue == "R5" ? " selected" : "") . '>R5 - Rectificativa in simplified invoices</option>';
	$comboTipoFactura .= '</optgroup>';
	$comboTipoFactura .= '</select>';
	$subPlantilla->assign("COMBO_TIPO_FACTURA_VERIFACTU", $comboTipoFactura);

	//Verifactu status display (only show if invoice has been processed)
	if (!empty($datoFactura["verifactu_uuid"]) || !empty($datoFactura["verifactu_status"])) {
		$estado = isset($datoFactura["verifactu_status"]) ? $datoFactura["verifactu_status"] : "pending";
		switch ($estado) {
			case "anulada":
				$subPlantilla->assign("VERIFACTU_STATUS_TEXT", "Anulada (Cancelled)");
				$subPlantilla->assign("VERIFACTU_STATUS_STYLE", "color:#dc3545;");
				break;
			case "error":
				$subPlantilla->assign("VERIFACTU_STATUS_TEXT", "Error");
				$subPlantilla->assign("VERIFACTU_STATUS_STYLE", "color:#dc3545;");
				break;
			case "confirmed":
			case "Correcto":
				$subPlantilla->assign("VERIFACTU_STATUS_TEXT", "Confirmed");
				$subPlantilla->assign("VERIFACTU_STATUS_STYLE", "color:#28a745;");
				break;
			default:
				$subPlantilla->assign("VERIFACTU_STATUS_TEXT", "Pending");
				$subPlantilla->assign("VERIFACTU_STATUS_STYLE", "color:#ffc107;");
		}

		// Show UUID if available
		if (!empty($datoFactura["verifactu_uuid"])) {
			$subPlantilla->assign("VERIFACTU_UUID", $datoFactura["verifactu_uuid"]);
			$subPlantilla->parse("contenido_principal.verifactu_status_section.verifactu_uuid_display");
		}

		// Show error message if available
		if (!empty($datoFactura["verifactu_error"])) {
			$subPlantilla->assign("VERIFACTU_ERROR", $datoFactura["verifactu_error"]);
			$subPlantilla->parse("contenido_principal.verifactu_status_section.verifactu_error_display");
		}

		$subPlantilla->parse("contenido_principal.verifactu_status_section");
	}

	//Parseamos boton generar pdf
	$subPlantilla->parse("contenido_principal.boton_pdf_factura");
	if($datoFactura["hash_generado"]){
		$subPlantilla->assign("HASH_FACTURA",$datoFactura["hash_generado"]);
		$subPlantilla->parse("contenido_principal.boton_download_factura");
	}

	//Show "Anular Verifacti" button only if invoice was submitted and not already cancelled
	if(!empty($datoFactura["verifactu_uuid"]) &&
	   (!isset($datoFactura["verifactu_status"]) || $datoFactura["verifactu_status"] != "anulada")){
		$subPlantilla->parse("contenido_principal.boton_anular_verifacti");
	}

	//Show "Refresh Verifacti" button if invoice has been submitted (has UUID)
	if(!empty($datoFactura["verifactu_uuid"])){
		$subPlantilla->parse("contenido_principal.boton_refresh_verifacti");
	}
	
	if($datoFactura["visible_nombre_cliente_factura"]){
		$subPlantilla->assign("CHECKED_NOMBRE_CLIENTE","checked");
	}
	
	if($datoFactura["visible_nombre_empresa_factura"]){
		$subPlantilla->assign("CHECKED_NOMBRE_EMPRESA","checked");
	}
	if ($datoFactura["proforma"]==1){
		$subPlantilla->assign("CHECKED_FACTURA_PROFORMA1","checked");
		} elseif ($datoFactura["proforma"]==2){
		$subPlantilla->assign("CHECKED_FACTURA_PROFORMA2","checked");
		} else {
		$subPlantilla->assign("CHECKED_FACTURA_PROFORMA","checked");
		}
	


	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_INVOICE_EDIT_INVOICE_LINK."&id_factura=".$_GET["id_factura"];
	$vectorMigas[2]["texto"]=$datoFactura["numero_factura"];

	
	//Combo tipo
	$subPlantilla->assign("COMBO_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_movimiento_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",-1,"nombre","id_tipo_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,""));

	//Combo tipo pago
	$subPlantilla->assign("COMBO_TIPO_PAGO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_buscador_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoPago","cmbTipoPago",-1,"nombre","id_tipo_pago_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"style='width:100px;'"));

	//Combo concepto
	$idConcepto = -1; // Initialize variable to avoid undefined warning
	$subPlantilla->assign("COMBO_CONCEPTO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_obtener_combo(null,".$_SESSION["user"]["language_id"].")","cmbConcepto","cmbConcepto",$idConcepto,"nombre","id_concepto_movimiento",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboSubConcepto(this)'","style='width:100px;'"));
	
	//Combo subconcepto

	$subPlantilla->assign("DISPLAY_SUBCONCEPTO","style='display:none;'");
	
	//Combo pagado
	$matriz[1]["descripcion"]=STATIC_GLOBAL_BUTTON_YES;
	$matriz[0]["descripcion"]=STATIC_GLOBAL_BUTTON_NO;
	
	$subPlantilla->assign("COMBO_PAGADO",generalUtils::construirComboMatriz($matriz,"cmbPagado","cmbPagado",-1,STATIC_GLOBAL_COMBO_DEFAULT,-1,""));
	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_FACTURA",$_GET["id_factura"]);
	$subPlantilla->assign("ACTION","edit");

	$fechaDesde=date("Y")."-01-01";
	$fechaHasta=date("Y")."-12-31";
	$subPlantilla->assign("VIEW_MOVEMENT_DATE_FROM_SEARCH_VALUE",$fechaDesde);
	$subPlantilla->assign("VIEW_MOVEMENT_DATE_TO_SEARCH_VALUE",$fechaHasta);
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & clsoe
	$subPlantilla->parse("contenido_principal.item_button_close");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaFactura");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaPagoFactura");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	
	//Date picker FROM
	$plantilla->assign("INPUT_ID","txtFechaDesde");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date picker TO
	$plantilla->assign("INPUT_ID","txtFechaHasta");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	

	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	$resultadoFacturaMovimiento=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_movimiento_seleccionados(".$_GET["id_factura"].",".$_SESSION["user"]["language_id"].")");
	$totalFacturas=$db->getNumberRows($resultadoFacturaMovimiento);
	$i=1;
	while($datoFacturaMovimiento=$db->getData($resultadoFacturaMovimiento)){
		$subPlantilla->assign("ID_MOVIMIENTO",$datoFacturaMovimiento["id_movimiento"]);
		$subPlantilla->assign("MOVIMIENTO_CONCEPTO",$datoFacturaMovimiento["concepto"]);
		$subPlantilla->assign("MOVIMIENTO_TIPO",$datoFacturaMovimiento["tipo"]);
		$subPlantilla->parse("contenido_principal.bloque_movimientos.item_movimiento_seleccionado");
		$i++;
	}
	
	
	$subPlantilla->parse("contenido_principal.bloque_usuarios");
	$subPlantilla->parse("contenido_principal.bloque_movimientos");
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>