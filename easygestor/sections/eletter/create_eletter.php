<?php
	/**
	 * 
	 * Script que muestra y realiza de un evio
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Inserto novedad
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_insertar('".generalUtils::escaparCadena($_POST["txtNombre"])."','".generalUtils::escaparCadena($_POST["txtaDescripcion"])."','".generalUtils::escaparCadena($_POST["txtPreview"])."')");
	
		
		generalUtils::redirigir("main_app.php?section=eletter&action=view");
	}

	

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/eletter/manage_eletter.html");
	
	
	$subPlantilla->parse("contenido_principal.bloque_maquetacion_descripcion");
	

	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ELETTER_CREATE_ELETTER_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_ELETTER_CREATE_ELETTER_TEXT;
	

	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ACTION","create");
	
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
			
	
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