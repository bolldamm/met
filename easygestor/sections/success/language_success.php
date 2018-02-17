<?php
	
	//Tanto crear menu como editar menu, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo_".$datoIdioma["id_idioma"]]);
		$descripcionPrevia=generalUtils::escaparCadena($_POST["txtaDescripcionPrevia_".$datoIdioma["id_idioma"]]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion_".$datoIdioma["id_idioma"]]);

		//Insertamos datos multidioma de el caso de exito por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_idioma_guardar(".$idCasoExito.",".$datoIdioma["id_idioma"].",'".$titulo."','".$descripcionPrevia."','".$descripcion."')");
	}
?>