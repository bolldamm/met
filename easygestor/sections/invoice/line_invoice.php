<?php 
	//Borrar usuarios asociados
	if(isset($borrar) && $borrar==1){
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_eliminar(".$idFactura.")");
	}
	
	$movimientosSeleccionados=array_filter(explode(",",$_POST["hdnMovimientosSeleccionados"]));
	$totalMovimientosSeleccionados=count($movimientosSeleccionados);
	$i=0;
	for($i=0;$i<$totalMovimientosSeleccionados;$i++){
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_linea_factura_insertar(".$idFactura.",".$movimientosSeleccionados[$i].");");
	}
?>