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
		$idOfertaTrabajo=$_POST["hdnIdOfertaTrabajo"];
		
		$titulo=generalUtils::escaparCadena($_POST["txtTitulo"]);
		$descripcionPrevia=generalUtils::escaparCadena($_POST["txtaDescripcionPrevia"]);
		$descripcion=generalUtils::escaparCadena($_POST["txtaDescripcion"]);

		//Insercion job
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_oferta_trabajo_editar(".$idOfertaTrabajo.",'".generalUtils::escaparCadena($_POST["txtEmail"])."','".$titulo."','".$descripcionPrevia."','".$descripcion."','".generalUtils::conversionFechaFormato($_POST["txtFecha"])."',".$activo.")");

		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=job&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=job&action=edit&id_oferta_trabajo=".$idOfertaTrabajo);
		}
	}

	
	if(!isset($_GET["id_oferta_trabajo"]) || !is_numeric($_GET["id_oferta_trabajo"])){
		generalUtils::redirigir("main_app.php?section=job&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/job/manage_job.html");
		


	//Sacamos la informacion del menu en cada idioma
	$resultadoJob=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_oferta_trabajo_obtener_concreta(".$_GET["id_oferta_trabajo"].")");	
	$datoJob=$db->getData($resultadoJob);
	
	if($datoJob["id_usuario_web"]){
		$subPlantilla->assign("JOB_USUARIO","<a href='main_app.php?section=member&action=edit&id_miembro=".$datoJob["id_usuario_web"]."&origen=1'>".$datoJob["correo_usuario"]."</a>");
		$subPlantilla->parse("contenido_principal.bloque_usuario");
	}
	
	$tituloJob=$datoJob["titulo"];
	$correoElectronico=$datoJob["correo_electronico"];
	//La primera vez, miramos si la noticia esta activo o no
	if($datoJob["activo"]==1){
		$subPlantilla->assign("ESTADO_CLASE","checked");
	}else{
		$subPlantilla->assign("ESTADO_CLASE","unChecked");
	}
	$subPlantilla->assign("JOB_ESTADO",$datoJob["activo"]);
	$subPlantilla->assign("JOB_FECHA",generalUtils::conversionFechaFormato($datoJob["fecha"]));
	$subPlantilla->assign("JOB_EMAIL",$correoElectronico);


	$subPlantilla->assign("JOB_TITULO",htmlspecialchars($datoJob["titulo"]));
	$subPlantilla->assign("JOB_DESCRIPCION_PREVIA",$datoJob["descripcion_previa"]);		
	$subPlantilla->assign("JOB_DESCRIPCION",$datoJob["descripcion_completa"]);
	

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
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_JOB_VIEW_JOB_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_JOB_VIEW_JOB_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_JOB_EDIT_JOB_LINK."&id_oferta_trabajo=".$_GET["id_oferta_trabajo"];
	$vectorMigas[2]["texto"]=$tituloJob;

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_JOB",$_GET["id_oferta_trabajo"]);
	$subPlantilla->assign("ACTION","edit");
	
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