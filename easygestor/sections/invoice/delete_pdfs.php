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
   
		//Recorremos array
		for($i=0;$i<$totalItem;$i++){
          
          $resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_pdf_delete(".$vectorItem[$i].")");
 
}

generalUtils::redirigir("../../main_app.php?section=invoice&action=view");

?>