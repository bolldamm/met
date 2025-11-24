<?php
	require "classes/phpThumb/ThumbLib.inc.php";
	
	//Tanto crear area como editar area, atacaran a este fichero

	//Obtenemos idiomas
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$url=generalUtils::escaparCadena($_POST["txtUrl_".$datoIdioma["id_idioma"]]);
		$nombreGenerado="";

		
		if($_FILES["fileImagen_".$datoIdioma["id_idioma"]]["tmp_name"]!=""){
			$extensionFichero=generalUtils::obtenerExtensionFichero($_FILES["fileImagen_".$datoIdioma["id_idioma"]]["name"]);
			$nombreGenerado=$idBotonAcceso."_".$datoIdioma["id_idioma"].".".$extensionFichero;
			//Creamos la imagen en el servidor
			$archivo=PhpThumbFactory::create($_FILES["fileImagen_".$datoIdioma["id_idioma"]]["tmp_name"]);
			
			//Redimension imagen fondo
			$archivo->resize(WIDTH_SIZE_ACCESS_BUTTON,HEIGHT_SIZE_ACCESS_BUTTON);
			$archivo->save("../files/sections/".$nombreGenerado);
		}else{
			if(isset($_POST["hdnNombrePreview_".$datoIdioma["id_idioma"]])){
				if($_POST["hdnEliminarPreview_".$datoIdioma["id_idioma"]]==0){
					$nombreGenerado=$_POST["hdnNombrePreview_".$datoIdioma["id_idioma"]];
				}else{
					unlink("../files/sections/".$_POST["hdnNombrePreview_".$datoIdioma["id_idioma"]]);
				}
			}
		}
		
		//Insertamos datos multidioma del boton por cada idioma
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_idioma_guardar(".$idBotonAcceso.",".$datoIdioma["id_idioma"].",'".$nombreGenerado."','".$url."')");	
	}
?>