<?php
//Convertimos a PDF
require "classes/html2pdf/html2pdf.class.php";

//Plantilla pdf
$plantillaPdf=new XTemplate("html/sections/invoice/generate_invoice.html");

//Obtenemos la factura
$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto_pdf(".$_GET["id_factura"].")");
$datoFactura=$db->getData($resultadoFactura);


$plantillaPdf->assign("INVOICE_PDF_DATE_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_factura"],"-","/"));
if ($datoFactura["proforma"]==1){
    $plantillaPdf->assign("INVOICE_PROFORMA","PRO FORMA");
}
$plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE",$datoFactura["numero_factura"]);
$plantillaPdf->assign("INVOICE_PDF_CUSTOMER_CIF_VALUE",$datoFactura["nif_cliente_factura"]);
$plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"],"-","/"));





//Billing address
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


$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_obtener_concreto_pdf(".$_GET["id_factura"].",".$_SESSION["user"]["language_id"].")");
$total=0;
while($datoLineaFactura=$db->getData($resultadoFactura)){
    //Id registro de workshop
    if($datoLineaFactura["id_concepto_movimiento"]==55){
        //Dejamos claro que estamos con workshops
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