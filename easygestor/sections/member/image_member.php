<?php

    //Store name of existing image or empty variable
	if(isset($_POST["hdnNombreImagen_1"])){
		$nombreImagen=$_POST["hdnNombreImagen_1"];
	}else{
		$nombreImagen="";
	}
	
	//If delete button has been clicked, delete image and empty variable
	if($_POST["hdnEliminarImagen_1"]==1){
		unlink("../files/members/".$_POST["hdnNombreImagen_1"]);
		unlink("../files/members/thumb/".$_POST["hdnNombreImagen_1"]);
		$nombreImagen="";
	}
	
	//If a new image has been added
	if($_FILES["fileImagen"]["tmp_name"]){
		//Load phpthumb class
		require "classes/phpThumb/ThumbLib.inc.php";
		$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
		$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
		$nombreImagen=$idUsuarioWeb.".".$extensionImagen;
				
		
		if($idModalidadUsuario==MODALIDAD_USUARIO_INDIVIDUAL){	
			$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL, HEIGHT_SIZE_MEMBER_INDIVIDUAL);
			$thumb->save("../files/members/".$nombreImagen);
			
			//Resize the image
			$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL_THUMB, HEIGHT_SIZE_MEMBER_INDIVIDUAL_THUMB);
			$thumb->save("../files/members/thumb/".$nombreImagen);
			
		}else if($idModalidadUsuario==MODALIDAD_USUARIO_INSTITUTIONAL){
			$thumb->resize(WIDTH_SIZE_MEMBER_INSTITUTIONAL, HEIGHT_SIZE_MEMBER_INSTITUTIONAL);
			$thumb->save("../files/members/".$nombreImagen);
			
			//Resize the image
			$thumb->resize(WIDTH_SIZE_MEMBER_INSTITUTIONAL_THUMB, HEIGHT_SIZE_MEMBER_INSTITUTIONAL_THUMB);
			$thumb->save("../files/members/thumb/".$nombreImagen);
		}
		
		//Check if there was already an image
		if(isset($_POST["hdnNombreImagen_1"])){
			//Check whether file extension has changed
			$extensionImagenActual=generalUtils::obtenerExtensionFichero($_POST["hdnNombreImagen_1"]);
			if($extensionImagenActual!=$extensionImagen){
				//Delete the old image from the server
				unlink("../files/members/".$_POST["hdnNombreImagen_1"]);
				unlink("../files/members/thumb/".$_POST["hdnNombreImagen_1"]);
			}
		}
	}
	
	//Update member's image in ed_tb_usuario_web
	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_imagen_guardar(".$idUsuarioWeb.",'".$nombreImagen."')");
?>