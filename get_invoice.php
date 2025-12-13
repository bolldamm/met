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
		//Verificamos si existe el hash - use direct query on invoice table
		$hash = generalUtils::escaparCadena($_GET["hash"]);
		$query = "SELECT numero_factura FROM ed_tb_factura WHERE hash_generado = '".$hash."'";

		$resultadoPedidoHash = $db->callProcedure($query);

		if($db->getNumberRows($resultadoPedidoHash) > 0) {
			$dataPedidoHash = $db->getData($resultadoPedidoHash);

			$filename = "files/customers/invoice/pdf/".$dataPedidoHash["numero_factura"].".pdf";

			if (file_exists($filename)) {
				header("Content-type: application/pdf");
				header("Content-Length: " . filesize($filename));
				header("Content-Disposition: attachment; filename=".$dataPedidoHash["numero_factura"].".pdf");
				readfile($filename);
				exit;
			} else {
				// PDF file not found
				header("HTTP/1.0 404 Not Found");
				echo "Invoice PDF not found. Looking for: " . htmlspecialchars($filename);
				exit;
			}
		} else {
			// Hash not found in database
			header("HTTP/1.0 404 Not Found");
			echo "Invalid invoice link.";
			exit;
		}
	} else {
		// No hash provided
		header("HTTP/1.0 400 Bad Request");
		echo "No invoice specified.";
		exit;
	}
?>