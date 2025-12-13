<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){

		//Datos generales del enlace directo
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		
		if(isset($_POST["hdnRemoto"])){
			$remoto=$_POST["hdnRemoto"];
		}else{
			$remoto=0;
		}
		
		//Insercion enlace directo
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_insertar(".$activo.",".$remoto.")");		
		$dato=$db->getData($resultado);
		$idBotonAcceso=$dato["id_boton_acceso"];
		
		//Guardamos la informacion multidioma de boton acceso
		require "language_access_button.php";
		
		//Volvemos a listar area
		generalUtils::redirigir("main_app.php?section=access_button&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/access_button/manage_access_button.html");
		
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ACCESS_BUTTON_CREATE_ACCESS_BUTTON_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_ACCESS_BUTTON_CREATE_ACCESS_BUTTON_TEXT;
	
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


		
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
				
		$i++;
	}
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox es remoto
	$subPlantilla->assign("BOTON_ACCESO_REMOTO","0");
	$subPlantilla->assign("REMOTO_CLASE","unChecked");
	
	//Atributos del checkbox
	$subPlantilla->assign("BOTON_ACCESO_ESTADO","0");
	$subPlantilla->assign("ESTADO_CLASE","unChecked");
	
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