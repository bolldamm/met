<?php
	
	//Tanto crear menu como editar menu, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$nombre=generalUtils::escaparCadena($_POST["txtNombre_".$datoIdioma["id_idioma"]]);
		$descripcion=generalUtils::escaparCadena($_POST["txtDescripcion_".$datoIdioma["id_idioma"]]);
      $season=generalUtils::escaparCadena($_POST["txtSeason_".$datoIdioma["id_idioma"]]);
      $duration=generalUtils::escaparCadena($_POST["txtDuration_".$datoIdioma["id_idioma"]]);
      $facilitator=generalUtils::escaparCadena($_POST["txtFacilitator_".$datoIdioma["id_idioma"]]);
      $feedback=generalUtils::escaparCadena($_POST["txtFeedback_".$datoIdioma["id_idioma"]]);
      $n_facilitator=generalUtils::escaparCadena($_POST["numNFacilitator_".$datoIdioma["id_idioma"]]);

		//Insertamos datos multidioma de el caso de exito por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_idioma_guardar(".$idTaller.",".$datoIdioma["id_idioma"].",'".$nombre."','".$descripcion."','".$season."','".$duration."','".$facilitator."','".$feedback."','".$n_facilitator."')");
      	//	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_idioma_guardar(".$idTaller.",".$datoIdioma["id_idioma"].",'".$nombre."','".$descripcion."','".$season."','".$duration."','".$facilitator."')");
	}
?>