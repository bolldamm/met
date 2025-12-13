<?php
	
	//Tanto crear menu como editar menu, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo_".$datoIdioma["id_idioma"]]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion_".$datoIdioma["id_idioma"]]);

		//Insertamos titulo y descripcion del menu por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_idioma_guardar(".$idMenu.",".$datoIdioma["id_idioma"].",'".$titulo."','".$descripcion."')");
	}
?>