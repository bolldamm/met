<?php
	if(isset($_GET["hash"])) {
		//Verificamos si existe el hash
		$resultadoFacturaHash = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_hash_obtener('".$_GET["hash"]."')");
		if($db->getNumberRows($resultadoFacturaHash) > 0) {
			$dataFacturaHash = $db->getData($resultadoFacturaHash);
			
			$filename = "../files/customers/invoice/pdf/".$dataFacturaHash["numero_factura"].".pdf";
		}
		if (file_exists($filename)) {
			header("Content-type: 'application/pdf'");
			header("Content-Length: " . filesize($filename));
		    header("Content-Disposition: attachment; filename=".$dataFacturaHash["numero_factura"].".pdf");
		    readfile($filename);
		    exit;
		} else {
			generalUtils::redirigir("index.php");
		}
	} else {
		generalUtils::redirigir("index.php");
	}
?>