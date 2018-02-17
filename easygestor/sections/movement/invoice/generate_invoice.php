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
    $plantillaPdf->assign("INVOICE_PDF_CUSTOMER_CIF_VALUE",$datoFactura["nif_cliente_factura"]);
    $plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"],"-","/"));
    
    //Billing address
    $direccion=$datoFactura["nombre_cliente_factura"]."<br>";
    if($datoFactura["nombre_empresa_factura"]){
    	$direccion.=$datoFactura["nombre_empresa_factura"]."<br>";
    }
    $direccion.=$datoFactura["direccion_factura"]."<br>";
    $direccion.=$datoFactura["codigo_postal_factura"]."<br>";
    $direccion.=$datoFactura["ciudad_factura"]." (".$datoFactura["provincia_factura"].") ".$datoFactura["pais_factura"]."<br>";
    
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