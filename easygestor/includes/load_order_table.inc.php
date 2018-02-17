<?php
	/**
	 * 
	 * Nos encargamos de asignar a todas las tablas de la plantilla, el componente drag and drop
	 * @author eData
	 * 
	 */

	$totalTablaOrden=count($vectorTablaOrden);
	
	
	for($i=0;$i<$totalTablaOrden;$i++){
		$plantilla->assign("TABLE",$vectorTablaOrden[$i]);
		$plantilla->parse("contenido_principal.carga_inicial.bloque_orden_tabla");
	}
?>