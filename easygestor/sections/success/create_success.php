<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del caso exito
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		
		//Iniciar transaccion
		$db->startTransaction();
		//Insercion caso exito
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_insertar(".$activo.")");		
		$dato=$db->getData($resultado);
		$idCasoExito=$dato["id_caso_exito"];

		//Subimos ficheros
		$idElemento=$idCasoExito;
		$idImagen=0;
		
		//Guardamos la informacion multidioma del menu
		require "language_success.php";
		
		require "image_success.php";

		//Cerrar transaccion
		$db->endTransaction();
	
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=success&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/success/manage_success.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_SUCCESS_VIEW_SUCCESS_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_SUCCESS_VIEW_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_SUCCESS_CREATE_SUCCESS_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_SUCCESS_CREATE_TEXT;
		
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
		
		//Editor descripcion previa
		$plantilla->assign("TEXTAREA_ID","txtaDescripcionPrevia_".$datoIdioma["id_idioma"]);
		$plantilla->assign("TEXTAREA_TOOLBAR","Basic");
		$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
		
		//Editor descripcion completa
		$plantilla->assign("TEXTAREA_ID","txtaDescripcion_".$datoIdioma["id_idioma"]);
		$plantilla->assign("TEXTAREA_TOOLBAR","Basic");
		$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
		
		$plantilla->parse("contenido_principal.carga_inicial.editor_idioma.editor_descripcion_previa");
		$plantilla->parse("contenido_principal.carga_inicial.editor_idioma");
		
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
				
		$i++;
	}
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox
	$subPlantilla->assign("CASOEXITO_ESTADO","0");
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