<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales de la noticia
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo"]);
		$descripcionPrevia=generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);
		
			
		//Insercion noticia
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_insertar(".$_POST["cmbTematica"].",'".$titulo."','".$descripcionPrevia."','".$descripcion."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."',".$activo.")");		

		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=new&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/new/manage_new.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_NEW_VIEW_NEW_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_NEW_VIEW_NEW_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_NEW_CREATE_NEW_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_NEW_CREATE_NEW_TEXT;
		
	require "includes/load_breadcumb.inc.php";
	
	//Editor descripcion previa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcionPrevia");
	$plantilla->assign("TEXTAREA_TOOLBAR","Basic_New");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Basic_New");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox
	$subPlantilla->assign("NOTICIA_ESTADO","0");
	$subPlantilla->assign("ESTADO_CLASE","unChecked");

	//Combo tematica
	$subPlantilla->assign("COMBO_TEMATICA",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tematica_obtener_combo(-1,".$_SESSION["user"]["language_id"].")","cmbTematica","cmbTematica",0,"nombre","id_tematica",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFecha");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>