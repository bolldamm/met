<?php
	/**
	 * 
	 * Pagina para forzar la descarga de certificados
	 * @author Mike
	 */


	require "includes/load_main_components.inc.php";
	
	if(isset($_GET["hash"])) {
		//Verificamos si existe el hash
		$resultadoFacturaHash = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_hash_obtener('".$_GET["hash"]."')");
		if($db->getNumberRows($resultadoFacturaHash) > 0) {
			$dataFacturaHash = $db->getData($resultadoFacturaHash);
			
			$filename = "files/customers/workshops/".$dataFacturaHash['filename'].".pdf";
		}
		if (file_exists($filename)) {
          
			header("Content-type: 'application/pdf'");
			header("Content-Length: " . filesize($filename));
		    header("Content-Disposition: attachment; filename=".$dataFacturaHash['filename'].".pdf");
		    readfile($filename);
		    exit;
		} else {
			exit;
		}
	} else {
		exit;
	}
?>