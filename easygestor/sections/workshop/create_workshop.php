<?php
	/**
	 * 
	 * Script que muestra y realiza la creacion de un menu
	 * @Author eData
	 * 
	 */


	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales de la noticia
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		
		if(isset($_POST["hdnMiniSesion"])){
			$miniSesion=$_POST["hdnMiniSesion"];
		}else{
			$miniSesion=0;
		}
		
      	if(isset($_POST["isForm2"])){
			$event2=1;
		}else{
			$event2=0;
		}
      
		$enlace=generalUtils::escaparCadena($_POST["txtEnlace"]);

		
		$db->startTransaction();
			
		//Insercion taller
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_insertar('".$enlace."',".$miniSesion.",".$activo.",".$event2.")");
      	//	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_insertar('".$enlace."',".$miniSesion.",".$activo.")");
		$dato=$db->getData($resultado);
		$idTaller=$dato["id_taller"];
		
		require "language_workshop.php";
		
		//Guardamos fechas
		$borrar=0;
		require "date_workshop.php";

		
		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=workshop&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/workshop/manage_workshop.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_WORKSHOP_CREATE_WORKSHOP_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_WORKSHOP_CREATE_WORKSHOP_TEXT;
		
	require "includes/load_breadcumb.inc.php";
	
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);

		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
				
		$i++;
	}	
	
	$subPlantilla->assign("ACTION","create");
	
	//Atributos del checkbox
	$subPlantilla->assign("TALLER_ESTADO","0");
	$subPlantilla->assign("ESTADO_CLASE","unChecked");
	
	//Atributos del checkbox
	$subPlantilla->assign("TALLER_MINI_SESION","0");
	$subPlantilla->assign("MINI_SESION_CLASE","unChecked");

	
	//Contador fecha
	$plantilla->assign("CONTADOR_FECHA",1);
	$plantilla->parse("contenido_principal.variables_incrementales.fecha_contador");
	$plantilla->parse("contenido_principal.variables_incrementales");
		
	
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