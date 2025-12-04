<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){		
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

		//Get Rectificativa fields from form
		$tipoRectificativa = isset($_POST["cmbTipoRectificativa"]) ? generalUtils::escaparCadena($_POST["cmbTipoRectificativa"]) : "";
		$idFacturaRectificada = isset($_POST["hdnIdFacturaRectificada"]) && !empty($_POST["hdnIdFacturaRectificada"]) ? intval($_POST["hdnIdFacturaRectificada"]) : null;
		$importeRectificativa = isset($_POST["txtImporteRectificativa"]) ? floatval(str_replace(",", ".", $_POST["txtImporteRectificativa"])) : 0;
		$cuotaRectificativa = isset($_POST["txtCuotaRectificativa"]) ? floatval(str_replace(",", ".", $_POST["txtCuotaRectificativa"])) : 0;

		$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_insertar(".$_SESSION["user"]["id"].",".$fechaFactura.",".$fechaPagoFactura.",'".generalUtils::escaparCadena($_POST["txtNumeroFactura"])."','".generalUtils::escaparCadena($_POST["txtNif"])."','".generalUtils::escaparCadena($_POST["txtNombreCliente"])."',".$visibleNombreCliente.",'".generalUtils::escaparCadena($_POST["txtNombreEmpresa"])."',".$visibleNombreEmpresa.",'".generalUtils::escaparCadena($_POST["txtDireccion"])."','".generalUtils::escaparCadena($_POST["txtCodigoPostal"])."','".generalUtils::escaparCadena($_POST["txtCiudad"])."','".generalUtils::escaparCadena($_POST["txtProvincia"])."','".generalUtils::escaparCadena($_POST["txtPais"])."','".$_POST["checkProformaFactura"]."','".$taxIdCountry."','".$taxIdType."','".$taxIdNumber."','".$tipoFacturaVerifactu."')");
		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];

		//Update rectificativa fields if this is a rectificativa invoice
		$isRectificativa = in_array($tipoFacturaVerifactu, ['R1', 'R2', 'R3', 'R4', 'R5']);
		if ($isRectificativa && !empty($tipoRectificativa)) {
			$updateQuery = "UPDATE ed_tb_factura SET
				es_rectificativa = 1,
				tipo_rectificativa = '" . $tipoRectificativa . "'";

			if ($tipoRectificativa === 'S' && $idFacturaRectificada !== null) {
				$updateQuery .= ", id_factura_rectificada = " . $idFacturaRectificada;
			} elseif ($tipoRectificativa === 'I') {
				$updateQuery .= ", importe_rectificativa = " . $importeRectificativa;
				$updateQuery .= ", cuota_rectificativa = " . $cuotaRectificativa;
			}

			$updateQuery .= " WHERE id_factura = " . $idFactura;
			$db->callProcedure($updateQuery);
		}

		require "line_invoice.php";
		
		//Guardamos factura...
		/*$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_guardar(".$idMovimiento.",".$_SESSION["user"]["id"].",".$fechaFactura.",".$fechaPagoFactura.",'".generalUtils::escaparCadena($_POST["txtNumeroFactura"])."','".generalUtils::escaparCadena($_POST["txtNif"])."','".generalUtils::escaparCadena($_POST["txtNombreCliente"])."','".generalUtils::escaparCadena($_POST["txtNombreEmpresa"])."','".generalUtils::escaparCadena($_POST["txtDireccion"])."','".generalUtils::escaparCadena($_POST["txtCodigoPostal"])."','".generalUtils::escaparCadena($_POST["txtCiudad"])."','".generalUtils::escaparCadena($_POST["txtProvincia"])."','".generalUtils::escaparCadena($_POST["txtPais"])."')");		
		$datoFactura=$db->getData($resultadoFactura);
		$idFactura=$datoFactura["id_factura"];*/
	
		//Linea factura
		//$resultadoLineaFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_guardar(".$idFactura.",".$idMovimiento.",'".$_POST["txtImporte"]."')");

		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=invoice&action=view");
	}

	

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/invoice/manage_invoice.html");
	
	$subPlantilla->assign("CHECKED_FACTURA_PROFORMA","checked");


	//Datos de la factura
	
	/*$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_movimiento_obtener_concreto(".$_GET["id_movimiento"].")");
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
		
	}else{*/

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_INVOICE_CREATE_INVOICE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_INVOICE_CREATE_INVOICE_TEXT;
	
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
	
	$subPlantilla->assign("ACTION","create");
	
	$subPlantilla->assign("CHECKED_NOMBRE_CLIENTE","checked");
	$subPlantilla->assign("CHECKED_NOMBRE_EMPRESA","checked");

	//Verifactu tax ID fields - dropdown selects
	$subPlantilla->assign("FACTURA_TAX_ID_NUMBER", "");

	//Combo tax ID country - uses ISO2 code as value
	$subPlantilla->assign("COMBO_TAX_ID_COUNTRY", generalUtils::construirCombo($db, "CALL ed_sp_web_pais_iso_obtener_combo()", "cmbTaxIdCountry", "cmbTaxIdCountry", "", "nombre_original", "iso2", "-- Country --", -1, 'style="width:150px;"'));

	//Combo tax ID type
	$subPlantilla->assign("COMBO_TAX_ID_TYPE", generalUtils::construirCombo($db, "CALL ed_sp_web_tax_id_type_obtener_combo()", "cmbTaxIdType", "cmbTaxIdType", "", "description", "tax_id_type", "-- ID Type --", -1, 'style="width:150px;"'));

	//Combo Verifactu invoice type
	// F1=Standard, F2=Simplified, F3=Replacement for simplified
	// R1-R5 = Rectificativa (corrective) invoices
	$comboTipoFactura = '<select name="cmbTipoFacturaVerifactu" id="cmbTipoFacturaVerifactu" style="width:350px;">';
	$comboTipoFactura .= '<option value="F1" selected>F1 - Standard Invoice</option>';
	$comboTipoFactura .= '<option value="F2">F2 - Simplified Invoice</option>';
	$comboTipoFactura .= '<option value="F3">F3 - Invoice replacing simplified invoices</option>';
	$comboTipoFactura .= '<optgroup label="Rectificativa (Corrective Invoices)">';
	$comboTipoFactura .= '<option value="R1">R1 - Rectificativa (Art. 80.1, 80.2, 80.6 LIVA)</option>';
	$comboTipoFactura .= '<option value="R2">R2 - Rectificativa (Art. 80.3 LIVA - Bankruptcy)</option>';
	$comboTipoFactura .= '<option value="R3">R3 - Rectificativa (Art. 80.4 LIVA - Bad debt)</option>';
	$comboTipoFactura .= '<option value="R4">R4 - Rectificativa (Other causes)</option>';
	$comboTipoFactura .= '<option value="R5">R5 - Rectificativa in simplified invoices</option>';
	$comboTipoFactura .= '</optgroup>';
	$comboTipoFactura .= '</select>';
	$subPlantilla->assign("COMBO_TIPO_FACTURA_VERIFACTU", $comboTipoFactura);
	
	$fechaDesde="01-01-".date("Y");
	$fechaHasta="31-12-".date("Y");
	
	$subPlantilla->assign("VIEW_MOVEMENT_DATE_FROM_SEARCH_VALUE",$fechaDesde);
	$subPlantilla->assign("VIEW_MOVEMENT_DATE_TO_SEARCH_VALUE",$fechaHasta);
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
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