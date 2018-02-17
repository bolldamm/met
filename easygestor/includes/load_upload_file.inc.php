<?php
	/**
	 * 
	 * Fichero que se encarga de la subida de ficheros de cada seccion de la aplicacion
	 * @Author eData
	 * 
	 */

	require "classes/phpThumb/ThumbLib.inc.php";

	/**
	 * 
	 * El total de ficheros subidos
	 * @var int
	 * 
	 */
	$totalFicheros=count($fichero);
	$i=0;
	
	//Recorremos ficheros
	for($i=0;$i<$totalFicheros;$i++){
		switch($idSeccion){
			//Menu
			case 1:
				$esActualizable=false;
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$idArchivo=$fichero[$i]["idImagen"];
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							if($idPosicionMenu==3){
								unlink("../files/backgrounds/".$fichero[$i]["imagenActual"]);
							}else{
								unlink("../files/menu/".$fichero[$i]["imagenActual"]);
							}
						}
					}else{
						$nombreGenerado=$fichero[$i]["imagenActual"];
					}
					$esActualizable=true;
					
				}else{
					if($fichero[$i]["temporal"]!=""){
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_archivo_crear(".$idElemento.", '".$_POST["txtEnlace_".$i]."')");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$esActualizable=true;
					}
				}
				
				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					//Actualizamos la imagen creada, asignando el nombre generado
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."', '".$_POST["txtEnlace_".$i]."')");
				}
				
				if($fichero[$i]["temporal"]!=""){
					//Creamos la imagen en el servidor
					$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
					
					if($idPosicionMenu==3){
						//Redimension imagen fondo
						$archivo->resize(WIDTH_SIZE_MENU_BACKGROUND,HEIGHT_SIZE_MENU_BACKGROUND);
						$archivo->save("../files/backgrounds/".$nombreGenerado);
					}else{
						//Redimension imagen fondo
						$archivo->resize(WIDTH_SIZE_MENU_HEADER,HEIGHT_SIZE_MENU_HEADER);
						$archivo->save("../files/menu/".$nombreGenerado);
					}
				}
				
				break;
			case 2:
				//Noticia
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$esActualizable=true;
					$idArchivo=$fichero[$i]["idImagen"];
					$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);
					//Si hemos subido imagen...
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							unlink("../files/new/".$fichero[$i]["imagenActual"]);
						}
					}else{
						//Mantenemos el nombre anterior
						$nombreGenerado=$idArchivo.".".$extensionActual;
					}
						
				}else{
					if($fichero[$i]["temporal"]!=""){
						$esActualizable=true;
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_archivo_crear(".$idElemento.",".$fichero[$i]["idioma"].")");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
					}else{
						$esActualizable=false;
					}
				}
					

				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."','".generalUtils::escaparCadena($fichero[$i]["pie"])."')");
				}
				
				if($fichero[$i]["temporal"]!=""){
					//Creamos la imagen en el servidor
					$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
					
					//Redimension imagen fondo
					$archivo->resize(WIDTH_SIZE_NEW,HEIGHT_SIZE_NEW);
					$archivo->save("../files/new/".$nombreGenerado);
				}
			
				break;
			case 3:
				//Agenda
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$esActualizable=true;
					$idArchivo=$fichero[$i]["idImagen"];
					$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);
					//Si hemos subido imagen...
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							unlink("../files/diary/".$fichero[$i]["imagenActual"]);
						}
					}else{
						//Mantenemos el nombre anterior
						$nombreGenerado=$idArchivo.".".$extensionActual;
					}
						
				}else{
					if($fichero[$i]["temporal"]!=""){
						$esActualizable=true;
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_archivo_crear(".$idElemento.",".$fichero[$i]["idioma"].")");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
					}else{
						$esActualizable=false;
					}
				}
					

				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."','".generalUtils::escaparCadena($fichero[$i]["pie"])."')");
				}
				
				if($fichero[$i]["temporal"]!=""){
					//Creamos la imagen en el servidor
					$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
					
					//Redimension imagen fondo
					$archivo->resize(WIDTH_SIZE_DIARY,HEIGHT_SIZE_DIARY);
					$archivo->save("../files/diary/".$nombreGenerado);
				}
			
				break;
			case 5:
				//Nota prensa
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$esActualizable=true;
					$idArchivo=$fichero[$i]["idImagen"];
					$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);
					//Si hemos subido imagen...
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							unlink("../files/press/".$fichero[$i]["imagenActual"]);
						}
					}else{
						//Mantenemos el nombre anterior
						$nombreGenerado=$idArchivo.".".$extensionActual;
					}
						
				}else{
					if($fichero[$i]["temporal"]!=""){
						$esActualizable=true;
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_nota_prensa_archivo_crear(".$idElemento.",".$fichero[$i]["idioma"].")");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
					}else{
						$esActualizable=false;
					}
				}
					

				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_nota_prensa_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."','".generalUtils::escaparCadena($fichero[$i]["pie"])."')");
				}
				
				if($fichero[$i]["temporal"]!=""){
					//Creamos la imagen en el servidor
					$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
					
					//Redimension imagen fondo
					$archivo->resize(WIDTH_SIZE_PRESS,HEIGHT_SIZE_PRESS);
					$archivo->save("../files/press/".$nombreGenerado);
				}
			
				break;
			case 6:
				//Clipping
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$esActualizable=true;
					$idArchivo=$fichero[$i]["idImagen"];
					$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);
					//Si hemos subido imagen...
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							unlink("../files/clipping/".$fichero[$i]["imagenActual"]);
						}
					}else{
						//Mantenemos el nombre anterior
						$nombreGenerado=$idArchivo.".".$extensionActual;
					}
						
				}else{
					if($fichero[$i]["temporal"]!=""){
						$esActualizable=true;
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_clipping_archivo_crear(".$idElemento.",".$fichero[$i]["idioma"].")");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_clipping_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
					}else{
						$esActualizable=false;
					}
				}
					

				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_clipping_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."','".generalUtils::escaparCadena($fichero[$i]["pie"])."')");
				}
				
				if($fichero[$i]["temporal"]!=""){
					//Creamos la imagen en el servidor
					$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
					
					//Redimension imagen fondo
					$archivo->resize(WIDTH_SIZE_CLIPPING,HEIGHT_SIZE_CLIPPING);
					$archivo->save("../files/clipping/".$nombreGenerado);
				}
			
				break;
			case 7:
				//Newsletter
				//Actualizamos una imagen existente
				if($fichero[$i]["idImagen"]!=0){
					$esActualizable=true;
					$idArchivo=$fichero[$i]["idImagen"];
					$extensionActual=generalUtils::obtenerExtensionFichero($fichero[$i]["imagenActual"]);

					//Si hemos subido imagen...
					if($fichero[$i]["temporal"]!=""){
						$extensionNueva=generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
						$nombreGenerado=$idArchivo.".".$extensionNueva;
						
						//Si se ha cambiado la extension, borramos la imagen previa
						if($extensionNueva!=$extensionActual){
							unlink("../files/newsletter/".$fichero[$i]["imagenActual"]);
						}
					}else{
						//Mantenemos el nombre anterior
						$nombreGenerado=$idArchivo.".".$extensionActual;
					}
						
				}else{
					if($fichero[$i]["temporal"]!=""){
						$esActualizable=true;
						//Creamos la imagen en base de datos, para obtener el id y aplicarselo al nombre de la imagen
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_newsletter_archivo_crear(".$idElemento.",".$fichero[$i]["idioma"].")");
						$dato=$db->getData($resultado);
						$idArchivo=$dato["id_newsletter_archivo"];
	
						//Asignamos el nombre de la imagen que guardaremos en base de datos
						$nombreGenerado=$idArchivo.".".generalUtils::obtenerExtensionFichero($fichero[$i]["nombre_original"]);
					}else{
						$esActualizable=false;
					}
				}
					

				//Actualizamos la imagen creada, asignando el nombre generado
				if($esActualizable){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_newsletter_archivo_actualizar(".$idArchivo.",'".$nombreGenerado."','')");
				}
				
				if($fichero[$i]["temporal"]!=""){ 
					$tipoImagen = explode("/", $fichero[$i]["tipo"]);
					if($tipoImagen[0] == "image"){
						//Creamos la imagen en el servidor
						$archivo=PhpThumbFactory::create($fichero[$i]["temporal"]);
						
						//Redimension imagen fondo
						$archivo->resize(WIDTH_SIZE_CLIPPING,HEIGHT_SIZE_CLIPPING);
						$archivo->save("../files/newsletter/".$nombreGenerado);
					}else{
						move_uploaded_file($fichero[$i]["temporal"], "../files/newsletter/".$nombreGenerado);
					}
				}
			
				break;
		}
	}

?>