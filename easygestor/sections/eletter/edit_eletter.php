<?php
	/**
	 * 
	 * Script que muestra y realiza de un envio
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_editar(".$_POST["hdnIdEletter"].",'".generalUtils::escaparCadena($_POST["txtNombre"])."','".generalUtils::escaparCadena($_POST["txtaDescripcion"])."','".generalUtils::escaparCadena($_POST["txtPreview"])."')");
	
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=eletter&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=eletter&action=edit&id_eletter=".$_POST["hdnIdEletter"]);
		}
	}

	
	if(!isset($_GET["id_eletter"]) || !is_numeric($_GET["id_eletter"])){
		generalUtils::redirigir("index.php");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/eletter/manage_eletter.html");
	
	$resultadoEletter=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_obtener_concreta(".$_GET["id_eletter"].")");
	$datoEletter=$db->getData($resultadoEletter);
	
	$subPlantilla->assign("ELETTER_ASUNTO",$datoEletter["titulo"]);
	$subPlantilla->assign("ELETTER_DESCRIPCION",$datoEletter["descripcion"]);
    $subPlantilla->assign("ELETTER_PREVIEW",$datoEletter["teaser"]);
	$subPlantilla->parse("contenido_principal.bloque_descripcion_existente");
	
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
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ELETTER_EDIT_ELETTER_LINK."&id_eletter=".$_GET["id_eletter"];
	$vectorMigas[2]["texto"]=$datoEletter["titulo"];
	

	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_ELETTER",$_GET["id_eletter"]);
	$subPlantilla->assign("ACTION","edit");
	
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & clsoe
	$subPlantilla->parse("contenido_principal.item_button_close");
	
			
	//Boton historial

	$subPlantilla->parse("contenido_principal.boton_historial");
	
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