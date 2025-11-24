<?php
	/**
	 * 
	 * Script for showing movement buttons 
	 * @Author Mike
	 * 
     * You have to edit /easygestor/config/controller.php
     * and /easygestor/config/sections.php too!
	 */
	
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");

	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");

	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/workshop/buttons/view_buttons.html");

	//Migas de pan
	$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"] = STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"] = STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
	$vectorMigas[2]["url"]="";
	$vectorMigas[2]["texto"]="Buttons";

	require "includes/load_breadcumb.inc.php";
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>