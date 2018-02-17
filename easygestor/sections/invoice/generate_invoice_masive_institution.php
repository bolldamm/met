<?php
    //conexio bdd
   
    
	header("Content-Type: text/html; charset=utf-8");
	/**
	 * 
	 * Indicamos la ruta absoluta en donde esta alojado el fichero.
	 * @var string
	 */
	
	
	require "../../../classes/databaseConnection.php";
	require "../../../classes/generalUtils.php";
	require "../../classes/gestorGeneralUtils.php";
	require "../../../database/connection.php";
	require "../../../includes/load_template.inc.php";
   
  
  //posem el diccio
  require "../../config/dictionary/en_EN.php";
 
    //Convertimos a PDF
    require "../../classes/html2pdf/html2pdf.class.php";
    
	
  //$members='1561,1264';
  //$importe=30;
  //$id_mov=2;
  $members=$_GET["members"];
  $importe=$_GET["importe"];
  $id_mov=$_GET["mov"];
  
  //echo $members ."<br>";
  //echo $importe ."<br>";
  //echo $id_mov ."<br>";
  //die();
  //Es fa el bucle per fer la crida a totes les factures
  $facturasPDF=$db->callProcedure("CALL ed_sp_auto_renew_member_institution('".$members."',".$importe.",".$id_mov.")");
	$numFactura=$db->getNumberRows($facturasPDF);
  while($numFactura=$db->getData($facturasPDF)){
      //Plantilla pdf
	    $plantillaPdf=new XTemplate("../../html/sections/invoice/generate_invoice_massive.html");
	      
		  $id_factura=$numFactura["id_factura"];
      //.$_GET["id_factura"]
      
      $id_user_language_id="1";
      //.$_SESSION["user"]["language_id"].
      
//      echo $id_factura;
	    //Obtenemos la factura
	    $resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto_pdf(".$id_factura.")");
	    $datoFactura=$db->getData($resultadoFactura);
      
      $plantillaPdf->assign("INVOICE_PDF_DATE_VALUE",generalUtils::conversionFechaFormato($datoFactura["fecha_factura"],"-","/"));
      $plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE",$datoFactura["numero_factura"]);
//      echo $datoFactura["numero_factura"];
      
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
    
//      echo $direccion;
//      die();
      $plantillaPdf->assign("INVOICE_PDF_BILLING_ADDRESS_VALUE",$direccion);
    
       $resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_obtener_concreto_pdf(".$id_factura.",".$id_user_language_id.")");
	    $total=0;
    
        while ($datoLineaFactura = $db->getData($resultadoFactura)) {
        $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",
                $datoLineaFactura["mas_informacion"]);
        $total+=$datoLineaFactura["precio"];
        $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE",
                "&euro; " . $datoLineaFactura["precio"]);
        $plantillaPdf->parse("contenido_principal.item_linea_factura");
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
        $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_actualizar_hash(".$id_factura.",'".$hashGenerado."')");
        
        
        //$html2pdf->Output($datoFactura["numero_factura"].".pdf","FI");
        //Comento la I perque no surti per pantalla
        $html2pdf->Output($datoFactura["numero_factura"].".pdf","F");
        rename($datoFactura["numero_factura"].".pdf","../../../files/customers/invoice/pdf/".$datoFactura["numero_factura"].".pdf");
      }
      catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
      }
   //Final bucle generar factures.
   }