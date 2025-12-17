<?php
    //Convertimos a PDF
    require "classes/html2pdf/html2pdf.class.php";

	//Plantilla pdf
	$plantillaPdf=new XTemplate("html/sections/movement/invoice/generate_invoice.html");
	
	//Obtenemos la factura
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto(".$_GET["id_factura"].",".$_SESSION["user"]["language_id"].")");
	$datoFactura=$db->getData($resultadoFactura);
	
	
	/*f.id_factura,f.fecha_factura,f.fecha_pago_factura,f.numero_factura,
    f.nif_cliente_factura,f.nombre_cliente_factura,f.nombre_empresa_factura,f.direccion_factura,
    f.codigo_postal_factura,f.ciudad_factura,f.provincia_factura,f.pais_factura,lf.precio*/
	
    $plantillaPdf->assign("INVOICE_PDF_DATE_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_factura"],"-","/"));
    $plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE",$datoFactura["numero_factura"]);

    //Display tax ID with appropriate country prefix
    //Spanish/EU: show with country prefix (ES-12345678A, BE-0404621642)
    //Non-EU: show without prefix
    //F2 simplified invoices: show N/A (no tax ID required)
    $customerTaxId = "";

    //For F2 simplified invoices, just show N/A - no tax ID processing needed
    if (!empty($datoFactura["tipo_factura_verifactu"]) && $datoFactura["tipo_factura_verifactu"] === "F2") {
        $customerTaxId = "N/A";
    } else {
        $taxIdNumber = !empty($datoFactura["tax_id_number"])
            ? $datoFactura["tax_id_number"]
            : ($datoFactura["nif_cliente_factura"] ?? "");
        $taxIdCountry = strtoupper($datoFactura["tax_id_country"] ?? "");

        //EU VIES countries (excluding Spain which uses ES)
        $viesCountries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'FI', 'FR',
            'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT',
            'RO', 'SE', 'SI', 'SK'];

        if (!empty($taxIdNumber)) {
            $isSpanish = empty($taxIdCountry) || $taxIdCountry === "ES";
            $isEu = in_array($taxIdCountry, $viesCountries);

            if ($isSpanish) {
                //Spanish NIF: always show with ES- prefix
                $customerTaxId = "ES-" . $taxIdNumber;
            } elseif ($isEu) {
                //EU VAT: show with country prefix, avoiding duplication
                //Strip country code from number if already present
                if (strpos(strtoupper($taxIdNumber), $taxIdCountry) === 0) {
                    $taxIdNumber = substr($taxIdNumber, strlen($taxIdCountry));
                }
                $customerTaxId = $taxIdCountry . "-" . $taxIdNumber;
            } else {
                //Non-EU: show without prefix
                $customerTaxId = $taxIdNumber;
            }
        } else {
            $customerTaxId = "N/A";
        }
    }
    $plantillaPdf->assign("INVOICE_PDF_CUSTOMER_CIF_VALUE", $customerTaxId);
    $plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"],"-","/"));
    
    //Billing address
    $direccion=$datoFactura["nombre_cliente_factura"]."<br>";
    if($datoFactura["nombre_empresa_factura"]){
    	$direccion.=$datoFactura["nombre_empresa_factura"]."<br>";
    }
    $direccion.=$datoFactura["direccion_factura"]."<br>";
    $direccion.=$datoFactura["codigo_postal_factura"]."<br>";
    // Get country name from tax_id_country (ISO-2 code) - the authoritative source
    $displayCountry = "";
    if (!empty($datoFactura["tax_id_country"])) {
        $countryResult = $db->callProcedure("CALL ed_sp_web_pais_get_name_from_iso('" . $datoFactura["tax_id_country"] . "')");
        if ($countryRow = $db->getData($countryResult)) {
            $displayCountry = $countryRow["nombre_original"];
        }
    }
    // Fallback to pais_factura for legacy invoices that haven't been migrated
    if (empty($displayCountry) && !empty($datoFactura["pais_factura"])) {
        $displayCountry = $datoFactura["pais_factura"];
    }
    $direccion.=$datoFactura["ciudad_factura"]." (".$datoFactura["provincia_factura"].") ".$displayCountry."<br>";
    
    $plantillaPdf->assign("INVOICE_PDF_BILLING_ADDRESS_VALUE",$direccion);
    if($datoFactura["concepto_personalizado"]!=""){
   		$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoFactura["concepto_personalizado"]);
   	}else{
   		$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",$datoFactura["concepto"]);
   	}
    $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE",$datoFactura["precio"]." &euro;");


	
	$plantillaPdf->parse("contenido_principal");

	try{
      	$html2pdf = new HTML2PDF("P", "A4", "es");
        $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"), false);
       // $html2pdf->Output("../files/customers/invoice/pdf/".$datoFactura["numero_factura"].".pdf","F");
	    //Calculamos hash
		$hashGenerado=md5(uniqid(time()).$datoFactura["numero_factura"]);
        
        //Actualizar factura
        $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_actualizar_hash(".$_GET["id_factura"].",'".$hashGenerado."')");
        
        
        $html2pdf->Output($datoFactura["numero_factura"].".pdf","FI");
        rename($datoFactura["numero_factura"].".pdf","../files/customers/invoice/pdf/".$datoFactura["numero_factura"].".pdf");
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>