<?php
	
	//Tanto crear menu como editar menu, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$nombre=generalUtils::escaparCadena($_POST["txtNombre_".$datoIdioma["id_idioma"]]);

		//Insertamos nombre de la tematica por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_idioma_guardar(".$idTipoPago.",".$datoIdioma["id_idioma"].",'".$nombre."')");
	}
?>