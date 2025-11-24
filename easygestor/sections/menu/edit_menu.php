<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del menu
		$idPosicion=$_POST["hdnIdPosicion"];

		$idModulo=$_POST["cmbTipo"];
		$idCategoriaMedia="null";
		/*if($_POST["cmbTipoMedia"]!="0"){
			$idCategoriaMedia=$_POST["cmbTipoMedia"];
		}*/
		
		$idTipoMiembro = ($_POST["cmbTipoMiembro"] == 0) ? "null" : $_POST["cmbTipoMiembro"];
		
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		
		if(isset($_POST["hdnVisible"])){
			$visible=$_POST["hdnVisible"];
		}else{
			$visible=0;
		}
      
		require "includes/load_webify.inc.php";	
		if($_POST["hdnWebify"]){
          $_POST["txtaDescripcion_3"] = Webify($_POST["txtaDescripcion_3"]);
		}
      
		$idMenu=$_POST["hdnIdMenu"];

        //Define $parametroPadre before "if" to prevent php notices
        $parametroPadre = '';
		if(isset($_POST["hdnIdPadre"]) && is_numeric($_POST["hdnIdPadre"])){
			$idPosicion=1;
			//Miramos si tiene permiso de escritura
			if(!gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_POST["hdnIdMenu"], 2) && !gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_POST["hdnIdPadre"], 2)){
				generalUtils::redirigir("index.php");
			}
			
			$parametroPadre="&id_padre=".$_POST["hdnIdPadre"];
		}else{
			//Miramos si tiene permiso de escritura
			if(!gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_POST["hdnIdMenu"], 2)){
				generalUtils::redirigir("index.php");
			}
		}
		
		$idFormulario = ($_POST["cmbFormulario"] != 0) ? $_POST["cmbFormulario"] : "null";
		
		$idMenuPadre="null";
		if(isset($_POST["hdnIdPadreNuevo"]) && $_POST["hdnIdPadreNuevo"]>0){
			$idMenuPadre=$_POST["hdnIdPadreNuevo"];
		}
		
		//es home
		if($_POST["hdnIdPadre"]==83){
			$idMenuPadre=$_POST["hdnIdPadre"];
		}

		//Iniciar transaccion
		$db->startTransaction();
		
		//Insercion menu
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_editar(".$idMenu.",".$idMenuPadre.",".$idFormulario.",".$idModulo.",".$idTipoMiembro.",".$activo.",".$visible.")");

		//Guardamos la informacion multidioma del menu
		require "language_menu.php";
		

		if($idPosicion==3){
			$totalImagenes=MAX_IMG_MENU_BACKGROUND;
			$ruta="backgrounds";
		}else{
			$totalImagenes=MAX_IMG_MENU_HEADER;
			$ruta="menu";
		}
		//Eliminamos imagen de menu
		for($i=0;$i<$totalImagenes;$i++){
			if(isset($_POST["hdnEliminarImagen_".$i]) && $_POST["hdnEliminarImagen_".$i]==1){
				$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_archivo_eliminar(".$_POST["hdnIdImagen_".$i].")");
				unlink("../files/".$ruta."/".$_POST["hdnNombreImagen_".$i]);
			}
		}

		//Upload files
		/**
		 *
		 * Nos indica el nombre de la tabla a la que atacaremos
		 * @var int
		 *
		 */
		$idSeccion=1;
		$idElemento=$idMenu;
		$idPosicionMenu=$idPosicion;

		$i=0;
        /*
         * Initially, $_FILES is an empty array with error=4
         * This functions checks each element in the array
         * If the error is 0 (UPLOAD_ERR_OK), the function returns false
         * If all files return false, $uploadedFiles is null
         * so the program does not enter the foreach loop
         */
        $uploadedFiles = array_filter($_FILES, function($file){
		    return $file['error'] == UPLOAD_ERR_OK;
        });

        //Create array of files to be uploaded (define array first for case where there are no files)
		$fichero = [];
		foreach($uploadedFiles as $clave=>$valor){
			$fichero[$i]["temporal"]=$valor["tmp_name"];
			$fichero[$i]["nombre_original"]=$valor["name"];
			$fichero[$i]["idImagen"]= $_POST["hdnIdImagen_".$i];
			$fichero[$i]["imagenActual"]= $_POST["hdnNombreImagen_".$i];
			$vectorPropiedades=explode("_",$clave);
			if(count($vectorPropiedades)==3){
				//Estamos con las imagenes multi
				$idPosicion=$vectorPropiedades[1];
				$idIdioma=$vectorPropiedades[2];
				$fichero[$i]["idImagen"]=$_POST["hdnIdImagen_".$idPosicion."_".$idIdioma];
				$fichero[$i]["imagenActual"]=$_POST["hdnNombreImagen_".$idPosicion."_".$idIdioma];
				$fichero[$i]["pie"]=$_POST["txtPie_".$idPosicion."_".$idIdioma];
				$fichero[$i]["descripcion"]=$_POST["txtaDescripcion_".$idPosicion."_".$idIdioma];
//				$fichero[$i]["descripcion"]="WEBIFIED" . $_POST["txtaDescripcion_".$idPosicion."_".$idIdioma];              
				$fichero[$i]["url"]=$_POST["txtUrl_".$idPosicion."_".$idIdioma];
				$fichero[$i]["remoto"]=$_POST["hdnRemoto_".$idPosicion."_".$idIdioma];
				$fichero[$i]["idioma"]=$idIdioma;
			}

			$i++;
		}
		
		require "includes/load_upload_file.inc.php";	
		
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=menu&action=view".$parametroPadre."&hdnIdPosicion=".$idPosicionMenu);
		}else{
			generalUtils::redirigir("main_app.php?section=menu&action=edit".$parametroPadre."&id_menu=".$idMenu);
		}
	}

	
	if(!isset($_GET["id_menu"]) || !is_numeric($_GET["id_menu"]) || $_GET["id_menu"]==143 || $_GET["id_menu"]==164){
		generalUtils::redirigir("main_app.php?section=menu&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/menu/manage_menu.html");
		
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	$totalImagenes = 0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion del menu en cada idioma
		$resultadoMenu=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_concreto(".$_GET["id_menu"].",".$datoIdioma["id_idioma"].")");
		$datoMenu=$db->getData($resultadoMenu);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("SEO_MISSING",$datoMenu["seo_title"]);		
		//Obtenemos url real menu
		$vectorAtributosMenu["idioma"]=$datoIdioma["siglas"];
		$vectorAtributosMenu["id_menu"]=$_GET["id_menu"];
		$vectorAtributosMenu["seo_url"]=$datoMenu["seo_url"];
	
		$subPlantilla->assign("MENU_URL_REAL", CURRENT_DOMAIN.generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
		
		
		if($i==0){
			$nombreMenu=$datoMenu["nombre"];
			$idPadre=$datoMenu["id_padre"];
			$idPosicion=$datoMenu["id_posicion"];
			

		
			
			if($datoMenu["id_modulo"]!=1){
				$subPlantilla->assign("DISPLAY_FORMULARIO","style='display:none;'");
			}
			
			$subPlantilla->assign("COMBO_MENU_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_modulo_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",$datoMenu["id_modulo"],"nombre","id_modulo","",0,"onchange='tratarCombosAsociados(this)'"));
			$subPlantilla->assign("COMBO_MENU_TIPO_USUARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoMiembro","cmbMiembro",$datoMenu["id_tipo_usuario_web"],"nombre","id_tipo_usuario_web",STATIC_MANAGE_MENU_TYPE_MEMBER_PUBLIC,0,""));
			$subPlantilla->assign("COMBO_MENU_FORMULARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_formulario_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbFormulario","cmbFormulario",$datoMenu["id_formulario"],"descripcion","id_formulario",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
			//La primera vez, miramos si el menu esta activo o no
			if($datoMenu["activo"]==1){
				$subPlantilla->assign("ESTADO_CLASE","checked");
			}else{
				$subPlantilla->assign("ESTADO_CLASE","unChecked");
			}
			$subPlantilla->assign("MENU_ESTADO",$datoMenu["activo"]);
			
		
			if($datoMenu["visible"]==1){
				$subPlantilla->assign("VISIBLE_CLASE","checked");
			}else{
				$subPlantilla->assign("VISIBLE_CLASE","unChecked");
			}
			$subPlantilla->assign("MENU_VISIBLE",$datoMenu["visible"]);
			
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->assign("MENU_TITULO",$datoMenu["nombre"]);
//		$webify = true;
		if ($webify) {
			$datoMenu["descripcion"] = "WEBIFIED" . $datoMenu["descripcion"]; 
		}       
		$subPlantilla->assign("MENU_DESCRIPCION",$datoMenu["descripcion"]);
		/*$subPlantilla->assign("MENU_DESCRIPCION_SLIDER",$datoMenu["descripcion_slider"]);*/
		
		$plantilla->assign("TEXTAREA_ID","txtaDescripcion_".$datoIdioma["id_idioma"]);
		$plantilla->assign("TEXTAREA_TOOLBAR","Basic");
		$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
		
		if($idPosicion != 3) {
			$totalImagenes = MAX_IMG_MENU_HEADER;
			/*$subPlantilla->parse("contenido_principal.item_contenido_idioma.campo_descripcion_slider");
			$plantilla->assign("TEXTAREA_ID","txtDescripcionSlider_".$datoIdioma["id_idioma"]);
			$plantilla->assign("TEXTAREA_TOOLBAR","Minimo");
			$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");*/
		}else{
			if(!$idPadre){
				$totalImagenes = MAX_IMG_MENU_BACKGROUND;
			}
			
		}
		$subPlantilla->parse("contenido_principal.item_contenido_idioma.bloque_url_real");
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	
	

	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	
	$mostrarSubMenu=true;
	$i=0;
	$contadorMenu=1;
	if($idPadre==""){
		$idPadreAux="null";
		//Miramos si tiene permiso de escritura
		if(gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_GET["id_menu"], 2)){
			$subPlantilla->parse("contenido_principal.item_edit_menu");
			//Incluimos save & clsoe
			$subPlantilla->parse("contenido_principal.item_button_close");
		}
		
		$vectorMigas[1]["url"]=STATIC_BREADCUMB_MENU_EDIT_MENU_LINK."&id_menu=".$_GET["id_menu"]."&posicion=".$idPosicion;
		$vectorMigas[1]["texto"]=$nombreMenu;
		$esPadre=false;
	}else{
		$idPadreAux=$idPadre;


		//Miramos si tiene permiso de escritura
		if(gestorGeneralUtils::tienePermisoUsuarioMenu($db, $idPadre, 2) || gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_GET["id_menu"], 2)){
			$subPlantilla->parse("contenido_principal.item_edit_menu");
			//Incluimos save & clsoe
			$subPlantilla->parse("contenido_principal.item_button_close");
		}
		
		
		
		$subPlantilla->assign("ID_PADRE",$idPadre);
		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$idPadre);
		
		//Obtenemos todos los menus padres del menu actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_breadcumb(null,".$_GET["id_menu"].",".$_SESSION["user"]["language_id"].",'','')");
		$dato=$db->getData($resultado);
		//Generamos vector con los id menu de los datos separados por |
		$vectorIdMenu=explode("|",$dato["id_menu"]);
		//Generamos vector con los nombre menu de los datos separados por |
		$vectorNombreMenu=explode("|",$dato["nombre_menu"]);
		
		//Invertimos ambos vectores
		$vectorIdMenu=array_reverse($vectorIdMenu);
		$totalVectorIdMenu=count($vectorIdMenu);
		$vectorNombreMenu=array_reverse($vectorNombreMenu);
		
		$contadorMigas=1;
		for($i=0;$i<$totalVectorIdMenu;$i++){
			$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_MENU_EDIT_MENU_LINK."&id_menu=".$vectorIdMenu[$i]."&posicion=".$idPosicion;
			$vectorMigas[$contadorMigas]["texto"]=$vectorNombreMenu[$i];
			
			$contadorMigas++;
			
			if($i<$totalVectorIdMenu-1){
				//SubMenu
				$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_INICIO_LINK."&id_padre=".$vectorIdMenu[$i]."&hdnIdPosicion=".$idPosicion;
				$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_MENU_SUB_MENU_TEXT;
				
				$contadorMigas++;
			}
		}
		if($totalVectorIdMenu>4 || $idPosicion==3){
			$mostrarSubMenu=false;
		}
		$esPadre=true;
	}
	
	if($idPosicion!=3){
		//Obtenemos todos los menus padres del menu actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_jerarquia('',".$idPadreAux.",'')");
		$dato=$db->getData($resultado);
		
		
		//Generamos vector con los id menu de los datos separados por |
		$vectorIdMenu=explode("|",$dato["id_menu"]);
	
		//Invertimos ambos vectores
		$vectorIdMenu=array_reverse($vectorIdMenu);
		$totalVectorIdMenu=count($vectorIdMenu);
		
		for($i=0;$i<$totalVectorIdMenu;$i++){
			if($i==0){
				$idPadreAux="null";
			}else{
				$idPadreAux=$vectorIdMenu[$i-1];
			}
			
			$subPlantilla->assign("COMBO_MENU_COPY",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_combo(".$idPadreAux.",".$_SESSION["user"]["language_id"].",".$_GET["id_menu"].")","cmbMenu_".$contadorMenu,"cmbMenu".$contadorMenu,$vectorIdMenu[$i],"nombre","id_menu",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboMenu(this)'"));
			$subPlantilla->assign("CONTADOR_MENU",$contadorMenu);
			$subPlantilla->parse("contenido_principal.bloque_menu_hereda.item_menu_combo");
			$contadorMenu++;
		}
	

		$subPlantilla->assign("ID_MENU_COPIA",$vectorIdMenu[$i-1]);
		$subPlantilla->parse("contenido_principal.bloque_menu_hereda.bloque_menu_original");
		
		
		if($i==0){
			$idMenuCopia="null";
		}else{
			$idMenuCopia=0;
		}
		$subPlantilla->assign("COMBO_MENU_COPY",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_menu_obtener_combo(".$idMenuCopia.",".$_SESSION["user"]["language_id"].",".$_GET["id_menu"].")","cmbMenu_".$contadorMenu,"cmbMenu".$contadorMenu,0,"nombre","id_menu",STATIC_GLOBAL_COMBO_DEFAULT,0,"onchange='obtenerComboMenu(this)'"));
		$subPlantilla->assign("CONTADOR_MENU",$contadorMenu);
		$subPlantilla->parse("contenido_principal.bloque_menu_hereda.item_menu_combo");
		
		$subPlantilla->parse("contenido_principal.bloque_menu_hereda");
		
		//Contador menu
		$plantilla->assign("CONTADOR_MENU",$contadorMenu);
		$plantilla->parse("contenido_principal.variables_incrementales.menu_combo_contador");
		$plantilla->parse("contenido_principal.variables_incrementales");
	}
	
	require "includes/load_breadcumb.inc.php";
	

	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_MENU",$_GET["id_menu"]);
	$subPlantilla->assign("ACTION","edit");
	

	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	
	

	$subPlantilla->assign("ID_POSICION", $idPosicion);
	
	//Submenu
	if(($idPosicion==1 || ($idPosicion==3 && !$esPadre)) && $mostrarSubMenu){
		$subPlantilla->parse("contenido_principal.item_submenu");
	}
	
	$subPlantilla->assign("IMAGEN_ACLARACION_MEDIDAS",STATIC_MANAGE_MENU_SIZE_IMAGE);
	$subPlantilla->assign("RUTA_IMAGEN","menu");
	
	if($idPosicion==3){
		if($esPadre){
			$subPlantilla->assign("DISPLAY_ITEM","style='display:none;'");
			$subPlantilla->assign("DISPLAY_FORMULARIO","style='display:none;'");
			$subPlantilla->assign("ID_POSICION", 1);
		}else{
			$subPlantilla->assign("RUTA_IMAGEN","backgrounds");
			$subPlantilla->assign("IMAGEN_ACLARACION_MEDIDAS",STATIC_MANAGE_MENU_SIZE_IMAGE_BACKGROUND);
			$subPlantilla->assign("DISPLAY_ENLACE","style='display:none;'");
			
		}
	}

	//Input imagenes
	for($i=0;$i<$totalImagenes;$i++){
		$subPlantilla->assign("IMAGEN_CONTADOR",$i);
		$subPlantilla->assign("IMAGEN_CONTADOR_LABEL",($i+1));
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_archivo_obtener(".$_GET["id_menu"].",'LIMIT ".$i.",1');");
		if($db->getNumberRows($resultado)==1){
			$dato=$db->getData($resultado);
			$subPlantilla->assign("IMAGEN_MENU",$dato["nombre"]);
			$subPlantilla->assign("IMAGEN_MENU_ENLACE",$dato["url"]);
			$subPlantilla->assign("IMAGEN_MENU_ID",$dato["id_menu_archivo"]);
			$subPlantilla->parse("contenido_principal.bloque_imagen.item_imagen.item_imagen_opciones");	
		}else{
			$subPlantilla->assign("IMAGEN_MENU_ENLACE","");
		}
		$subPlantilla->parse("contenido_principal.bloque_imagen.item_imagen");	
	}
	if($i>0){
		$subPlantilla->parse("contenido_principal.bloque_imagen");	
	}
	
	
	

	
	//Si somos rol seo, veremos el boton
	if(gestorGeneralUtils::tieneUsuarioRolSeo()){
		$subPlantilla->parse("contenido_principal.item_seo_menu");
	}
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>