<?php
	if(isset($_POST["hdnNombreImagen_1"])){
		$nombreImagen=$_POST["hdnNombreImagen_1"];
	}else{
		//Si estamos creando el logo por primera vez, tenemos el nombre en blanco por defecto
		$nombreImagen="";
	}
	
	//Borramos logo del servidor
	if($_POST["hdnEliminarImagen_1"]==1){
		unlink("../files/members/".$_POST["hdnNombreImagen_1"]);
		unlink("../files/members/thumb/".$_POST["hdnNombreImagen_1"]);
		$nombreImagen="";
	}
	
	//Si hemos introducido imagen...
	if($_FILES["fileImagen"]["tmp_name"]){
		//Cargamos clase phpthumb
		require "classes/phpThumb/ThumbLib.inc.php";
		$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
		$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
		$nombreImagen=$idUsuarioWeb.".".$extensionImagen;
				
		
		if($idModalidadUsuario==MODALIDAD_USUARIO_INDIVIDUAL){	
			$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL, HEIGHT_SIZE_MEMBER_INDIVIDUAL);
			$thumb->save("../files/members/".$nombreImagen);
			
			//Redimension la imagen
			$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL_THUMB, HEIGHT_SIZE_MEMBER_INDIVIDUAL_THUMB);
			$thumb->save("../files/members/thumb/".$nombreImagen);
			
		}else if($idModalidadUsuario==MODALIDAD_USUARIO_INSTITUTIONAL){
			$thumb->resize(WIDTH_SIZE_MEMBER_INSTITUTIONAL, HEIGHT_SIZE_MEMBER_INSTITUTIONAL);
			$thumb->save("../files/members/".$nombreImagen);
			
			//Redimension la imagen
			$thumb->resize(WIDTH_SIZE_MEMBER_INSTITUTIONAL_THUMB, HEIGHT_SIZE_MEMBER_INSTITUTIONAL_THUMB);
			$thumb->save("../files/members/thumb/".$nombreImagen);
		}
		
		//Comprobamos si antes tenia imagen
		if(isset($_POST["hdnNombreImagen_1"])){
			//Miramos si ha cambiado la extension
			$extensionImagenActual=generalUtils::obtenerExtensionFichero($_POST["hdnNombreImagen_1"]);
			if($extensionImagenActual!=$extensionImagen){
				//Borramos la imagen antigua del servidor
				unlink("../files/members/".$_POST["hdnNombreImagen_1"]);
				unlink("../files/members/thumb/".$_POST["hdnNombreImagen_1"]);
			}
		}
	}
	
	//Actualizamos columna logo de la tabla proyecto
	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_imagen_guardar(".$idUsuarioWeb.",'".$nombreImagen."')");
?>