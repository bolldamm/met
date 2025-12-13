<?php
	if(isset($_POST["hdnNombreImagen_1"])){
		$nombreImagen=$_POST["hdnNombreImagen_1"];
	}else{
		//Si estamos creando el logo por primera vez, tenemos el nombre en blanco por defecto
		$nombreImagen="";
	}
	
	//Borramos logo del servidor
	if($_POST["hdnEliminarImagen_1"]==1){
		unlink("../files/institutional/".$_POST["hdnNombreImagen_1"]);
		$nombreImagen="";
	}
	
	//Si hemos introducido imagen...
	if($_FILES["fileImagen"]["tmp_name"]){
		//Cargamos clase phpthumb
		require "classes/phpThumb/ThumbLib.inc.php";
		$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
		$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
		$nombreImagen=$idCasoExito.".".$extensionImagen;
		
		//Redimension logo
		$thumb->resize(WIDTH_SIZE_SUCCESS,HEIGHT_SIZE_SUCCESS);
		$thumb->save("../files/institutional/".$nombreImagen);
		
		//Comprobamos si antes tenia imagen
		if(isset($_POST["hdnNombreImagen_1"])){
			//Miramos si ha cambiado la extension
			$extensionImagenActual=generalUtils::obtenerExtensionFichero($_POST["hdnNombreImagen_1"]);
			if($extensionImagenActual!=$extensionImagen){
				//Borramos la imagen antigua del servidor
				unlink("../files/institutional/".$_POST["hdnNombreImagen_1"]);
			}
		}
	}
	
	//Actualizamos columna logo de la tabla proyecto
	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_imagen_insertar(".$idCasoExito.",'".$nombreImagen."')");
?>