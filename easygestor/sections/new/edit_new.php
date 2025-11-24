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
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		$idNoticia=$_POST["hdnIdNoticia"];
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo"]);
		$descripcionPrevia=generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);
		
		//Insercion noticia
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_editar(".$idNoticia.",".$_POST["cmbTematica"].",'".$titulo."','".$descripcionPrevia."','".$descripcion."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."',".$activo.")");

		
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=new&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=new&action=edit&id_noticia=".$idNoticia);
		}
	}

	
	if(!isset($_GET["id_noticia"]) || !is_numeric($_GET["id_noticia"])){
		generalUtils::redirigir("main_app.php?section=new&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/new/manage_new.html");
		
	//Sacamos la informacion del menu en cada idioma
	$resultadoMenu=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_obtener_concreta(".$_GET["id_noticia"].")");
	$datoNoticia=$db->getData($resultadoMenu);
	
	if($datoNoticia["id_usuario_web"]){
		$subPlantilla->assign("NOTICIA_USUARIO","<a href='main_app.php?section=member&action=edit&id_miembro=".$datoNoticia["id_usuario_web"]."&origen=1'>".$datoNoticia["correo_electronico"]."</a>");
		$subPlantilla->parse("contenido_principal.bloque_usuario");
	}
	
	$tituloNoticia=$datoNoticia["titulo"];
	$idTematica=$datoNoticia["id_tematica"];
	//La primera vez, miramos si la noticia esta activo o no
	if($datoNoticia["activo"]==1){
		$subPlantilla->assign("ESTADO_CLASE","checked");
	}else{
		$subPlantilla->assign("ESTADO_CLASE","unChecked");
	}
	$subPlantilla->assign("NOTICIA_ESTADO",$datoNoticia["activo"]);
	$subPlantilla->assign("NOTICIA_FECHA",generalUtils::conversionFechaFormato($datoNoticia["fecha"]));

	$subPlantilla->assign("NOTICIA_TITULO",htmlspecialchars($datoNoticia["titulo"]));
	$subPlantilla->assign("NOTICIA_DESCRIPCION_PREVIA",$datoNoticia["descripcion_previa"]);		
	$subPlantilla->assign("NOTICIA_DESCRIPCION",$datoNoticia["descripcion_completa"]);
	
	
	//Editor descripcion previa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcionPrevia");
	$plantilla->assign("TEXTAREA_TOOLBAR","Basic_New");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Basic_New");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
		
		

	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_NEW_VIEW_NEW_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_NEW_VIEW_NEW_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_NEW_EDIT_NEW_LINK."&id_noticia=".$_GET["id_noticia"];
	$vectorMigas[2]["texto"]=$tituloNoticia;

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_NOTICIA",$_GET["id_noticia"]);
	$subPlantilla->assign("ACTION","edit");
	
	//Combo tematica
	$subPlantilla->assign("COMBO_TEMATICA",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tematica_obtener_combo(-1,".$_SESSION["user"]["language_id"].")","cmbTematica","cmbTematica",$idTematica,"nombre","id_tematica",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	$subPlantilla->parse("contenido_principal.item_button_close");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFecha");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
		
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Submenu
	$subPlantilla->parse("contenido_principal.item_submenu");
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>