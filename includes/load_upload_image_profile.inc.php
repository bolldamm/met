<?php
	/**
	 * Este fichero trata la imagen del perfil de usuario
	 */

	//Si estamos editando una imagen, ponemos el nombre de la imagen actual
	$esCorrect = true;
	if(isset($_POST["hdnFileImagenPerfil"])){
		$nombreImagen = $_POST["hdnFileImagenPerfil"];
		//Verificamos que existe esta imagen para este usuario
		$resultImagen = $db->callProcedure("CALL ed_sp_web_usuario_web_imagen_existe(".$idUsuarioWebLogin.", '".$nombreImagen."')");
		if($db->getNumberRows($resultImagen) == 0) {
			$_POST["hdnEliminarFileImagenPerfil"] = 0;
			$esCorrect = false;
		}
	}else{
		//Si estamos creando una imagen por primera vez, tenemos el nombre en blanco por defecto
		$nombreImagen = "";
	}
	
	if($esCorrect) {
		//Borramos logo del servidor
		if($_POST["hdnEliminarFileImagenPerfil"] == 1){
			unlink("files/members/thumb/".$_POST["hdnFileImagenPerfil"]);
			unlink("files/members/".$_POST["hdnFileImagenPerfil"]);
			$nombreImagen = "";
		}

		//Si hemos introducido imagen...
		if($_FILES["fileImagenPerfil"]["tmp_name"] && generalUtils::esImagenValida($_FILES["fileImagenPerfil"]["name"])){
			//Cargamos clase phpthumb
			require "classes/phpThumb/ThumbLib.inc.php";
			$thumb = PhpThumbFactory::create($_FILES["fileImagenPerfil"]["tmp_name"]);
			$extensionImagen = generalUtils::obtenerExtensionFichero($_FILES["fileImagenPerfil"]["name"]);
			$nombreImagen = $idUsuarioWebLogin.".".$extensionImagen;
			
			
			if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INDIVIDUAL){
				$thumb->resize(WIDTH_SIZE_PROFILE_INDIVIDUAL, HEIGHT_SIZE_PROFILE_INDIVIDUAL);
				$thumb->save("files/members/".$nombreImagen);
				
				//Redimension la imagen

				$thumb->resize(WIDTH_SIZE_PROFILE_THUMB_INDIVIDUAL, HEIGHT_SIZE_PROFILE_THUMB_INDIVIDUAL);
				$thumb->save("files/members/thumb/".$nombreImagen);
				
			}else if($_SESSION["met_user"]["id_modalidad"]==MODALIDAD_USUARIO_INSTITUTIONAL){
				$thumb->resize(WIDTH_SIZE_PROFILE_INSTITUTIONAL, HEIGHT_SIZE_PROFILE_INSTITUTIONAL);
				$thumb->save("files/members/".$nombreImagen);
				
				$thumb->resize(WIDTH_SIZE_PROFILE_INSTITUTIONAL, HEIGHT_SIZE_PROFILE_INSTITUTIONAL);
				$thumb->save("files/members/thumb/".$nombreImagen);
			}
			
						
			//Comprobamos si antes tenia imagen
			if(isset($_POST["hdnFileImagenPerfil"])){
				//Miramos si ha cambiado la extension
				$extensionImagenActual = generalUtils::obtenerExtensionFichero($_POST["hdnFileImagenPerfil"]);
				if($extensionImagenActual != $extensionImagen){
					//Borramos la imagen antigua del servidor
					unlink("files/members/thumb/".$_POST["hdnFileImagenPerfil"]);
					unlink("files/members/".$_POST["hdnFileImagenPerfil"]);
				}
			}
		}
		
		//Actualizamos la imagen en la base de datos
		$resultado=$db->callProcedure("CALL ed_sp_web_usuario_web_imagen_guardar(".$idUsuarioWebLogin.",'".$nombreImagen."')");
	}
?>