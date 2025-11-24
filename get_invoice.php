<?php
	/**
	 * 
	 * Pagina para forzar la descarga de documentos
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */


	require "includes/load_main_components.inc.php";
	
	if(isset($_GET["hash"])) {
		//Verificamos si existe el hash
		$resultadoPedidoHash = $db->callProcedure("CALL ed_sp_web_factura_hash_obtener('".$_GET["hash"]."')");
		if($db->getNumberRows($resultadoPedidoHash) > 0) {
			$dataPedidoHash = $db->getData($resultadoPedidoHash);
			
			$filename = "files/customers/invoice/pdf/".$dataPedidoHash["numero_factura"].".pdf";
		}

		if (file_exists($filename)) {
			header("Content-type: 'application/pdf'");
			header("Content-Length: " . filesize($filename));
		    header("Content-Disposition: attachment; filename=".$dataPedidoHash["numero_factura"].".pdf");
		    readfile($filename);
		    exit;
		} else {
			exit;
		}
	} else {
		exit;
	}
?>