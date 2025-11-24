<?php
    /*
     * Create invoice using HTML template,
     * convert HTML to PDF, then
     * output PDF to screen and to file
     */	

	require_once('../vendor/autoload.php');

    use \Spipu\Html2Pdf\Html2Pdf;
    use \Spipu\Html2Pdf\Exception\Html2PdfException;
    use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

    //Get HTML invoice template
	$plantillaPdf=new XTemplate("html/sections/invoice/generate_invoice.html");
	
	//Get invoice details
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto_pdf(".$_GET["id_factura"].")");
	$datoFactura=$db->getData($resultadoFactura);

	//Assign invoice details to placeholders in template
    $plantillaPdf->assign("INVOICE_PDF_DATE_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_factura"],"-","/"));
    if ($datoFactura["proforma"]==1){
		$plantillaPdf->assign("INVOICE_PROFORMA","PRO FORMA INVOICE");
		} elseif ($datoFactura["proforma"]==2){
    	$plantillaPdf->assign("INVOICE_PROFORMA","CREDIT NOTE");
		} else {
      	$plantillaPdf->assign("INVOICE_PROFORMA","INVOICE");
    	}
	$plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE",$datoFactura["numero_factura"]);
    $plantillaPdf->assign("INVOICE_PDF_CUSTOMER_CIF_VALUE",$datoFactura["nif_cliente_factura"]);
    if ($datoFactura["fecha_pago_factura"] != "") {
        $plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE", generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"], "-", "/"));
    }

    //Get billing address and assign to placeholder
    $direccion="";
    if($datoFactura["visible_nombre_cliente_factura"]==1){
     	$direccion.=$datoFactura["nombre_cliente_factura"]."<br>";
    }
    if($datoFactura["visible_nombre_empresa_factura"]==1){
    	 $direccion.=$datoFactura["nombre_empresa_factura"]."<br>";
    }
    if($datoFactura["provincia_factura"]!=""){
    	$provincia=" (".$datoFactura["provincia_factura"].")";
    }else{
    	$provincia="";
    }
    $direccion.=$datoFactura["direccion_factura"]."<br>";
    $direccion.=$datoFactura["codigo_postal_factura"]."<br>";
    $direccion.=$datoFactura["ciudad_factura"].$provincia." <br>".$datoFactura["pais_factura"]."<br>";
    
    $plantillaPdf->assign("INVOICE_PDF_BILLING_ADDRESS_VALUE",$direccion);
    
    //Get invoice items (workshop = 55, custom = 46, or conference) and assign to placeholders
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_obtener_concreto_pdf(".$_GET["id_factura"].",".$_SESSION["user"]["language_id"].")");
	$total=0;
	while($datoLineaFactura=$db->getData($resultadoFactura)){
		if($datoLineaFactura["id_concepto_movimiento"]==55){
			//Workshops heading
			$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE","<strong>".STATIC_INVOICE_PDF_WORKSHOP_ROW_TITLE."</strong>");
			$plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE","");
			$plantillaPdf->assign("ESTILO_CONCRETO","");
			
			$plantillaPdf->parse("contenido_principal.item_linea_factura");
			
			$resultadoListadoTaller=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_obtener_concreto_pdf(".$datoLineaFactura["id_movimiento"].",".$_SESSION["user"]["language_id"].")");
			while($datoListadoTaller=$db->getData($resultadoListadoTaller)){
				
				$total+=$datoListadoTaller["importe"];
				$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoListadoTaller["nombre"]);
				$plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE","&euro; ".$datoListadoTaller["importe"]);
				$plantillaPdf->assign("ESTILO_CONCRETO","padding-left:30px;");
				
				
				$plantillaPdf->parse("contenido_principal.item_linea_factura");
			}
		}else if($datoLineaFactura["id_concepto_movimiento"]==46){
			$total+=$datoLineaFactura["precio"];
		    if($datoLineaFactura["concepto_personalizado"]!=""){
		   		$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoLineaFactura["concepto_personalizado"]);
		   	}else{
		   		//$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",STATIC_INVOICE_METM_CURRENT);
              	$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE","METM".date(y)." conference fee");
		   	}
		    $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE","&euro; ".$datoLineaFactura["precio"]);
		    
		    $plantillaPdf->parse("contenido_principal.item_linea_factura");
		}else{
			$total+=$datoLineaFactura["precio"];
		    if($datoLineaFactura["concepto_personalizado"]!=""){
		   		$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoLineaFactura["concepto_personalizado"]);
		   	}else{
		   		$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoLineaFactura["concepto"]);
		   	}
		    $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE","&euro; ".$datoLineaFactura["precio"]);
		    
		    $plantillaPdf->parse("contenido_principal.item_linea_factura");
		}
	}
	
	$plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE","&euro; ".sprintf("%.2f",$total));
	
	$plantillaPdf->parse("contenido_principal");

	//Convert HTML to PDF, else output error message
    try {
        $html2pdf = new Html2Pdf('P', 'A4', 'en');
        $html2pdf->setDefaultFont('freesans');
        $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"));
        $hashGenerado=md5(uniqid(time()) . $datoFactura["numero_factura"]);

        //Update invoice
        $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_actualizar_hash(".$_GET["id_factura"].",'".$hashGenerado."')");

        //Output PDF to file (F) and screen (I), then move to /easygestor/files/customers/invoice/pdf folder
      
      	if ($_GET["ioutput"]) {
          $invoiceOutput = "F";
        } else {
          $invoiceOutput = "FI";
        }
      
        $html2pdf->Output(__DIR__.$datoFactura["numero_factura"].".pdf",$invoiceOutput);
        rename(__DIR__.$datoFactura["numero_factura"].".pdf","../files/customers/invoice/pdf/".$datoFactura["numero_factura"].".pdf");

    } catch (Html2PdfException $e) {
        $html2pdf->clean();

        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
        exit;
    }
