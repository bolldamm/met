<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de una agenda
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales de la agenda
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		$idAgenda=$_POST["hdnIdAgenda"];
		
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo"]);
		$descripcionPrevia=generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);
		
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_editar(".$idAgenda.",".$_POST["cmbTematica"].",'".$titulo."','".$descripcionPrevia."','".$descripcion."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."',".$activo.")");

	
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=diary&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=diary&action=edit&id_agenda=".$idAgenda);
		}
	}

	
	if(!isset($_GET["id_agenda"]) || !is_numeric($_GET["id_agenda"])){
		generalUtils::redirigir("main_app.php?section=diary&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/diary/manage_diary.html");
		
	//Obtenemos bloque multidioma	
	$resultadoAgenda=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_obtener_concreta(".$_GET["id_agenda"].")");
	$datoAgenda=$db->getData($resultadoAgenda);
	
	if($datoAgenda["id_usuario_web"]){
		$subPlantilla->assign("AGENDA_USUARIO","<a href='main_app.php?section=member&action=edit&id_miembro=".$datoAgenda["id_usuario_web"]."&origen=1'>".$datoAgenda["correo_electronico"]."</a>");
		$subPlantilla->parse("contenido_principal.bloque_usuario");
	}

	$tituloAgenda=$datoAgenda["titulo"];
	$idTematica=$datoAgenda["id_tematica"];
	//La primera vez, miramos si la noticia esta activo o no
	if($datoAgenda["activo"]==1){
		$subPlantilla->assign("ESTADO_CLASE","checked");
	}else{
		$subPlantilla->assign("ESTADO_CLASE","unChecked");
	}
	$subPlantilla->assign("AGENDA_ESTADO",$datoAgenda["activo"]);
	$subPlantilla->assign("AGENDA_FECHA",generalUtils::conversionFechaFormato($datoAgenda["fecha"]));
	$subPlantilla->assign("AGENDA_TITULO",$datoAgenda["titulo"]);
	$subPlantilla->assign("AGENDA_DESCRIPCION_PREVIA",$datoAgenda["descripcion_previa"]);		
	$subPlantilla->assign("AGENDA_DESCRIPCION",$datoAgenda["descripcion_completa"]);
		

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
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_DIARY_VIEW_DIARY_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_DIARY_VIEW_DIARY_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_DIARY_EDIT_DIARY_LINK."&id_agenda=".$_GET["id_agenda"];
	$vectorMigas[2]["texto"]=$tituloAgenda;

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_AGENDA",$_GET["id_agenda"]);
	$subPlantilla->assign("ACTION","edit");
	
	//Combo tematica
	$subPlantilla->assign("COMBO_TEMATICA",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_tematica_obtener_combo(-1,".$_SESSION["user"]["language_id"].")","cmbTematica","cmbTematica",$idTematica,"nombre","id_tematica",STATIC_GLOBAL_COMBO_DEFAULT,0,""));
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & clsoe
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