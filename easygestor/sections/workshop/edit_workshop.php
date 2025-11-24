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
	
		$idTaller=$_POST["hdnIdTaller"];
		$enlace=generalUtils::escaparCadena($_POST["txtEnlace"]);
		//Edicion taller
		
		$db->startTransaction();
		
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_editar(".$idTaller.",'".$enlace."',".$miniSesion.",".$activo.",".$event2.")");


		//Guardamos fechas
		$borrar=1;
		require "date_workshop.php";
		
		
		require "language_workshop.php";
		
		$db->endTransaction();
		
		//Volvemos a listar menu
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=workshop&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=workshop&action=edit&id_taller=".$idTaller);
		}
	}

	
	if(!isset($_GET["id_taller"]) || !is_numeric($_GET["id_taller"])){
		generalUtils::redirigir("main_app.php?section=workshop&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/workshop/manage_workshop.html");
		
	
	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
		//Sacamos la informacion del menu en cada idioma
		$resultadoTaller=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_obtener_concreta(".$_GET["id_taller"].",".$datoIdioma["id_idioma"].")");
		$datoTaller=$db->getData($resultadoTaller);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		if($i==0){
			$nombre=$datoTaller["nombre"];
			
			if($datoTaller["activo"]==1){
				$subPlantilla->assign("ESTADO_CLASE","checked");
			}else{
				$subPlantilla->assign("ESTADO_CLASE","unChecked");
			}
			
			
			if($datoTaller["es_mini"]==1){
				$subPlantilla->assign("MINI_SESION_CLASE","checked");
			}else{
				$subPlantilla->assign("MINI_SESION_CLASE","unChecked");
			}
			
			$subPlantilla->assign("TALLER_ENLACE",$datoTaller["enlace"]);
			//$subPlantilla->assign("TALLER_FECHA_INICIO",generalUtils::conversionFechaFormato($datoTaller["fecha_inicio"])); //not used
			//$subPlantilla->assign("TALLER_FECHA_FIN",generalUtils::conversionFechaFormato($datoTaller["fecha_fin"])); //not used
			$subPlantilla->assign("TALLER_ESTADO",$datoTaller["activo"]);
			$subPlantilla->assign("TALLER_MINI_SESION",$datoTaller["es_mini"]);
			$subPlantilla->assign("STYLE_DISPLAY","display:");
		}else{
			$subPlantilla->assign("STYLE_DISPLAY","display:none;");
		}

		$subPlantilla->assign("TALLER_NOMBRE",$datoTaller["nombre"]);
		$subPlantilla->assign("TALLER_DESCRIPCION",$datoTaller["nombre_largo"]);
      $subPlantilla->assign("TALLER_SEASON",$datoTaller["season"]);
      $subPlantilla->assign("TALLER_DURATION",$datoTaller["duration"]);
      $subPlantilla->assign("TALLER_FACILITATOR",$datoTaller["facilitator"]);
      $subPlantilla->assign("TALLER_N_FACILITATOR",$datoTaller["n_facilitator"]);
      $subPlantilla->assign("TALLER_FEEDBACK",$datoTaller["feedback"]);
      if ($datoTaller["event2"]){
        $subPlantilla->assign("TALLER_FORM_2","checked");
      } else {
        $subPlantilla->assign("TALLER_FORM_2","");
      }

		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}


	

	//Taller fechas
	$contadorFecha=1;
	$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_fecha_listar(".$_GET["id_taller"].")");
	while($dato=$db->getData($resultado)){
		$subPlantilla->assign("FECHA_VALOR",generalUtils::conversionFechaFormato($dato["fecha"]));
		$subPlantilla->assign("FECHA_PLAZA",$dato["plazas"]);
		$subPlantilla->assign("FECHA_PRECIO",$dato["precio"]);
		$subPlantilla->assign("FECHA_PRECIO_SISTER",$dato["precio_asociacion"]);
		$subPlantilla->assign("FECHA_PRECIO_NO_SOCIO",$dato["precio_no_socio"]);
		$subPlantilla->assign("FECHA_ID",$dato["id_taller_fecha"]);
		

		if($dato["es_socio"]==1){
			$subPlantilla->assign("CHECKED_FECHA_MIEMBRO","checked");
		}else{
			$subPlantilla->assign("CHECKED_FECHA_MIEMBRO","");
		}
		
		if($dato["es_conferencia"]==1){
			$subPlantilla->assign("CHECKED_FECHA_CONFERENCIA","checked");
		}else{
			$subPlantilla->assign("CHECKED_FECHA_CONFERENCIA","");
		}
		
		$subPlantilla->assign("FECHA_CONTADOR",$contadorFecha);
		
		

		
		if($dato["inscritos"]==0){
			$subPlantilla->parse("contenido_principal.item_fecha.eliminar_fecha");
			$subPlantilla->assign("READONLY_TEXT","");
			$subPlantilla->assign("READONLY_CHECKED","");
			$plantilla->assign("INPUT_ID","txtFecha_".$contadorFecha);
			$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
		}else{
			$subPlantilla->assign("READONLY_TEXT","readonly");
			$subPlantilla->assign("READONLY_CHECKED","onclick='this.checked=!this.checked'");
		}
		
		$subPlantilla->parse("contenido_principal.item_fecha");
		$contadorFecha++;
	}
	
	$plantilla->assign("CONTADOR_FECHA",$contadorFecha);
	$plantilla->parse("contenido_principal.variables_incrementales.fecha_contador");
	
	
	$plantilla->parse("contenido_principal.variables_incrementales");
	
	
	
		

	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_WORKSHOP_EDIT_WORKSHOP_LINK."&id_taller=".$_GET["id_taller"];
	$vectorMigas[2]["texto"]=$datoTaller["nombre"];

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_TALLER",$_GET["id_taller"]);
	$subPlantilla->assign("ACTION","edit");
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & clsoe
	$subPlantilla->parse("contenido_principal.item_button_close");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaInicio");
	$plantilla->parse("contenido_principal.carga_inicial.autoload_datepicker");
	
	//Date pickers
	$plantilla->assign("INPUT_ID","txtFechaFinal");
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