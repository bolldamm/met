<?php
	/**
	 * 
	 * Pantalla de perfil del usuario autenticado en el sistema
	 * @Author eData
	 * 
	 */
		
	//Si hemos enviado el formulario...
	if($esPost){
		$scriptActual="main_app.php?section=user&action=profile";
		//Validamos si la contraseña actual es la correcta
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_existe_clave(".$_SESSION["user"]["id"].",'".generalUtils::escaparCadena($_POST["txtPasswordActual"])."')");
		
		//Si es correcto, entonces proseguimos modificando los valores del perfil
		if($db->getNumberRows($resultado)==1){
			$nombreImagen=$_SESSION["user"]["avatar"];
			
			//Vaciamos sesion imagen, borramos imagen del servidor y ponemos a vacio el nombre de la imagen
			if($_POST["hdnEliminarImagen"]==1){
				unlink("files/user/avatar/".$_SESSION["user"]["avatar"]);
				$_SESSION["user"]["avatar"]="";
				$nombreImagen="";
			}
			
			//Si hemos introducido imagen...
			if($_FILES["fileImagen"]["tmp_name"]){
				//Cargamos clase phpthumb
				require "classes/phpThumb/ThumbLib.inc.php";
				$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
				$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
				$nombreImagen=$_SESSION["user"]["id"].".".$extensionImagen;
				
				//Redimension imagen fondo
				$thumb->resize(WIDTH_SIZE_PROFILE,HEIGHT_SIZE_PROFILE);
				$thumb->save("files/user/avatar/".$nombreImagen);
				
				//Comprobamos si antes tenia imagen
				if($_SESSION["user"]["avatar"]!=""){
					//Miramos si ha cambiado la extension
					$extensionImagenActual=generalUtils::obtenerExtensionFichero($_SESSION["user"]["avatar"]);
					if($extensionImagenActual!=$extensionImagen){
						//Borramos la imagen antigua del servidor
						unlink("files/user/avatar/".$_SESSION["user"]["avatar"]);
					}
				}
				//Nueva imagen a la sesion
				$_SESSION["user"]["avatar"]=$nombreImagen;
			}
			
			//Si hemos cambiado de idioma...
			if($_SESSION["user"]["language_id"]!=$_POST["cmbIdioma"]){
				//Obtener diccionario del nuevo idioma introducido
				$resultadoDiccionario=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_diccionario_obtener_gestor(".$_POST["cmbIdioma"].")");
				$datoDiccionario=$db->getData($resultadoDiccionario);
				//Establecemos el diccionario del idioma escogido
				$_SESSION["user"]["language_id"]=$_POST["cmbIdioma"];
				$_SESSION["user"]["language_dictio"]=$datoDiccionario["diccionario"];
			}
			
			//Actualizamos perfil
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_perfil_editar(".$_SESSION["user"]["id"].",".$_POST["cmbIdioma"].",'".$nombreImagen."','".$_POST["txtPassword"]."')");
			
			//Realizamos los cambios
			generalUtils::redirigir($scriptActual."&code=1");
		}else{
			//La contraseña actual introducida es incorrecta.
			generalUtils::redirigir($scriptActual."&code=0");
		}
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/".SECTION_USER_PATH."profile_user.html");
	
	if(isset($_GET["code"])){
		/**
		 * 
		 * Nos indica si se tiene que mostrar una notificacion al hacer onload en la pagina
		 * @var boolean
		 * 
		 */
		$esNotify=false;
		switch($_GET["code"]){
			case 0:
				$esNotify=true;
				$tipoNotify=1;
				$mensajeNotify=STATIC_USER_PROFILE_INVALID_PASSWORD;
				break;
			case 1:
				$esNotify=true;
				$tipoNotify=2;
				$mensajeNotify=STATIC_USER_PROFILE_SUCCESS;
				break;
		}
		if($esNotify){
			$plantilla->assign("ITEM_EASYNOTIFY",$mensajeNotify);
			$plantilla->assign("TYPE_EASYNOTIFY",$tipoNotify);
			$plantilla->parse("contenido_principal.carga_inicial.autoload_easynotify");
		}
	}
	
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Combo idioma
	$subPlantilla->assign("COMBO_IDIOMA",generalUtils::construirCombo($db,"CALL ed_sp_idioma_obtener_gestor()","cmbIdioma","cmbIdioma",$_SESSION["user"]["language_id"],"nombre","id_idioma",STATIC_GLOBAL_COMBO_DEFAULT,"",""));	
	
	//Si hay imagen
	if($_SESSION["user"]["avatar"]!=""){
		$subPlantilla->assign("AVATAR_USER",$_SESSION["user"]["avatar"]);
		$subPlantilla->parse("contenido_principal.propiedades_imagen");
	}
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_USER_PROFILE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_USER_PROFILE_TEXT;
	
	require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>