<?php
	/**
	 *
	 * Script to ungenerate already generated PDF invoices
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

		$skippedInvoices = array();
		$unlinkedCount = 0;

		//Recorremos array
		for($i=0;$i<$totalItem;$i++){
			$idFactura = intval($vectorItem[$i]);

			//Check if invoice is registered with Verifactu
			$resultadoCheck = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto(".$idFactura.")");
			$datoCheck = $db->getData($resultadoCheck);

			if (!empty($datoCheck["verifactu_uuid"])) {
				//Invoice is registered with Verifactu - cannot unlink PDF
				$skippedInvoices[] = $datoCheck["numero_factura"];
			} else {
				//Safe to unlink PDF
				$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_pdf_delete(".$idFactura.")");
				$unlinkedCount++;
			}
		}

		//If any invoices were skipped, store message in session
		if (count($skippedInvoices) > 0) {
			$_SESSION["verifactu_warning"] = "Cannot unlink PDFs for Verifactu-registered invoices: " . implode(", ", $skippedInvoices) . ". These invoices have been submitted to AEAT and their PDFs must be preserved. Unlinked " . $unlinkedCount . " other invoice(s).";
		}

generalUtils::redirigir("../../main_app.php?section=invoice&action=view");

?>