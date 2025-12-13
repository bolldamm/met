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
		$idCasoExito=$_POST["hdnIdCasoExito"];
		
		//Iniciar transaccion
		$db->startTransaction();
		//Insercion noticia
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_editar(".$idCasoExito.",".$activo.")");

		//Guardamos la informacion multidioma de noticia
		require "language_success.php";

		require "image_success.php";
		
		//Cerrar transaccion
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=success&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=success&action=edit&id_caso_exito=".$idCasoExito);
		}
	}

	
	if(!isset($_GET["id_caso_exito"]) || !is_numeric($_GET["id_caso_exito"])){
		generalUtils::redirigir("main_app.php?section=success&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/success/manage_success.html");
		
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion del menu en cada idioma
		$resultadoCasoExito=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_obtener_concreta(".$_GET["id_caso_exito"].",".$datoIdioma["id_idioma"].")");
		$datoCasoExito=$db->getData($resultadoCasoExito);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["titulo"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		if($i==0){
			$tituloCasoExito=$datoCasoExito["titulo"];
			//La primera vez, miramos si la noticia esta activo o no
			if($datoCasoExito["activo"]==1){
				$subPlantilla->assign("ESTADO_CLASE","checked");
			}else{
				$subPlantilla->assign("ESTADO_CLASE","unChecked");
			}
			//Mostrar imagen
			if($datoCasoExito["imagen"]!=""){
				$subPlantilla->assign("IMAGEN_CASOEXITO",$datoCasoExito["imagen"]);
				$subPlantilla->parse("contenido_principal.propiedades_logo");
			}
			$subPlantilla->assign("CASOEXITO_ESTADO",$datoCasoExito["activo"]);
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
		$subPlantilla->assign("CASOEXITO_TITULO",$datoCasoExito["titulo"]);
		$subPlantilla->assign("CASOEXITO_DESCRIPCION_PREVIA",$datoCasoExito["descripcion_corta"]);		
		$subPlantilla->assign("CASOEXITO_DESCRIPCION",$datoCasoExito["descripcion_completa"]);
		
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
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_SUCCESS_VIEW_SUCCESS_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_SUCCESS_VIEW_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_SUCCESS_EDIT_SUCCESS_LINK;
	$vectorMigas[2]["texto"]=$tituloCasoExito;

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_CASOEXITO",$_GET["id_caso_exito"]);
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