<?php
	/**
	 * 
	 * Script to generate PDFs of all selected invoices
	 * @Author Mike
	 * 
	 */

	require "../../includes/load_main_components.inc.php";
	require "../../includes/load_validate_user.inc.php";
	require "../../config/constants.php";
	require "../../config/dictionary/".$_SESSION["user"]["language_dictio"];
	require "../../config/sections.php";
	
		/**
		 * 
		 * La cadena de los id separado por el caracter, se ha pasado a un array.
		 * @var array
		 * 
		 */
		$vectorItem=array_filter(explode(",",$_POST["hdnId"]));
		/**
		 * 
		 * Total items del array
		 * @var int
		 * 
		 */
		$totalItem=count($vectorItem);
		
	
		echo "<!DOCTYPE html><html><style>#myProgress {  width: 100%;  background-color: #ddd; } #myBar {  width: 1%;  height: 30px;  background-color: #4CAF50; } </style><body>";
		echo "<h1>Processing - please wait...</h1>";
		echo "<div id='myProgress'><div id='myBar'></div></div>";
		echo "<script type='text/javascript'>var i = 0; function move() {  if (i == 0) { i = 1; var elem = document.getElementById('myBar'); var width = 1; var id = setInterval(frame, 10); function frame() { if (width >= 100) { width = 1; i = 0; } else { width++; elem.style.width = width + '%'; } } } } move(); </script>";
      
		$resultadoFacturaLast=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_last()");
        $datoFacturaLast=$db->getData($resultadoFacturaLast);

         if(!$datoFacturaLast["max_numero_factura"]){ 
          	$datoFacturaLast["max_numero_factura"] = "XXXX-0";
         }

      	$fatno = substr($datoFacturaLast["max_numero_factura"], 5);
      
		//Recorremos array
		for($i=0;$i<$totalItem;$i++){
          
		$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto($vectorItem[$i])");
		$datoFactura=$db->getData($resultadoFactura);
          
         if($datoFactura["nombre_empresa_factura"]!=""){ 
          	$datoFactura["visible_nombre_empresa_factura"] = 1;
         }
          
        $fechaPagoFactura = $datoFactura['fecha_pago_factura'];
          
        if($datoFactura["numero_factura"]!=""){
          $fatnum = $datoFactura["numero_factura"];
          $fechaFactura = $datoFactura['fecha_factura'];
        } else {
	        $fatno++;
            // $fatnum = "Test-" . date("Y") . "-" . str_pad($fatno, 3, '0', STR_PAD_LEFT);
            $fatnum = date("Y") . "-" . str_pad($fatno, 3, '0', STR_PAD_LEFT);
	        $fechaFactura = date("Y-m-d");
            if(!$datoFactura["fecha_pago_factura"]){ 
 	         $fechaPagoFactura = $datoFactura['fecha_factura'];
	        } 
        }
         
          	//Guardamos factura...
		$resultadoFactura2=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_editar(".$vectorItem[$i]. ",'" . $fechaFactura . "','" . $fechaPagoFactura . "','".generalUtils::escaparCadena($fatnum)."','".generalUtils::escaparCadena($datoFactura['nif_cliente_factura'])."','".generalUtils::escaparCadena($datoFactura['nombre_cliente_factura'])."','1','".generalUtils::escaparCadena($datoFactura['nombre_empresa_factura'])."',".$datoFactura['visible_nombre_empresa_factura'].",'".generalUtils::escaparCadena($datoFactura['direccion_factura'])."','".generalUtils::escaparCadena($datoFactura['codigo_postal_factura'])."','".generalUtils::escaparCadena($datoFactura['ciudad_factura'])."','".generalUtils::escaparCadena($datoFactura['provincia_factura'])."','".generalUtils::escaparCadena($datoFactura['pais_factura'])."','".$datoFactura['proforma']."')");
		// require "line_invoice.php";


		$db->endTransaction();
          
          echo "<script type='text/javascript'>var element = document.createElement('iframe'); element.setAttribute('id', '" . $vectorItem[$i]  . "'); element.setAttribute('src', 'https://www.metmeetings.org/easygestor/main_app.php?section=invoice&action=generate&ioutput=1&id_factura=" . $vectorItem[$i]  . "'); element.style.display = 'none'; document.body.appendChild(element);</script>";

 
          
}
$i++;
$i = $i * 1000;

echo "<script type='text/javascript'>setTimeout(function(){window.location = 'https://www.metmeetings.org/easygestor/main_app.php?section=invoice&action=view&reload=" . rand() . "';}, " . $i . ");</script>";

// echo "<script type='text/javascript'>window.location = 'https://www.metmeetings.org/easygestor/main_app.php?section=invoice&action=view&reload=" . rand() . "';</script>";        
 echo "</body></html>";
?>