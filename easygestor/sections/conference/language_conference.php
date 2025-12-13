<?php
	
	//Tanto crear menu como editar menu, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$nombre=generalUtils::escaparCadena($_POST["txtNombre_".$datoIdioma["id_idioma"]]);
		$descripcion=generalUtils::escaparCadena($_POST["txtDescripcion_".$datoIdioma["id_idioma"]]);

		//Insertamos datos multidioma de el caso de exito por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_idioma_guardar(".$idConferencia.",".$datoIdioma["id_idioma"].",'".$nombre."','".$descripcion."')");
	}
?>