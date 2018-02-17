<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */

	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del menu
		$idModulo=$_POST["cmbTipo"];
		$idPosicion=$_POST["hdnIdPosicion"];
		
		$idCategoriaMedia="null";
		if($_POST["cmbTipoMedia"]>0){
			$idCategoriaMedia = $_POST["cmbTipoMedia"];
		}
		
		$idTipoMiembro = ($_POST["cmbTipoMiembro"] == 0) ? "null" : $_POST["cmbTipoMiembro"];
		
		//Si la han pasado vacia o han pasado la 3, cosa que esta prohibido, pondremos el idPosicion=1
		if($idPosicion==3 || $idPosicion==""){
			$idPosicion=1;
		}
		
		$idPadre="null";
		$idMenuCopia="null";
		$parametroPadre="";
		if(isset($_POST["hdnIdPadre"]) && is_numeric($_POST["hdnIdPadre"])){
			$idPadre=$_POST["hdnIdPadre"];
			$parametroPadre="&id_padre=".$_POST["hdnIdPadre"];
			
			if(!gestorGeneralUtils::tienePermisoUsuarioMenu($db, $idPadre, 2)){
				generalUtils::redirigir("index.php");
			}
			
		}else{
			//Solo pueden entrar aqui los administradores
			if($_SESSION["user"]["rol_global"]!=1){
				generalUtils::redirigir("index.php");
			}
		}
		
		
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
		
		$idFormulario = ($_POST["cmbFormulario"] != 0) ? $_POST["cmbFormulario"] : "null";
		
		//Iniciar transaccion
		$db->startTransaction();
		//Insercion menu

		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_insertar(".$idPadre.",".$idModulo.",".$idTipoMiembro.",".$idFormulario.",".$idPosicion.",".$activo.",".$visible.")");
		$dato=$db->getData($resultado);
		$idMenu=$dato["id_menu"];

		//Asignar permisos usuarios lectura a este menu en concreto
		$idPermiso=1;
		$resultadoPermisoMenu=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_permiso_usuario_menu_insertar(".$idPermiso.",".$idMenu.")");
		
		if($idPadre!="null"){
			//Asignar permisos escritura a este menu a todos aquellos usuarios que tengan permisos de escritura para el padre
			$resultadoPermisoMenu=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_permiso_usuario_padre_menu_concreto_insertar(".$idMenu.")");
		}
		

		//Guardamos la informacion multidioma del menu
		require "language_menu.php";

	
		
		//Subimos ficheros
		/**
		 * 
		 * Nos indica el nombre de la tabla a la que atacaremos
		 * @var int
		 * 
		 */
		$idSeccion=1;
		$idElemento=$idMenu;
		$idPosicionMenu=$idPosicion;
		//Creamos vector personalizado con los ficheros a subir
		$i=0;
		foreach($_FILES as $clave=>$valor){
			$fichero[$i]["temporal"]=$valor["tmp_name"];
			$fichero[$i]["nombre_original"]=$valor["name"];
			$fichero[$i]["idImagen"]=0;
			$vectorPropiedades=explode("_",$clave);
			if(count($vectorPropiedades)==3){
				//Estamos con las imagenes multi
				$idPosicion=$vectorPropiedades[1];
				$idIdioma=$vectorPropiedades[2];
				$fichero[$i]["idioma"]=$idIdioma;
				$fichero[$i]["pie"]=$_POST["txtPie_".$idPosicion."_".$idIdioma];
			}
			$i++;
		}
		
		
		require "includes/load_upload_file.inc.php";
		
		//Cerrar transaccion
		$db->endTransaction();
		
				
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=menu&action=view".$parametroPadre."&hdnIdPosicion=".$idPosicionMenu);
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/menu/manage_menu.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$posicionReferencia=1;
	$parametroPadre="";
	$idPosicion=$_GET["posicion"];
	
	//Id padre
	if(isset($_GET["id_padre"]) && $_GET["id_padre"]!=-1){
		if(!gestorGeneralUtils::tienePermisoUsuarioMenu($db, $_GET["id_padre"], 2)){
			generalUtils::redirigir("index.php");
		}

		$subPlantilla->assign("PARAMETRO_ID_PADRE","&id_padre=".$_GET["id_padre"]);
		$subPlantilla->assign("ID_PADRE",$_GET["id_padre"]);
		
		//Obtenemos todos los menus padres del menu actual, incluido el actual
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_breadcumb(null,".$_GET["id_padre"].",".$_SESSION["user"]["language_id"].",'','')");
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
			
			if($i<$totalVectorIdMenu){
				//SubMenu
				$vectorMigas[$contadorMigas]["url"]=STATIC_BREADCUMB_INICIO_LINK."&id_padre=".$vectorIdMenu[$i]."&hdnIdPosicion=".$idPosicion;
				$vectorMigas[$contadorMigas]["texto"]=STATIC_BREADCUMB_MENU_SUB_MENU_TEXT;
				
				$contadorMigas++;
			}
		}
		
		//Establecemos nueva posicion en el vector para las migas
		$posicionReferencia=$contadorMigas;
		$parametroPadre="&id_padre=".$_GET["id_padre"];
	}else{
		//Solo pueden entrar aqui los administradores
		if($_SESSION["user"]["rol_global"]!=1){
			generalUtils::redirigir("index.php");
		}
	}
	
	$vectorMigas[$posicionReferencia]["url"]=STATIC_BREADCUMB_MENU_CREATE_MENU_LINK.$parametroPadre."&posicion=".$idPosicion;
	$vectorMigas[$posicionReferencia]["texto"]=STATIC_BREADCUMB_MENU_CREATE_MENU_TEXT;
	
		
	require "includes/load_breadcumb.inc.php";
	
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		if($i==0){
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$plantilla->assign("TEXTAREA_ID","txtaDescripcion_".$datoIdioma["id_idioma"]);
		$plantilla->assign("TEXTAREA_TOOLBAR","Basic");
		$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
				
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	

	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox estado
	$subPlantilla->assign("MENU_ESTADO","0");
	$subPlantilla->assign("ESTADO_CLASE","unChecked");
	
	//Atributos del checkbox visible
	$subPlantilla->assign("MENU_VISIBLE","0");
	$subPlantilla->assign("VISIBLE_CLASE","unChecked");
	
	$subPlantilla->assign("IMAGEN_ACLARACION_MEDIDAS",STATIC_MANAGE_MENU_SIZE_IMAGE);
	
	//Input imagenes
	for($i=0;$i<MAX_IMG_MENU_HEADER;$i++){
		$subPlantilla->assign("IMAGEN_CONTADOR",$i);
		$subPlantilla->assign("IMAGEN_CONTADOR_LABEL",($i+1));	
		$subPlantilla->parse("contenido_principal.bloque_imagen.item_imagen");	
	}
	if($i>0){
		$subPlantilla->parse("contenido_principal.bloque_imagen");	
	}
	
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");

	$contadorMenu=1;
	$subPlantilla->assign("COMBO_MENU_FORMULARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_formulario_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbFormulario","cmbFormulario",0,"descripcion","id_formulario",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	$subPlantilla->assign("COMBO_MENU_TIPO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_modulo_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipo","cmbTipo",1,"nombre","id_modulo","",0,"onchange='tratarCombosAsociados(this)'"));
	$subPlantilla->assign("MEDIA_VISIBLE","none");
	$subPlantilla->assign("COMBO_MENU_TIPO_USUARIO",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tipo_usuario_web_obtener_combo(".$_SESSION["user"]["language_id"].")","cmbTipoMiembro","cmbMiembro",0,"nombre","id_tipo_usuario_web",STATIC_MANAGE_MENU_TYPE_MEMBER_PUBLIC,0,""));
	
	$subPlantilla->parse("contenido_principal.item_edit_menu");
	
	$subPlantilla->assign("ID_POSICION", $_GET["posicion"]);
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>