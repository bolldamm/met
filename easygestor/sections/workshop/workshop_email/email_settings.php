<?php
	/**
	 * 
	 * Script for tweaking workshop emails
	 * @Author Mike
	 * 
     * You have to edit /easygestor/config/controller.php
     * and /easygestor/config/sections.php too!
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){

      //Guardamos datos
      
      $opening=generalUtils::escaparCadena($_POST["txtOpening"]);
      $closing=generalUtils::escaparCadena($_POST["txtClosing"]);
      $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_workshop_email('".$opening."','".$closing."')");
      
      generalUtils::redirigir($_SERVER['HTTP_REFERER']);

    }
	
	// This would be necessary if the settings referred to a particular workshop
	// if(!isset($_GET["id_workshop"])){
	// 	generalUtils::redirigir("index.php");
	// }
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");

	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & close
	// $subPlantilla->parse("contenido_principal.item_button_close");

	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");

	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/workshop/workshop_email/manage_email_settings.html");

	//Obtenemos datos
	// require "../includes/settings.php";

    $plantilla->assign("TEXTAREA_ID","txtOpening");
    $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
    $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

    $plantilla->assign("TEXTAREA_ID","txtClosing");
    $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
    $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
	// $vectorMigas[2]["url"]="";
	$vectorMigas[2]["texto"]="Email settings";

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