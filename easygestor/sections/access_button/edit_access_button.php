<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un area
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idBotonAcceso=$_POST["hdnIdBotonAcceso"];
		
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
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_editar(".$idBotonAcceso.",".$activo.",".$remoto.")");
				
		//Guardamos la informacion multidioma de area
		require "language_access_button.php";
		
		//Volvemos a listar enlace directo
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=access_button&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=access_button&action=edit&id_boton_acceso=".$idBotonAcceso);
		}
	}

	
	if(!isset($_GET["id_boton_acceso"]) || !is_numeric($_GET["id_boton_acceso"])){
		generalUtils::redirigir("main_app.php?section=acess_button&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/access_button/manage_access_button.html");
		

	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		
		$resultadoBotonAcceso=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_obtener_concreto(".$_GET["id_boton_acceso"].",".$datoIdioma["id_idioma"].")");
		$datoBotonAcceso=$db->getData($resultadoBotonAcceso);
		
		if($i==0){
			//Sacamos la informacion del menu en cada idioma
			$descripcion=$datoBotonAcceso["url"];	
			if($datoBotonAcceso["activo"]==1){
				$subPlantilla->assign("ESTADO_CLASE","checked");
			}else{
				$subPlantilla->assign("ESTADO_CLASE","unChecked");
			}
			if($datoBotonAcceso["es_remoto"]==1){
				$subPlantilla->assign("REMOTO_CLASE","checked");
			}else{
				$subPlantilla->assign("REMOTO_CLASE","unChecked");
			}
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}
	
		if($datoBotonAcceso["imagen"]!=""){
			$subPlantilla->assign("BOTON_ACCESO_IMAGEN",$datoBotonAcceso["imagen"]);
			$subPlantilla->parse("contenido_principal.item_contenido_idioma.propiedades_imagen");	
		}
		
		$subPlantilla->assign("BOTON_ACCESO_REMOTO",$datoBotonAcceso["es_remoto"]);
		$subPlantilla->assign("BOTON_ACCESO_ESTADO",$datoBotonAcceso["activo"]);
		$subPlantilla->assign("BOTON_ACCESO_URL",$datoBotonAcceso["url"]);
			
		
		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
				
		$i++;
	}
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ACCESS_BUTTON_EDIT_ACCESS_BUTTON_LINK."&id_boton_acceso=".$_GET["id_boton_acceso"];
	$vectorMigas[2]["texto"]=$descripcion;
	
	
	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_BOTON_ACCESO",$_GET["id_boton_acceso"]);
	$subPlantilla->assign("ACTION","edit");
	
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
			
	//Incluimos save & clsoe
	$subPlantilla->parse("contenido_principal.item_button_close");
	
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