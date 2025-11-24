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
		
		$enlace=generalUtils::escaparCadena($_POST["txtEnlace"]);

		//Basicos
		#Socios
		$precioEarlyBasico=$_POST["txtPrecioEarlyBasico"];
		$precioLateBasico=$_POST["txtPrecioLateBasico"];
		$precioSpeakerBasico=$_POST["txtPrecioSpeakerBasico"];
		
		#Sister
		$precioEarlyBasicoAsociacion=$_POST["txtPrecioEarlyBasicoAsociacion"];
		$precioLateBasicoAsociacion=$_POST["txtPrecioLateBasicoAsociacion"];
		$precioSpeakerBasicoAsociacion=$_POST["txtPrecioSpeakerBasicoAsociacion"];
		
		#non member
		$precioEarlyBasicoNoSocio=$_POST["txtPrecioEarlyBasicoNoSocio"];
		$precioLateBasicoNoSocio=$_POST["txtPrecioLateBasicoNoSocio"];
		$precioSpeakerBasicoNoSocio=$_POST["txtPrecioSpeakerBasicoNoSocio"];
		
		//Completo
		#Socios
		$precioEarlyCompleto=$_POST["txtPrecioEarlyCompleto"];
		$precioLateCompleto=$_POST["txtPrecioLateCompleto"];
		$precioSpeakerCompleto=$_POST["txtPrecioSpeakerCompleto"];
		
		#Sister
		$precioEarlyCompletoAsociacion=$_POST["txtPrecioEarlyCompletoAsociacion"];
		$precioLateCompletoAsociacion=$_POST["txtPrecioLateCompletoAsociacion"];
		$precioSpeakerCompletoAsociacion=$_POST["txtPrecioSpeakerCompletoAsociacion"];
		
		#non member
		$precioEarlyCompletoNoSocio=$_POST["txtPrecioEarlyCompletoNoSocio"];
		$precioLateCompletoNoSocio=$_POST["txtPrecioLateCompletoNoSocio"];
		$precioSpeakerCompletoNoSocio=$_POST["txtPrecioSpeakerCompletoNoSocio"];

		$db->startTransaction();
		
		
			
		//Insercion conferencia
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_insertar('".$enlace."','".generalUtils::conversionFechaFormato($_POST["txtFechaInicio"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaFinal"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaEarly"])."','".$precioEarlyBasico."','".$precioEarlyBasicoAsociacion."','".$precioEarlyBasicoNoSocio."','".$precioLateBasico."','".$precioLateBasicoAsociacion."','".$precioLateBasicoNoSocio."','".$precioSpeakerBasico."','".$precioSpeakerBasicoAsociacion."','".$precioSpeakerBasicoNoSocio."','".$precioEarlyCompleto."','".$precioEarlyCompletoAsociacion."','".$precioEarlyCompletoNoSocio."','".$precioLateCompleto."','".$precioLateCompletoAsociacion."','".$precioLateCompletoNoSocio."','".$precioSpeakerCompleto."','".$precioSpeakerCompletoAsociacion."','".$precioSpeakerCompletoNoSocio."',".$activo.")");		
		$dato=$db->getData($resultado);
		$idConferencia=$dato["id_conferencia"];
		
		require "language_conference.php";

		
		$db->endTransaction();
		
		//Volvemos a listar menu
		generalUtils::redirigir("main_app.php?section=conference&action=view");
	}

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/conference/manage_conference.html");
	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONFERENCE_CREATE_CONFERENCE_LINK;
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_CONFERENCE_CREATE_CONFERENCE_TEXT;
		
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
	$subPlantilla->assign("CONFERENCIA_ESTADO","0");
	$subPlantilla->assign("ESTADO_CLASE","unChecked");

	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
		
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaInicio");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaFinal");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaEarly");
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