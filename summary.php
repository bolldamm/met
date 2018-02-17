<?php
	/**
	 * 
	 * Pagina openbox de contenido libre
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/summary.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	require "includes/load_structure.inc.php";
	
	$subPlantilla->assign("CURRENT_YEAR",date("Y"));

	
	if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
		//Obtenemos estadisticas(primero usuarios pagos estado)
		$resultadoPagoEstado=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_estado_pago()");
		$totalPagoEstado=$db->getNumberRows($resultadoPagoEstado);
		
		

		$vectorModos[1]["status"]=STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_YES;
		$vectorModos[1]["view"]=STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_PUBLIC;
		$vectorModos[2]["status"]=STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_YES;
		$vectorModos[2]["view"]=STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_MEMBERS_ONLY;
		$vectorModos[3]["status"]=STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_NO;
		$vectorModos[3]["view"]=STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_PUBLIC;
		$vectorModos[4]["status"]=STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_NO;
		$vectorModos[4]["view"]=STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_MEMBERS_ONLY;
		
		//Usuarios..
		$miembroContador=0;
		while($datoPagoEstado=$db->getData($resultadoPagoEstado)){
			$vectorModosValores[$datoPagoEstado["modo"]]=$datoPagoEstado["total"];
		}
		
		
		foreach($vectorModos as $clave=>$valor){
			if(isset($vectorModosValores[$clave])){
				$miembroContador+=$vectorModosValores[$clave];
				$valor=$vectorModosValores[$clave];
			}else{
				$miembroContador+=0;
				$valor=0;
			}

	
			$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_PAID_STATUS",$vectorModos[$clave]["status"]);
			$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_VIEW",$vectorModos[$clave]["view"]);
			
			$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_MEMBER",$valor);
			$subPlantilla->parse("contenido_principal.item_pago_estado");
		}

		
			/*if($datoPagoEstado["total"]==""){
				$datoPagoEstado["total"]=0;
			}
			switch($datoPagoEstado["modo"]){
				case 1:
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_PAID_STATUS",STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_YES);
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_VIEW",STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_PUBLIC);
					break;
				case 2:
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_PAID_STATUS",STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_YES);
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_VIEW",STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_MEMBERS_ONLY);
					break;
				case 3:
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_PAID_STATUS",STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_NO);
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_VIEW",STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_PUBLIC);
					break;
				case 4:
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_PAID_STATUS",STATIC_SUMMARY_BLOCK_PREFERENCES_PAID_STATUS_NO);
					$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_VIEW",STATIC_SUMMARY_BLOCK_PREFERENCES_VIEW_MEMBERS_ONLY);
					break;
			}
		
			$miembroContador+=$datoPagoEstado["total"];
			$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_MEMBER",$datoPagoEstado["total"]);
			$subPlantilla->parse("contenido_principal.item_pago_estado");
		}*/
		
		//Asignamos total contadores
		$subPlantilla->assign("SUMMARY_BLOCK_PREFERENCES_TOTAL_MEMBERS",$miembroContador);
		
		//Obtenemos estadisticas profesionales
		$resultadoActividadProfesional=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_actividad_profesional(".$_SESSION["id_idioma"].")");		
		while($datoActividadProfesional=$db->getData($resultadoActividadProfesional)){
			$subPlantilla->assign("SUMMARY_BLOCK_PROFESSION_NAME",$datoActividadProfesional["descripcion"]);
			$subPlantilla->assign("SUMMARY_BLOCK_PROFESSION_MEMBERS",$datoActividadProfesional["total"]);
			$subPlantilla->assign("SUMMARY_BLOCK_PROFESSION_MEMBERS_PORCENTAJE",$porcentaje=round(($datoActividadProfesional["total"]/$miembroContador)*100,1));
			$subPlantilla->parse("contenido_principal.item_profesion");
		}
		
		
		//Obtenemos estadisticas(primero paises)
		$resultadoPais=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_pais()");
		$totalPaises=$db->getNumberRows($resultadoPais);
		
		//Paises...
		$porcentajeContador=0;
		while($datoPais=$db->getData($resultadoPais)){
			$porcentaje=round(($datoPais["total_paises"]/$miembroContador)*100,1);
			$porcentajeContador+=$porcentaje;
			$subPlantilla->assign("ESTADISTICA_BLOQUE_PAIS_NOMBRE_PAIS",$datoPais["nombre_original"]);
			$subPlantilla->assign("ESTADISTICA_BLOQUE_PAIS_NUMERO_MIEMBROS_PAIS",$datoPais["total_paises"]);
			$subPlantilla->assign("ESTADISTICA_BLOQUE_PAIS_PORCENTAJE_MIEMBROS_PAIS",$porcentaje);
			$subPlantilla->parse("contenido_principal.item_pais");
		}
		
		//Obtenemos estadisticas(primero edades)
		$resultadoEdad=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_edad(".$_SESSION["id_idioma"].")");
		$totalEdades=$db->getNumberRows($resultadoEdad);
		
		//Edades...
		$porcentajeContador=0;
		while($datoEdad=$db->getData($resultadoEdad)){
			$porcentaje=round(($datoEdad["total_edad_usuario_web"]/$miembroContador)*100,1);
			$porcentajeContador+=$porcentaje;
			$subPlantilla->assign("SUMMARY_BLOCK_AGE_NAME",$datoEdad["nombre"]);
			$subPlantilla->assign("SUMMARY_BLOCK_AGE_MEMBERS",$datoEdad["total_edad_usuario_web"]);
			$subPlantilla->assign("SUMMARY_BLOCK_AGE_MEMBERS_PORCENTAJE",$porcentaje);
			$subPlantilla->parse("contenido_principal.item_edad");
		}
		
		//Obtenemos estadisticas(primero edades)
		$resultadoSexo=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_sexo()");
		$totalSexos=$db->getNumberRows($resultadoSexo);
		
		//Sexos...
		$porcentajeContador=0;
		while($datoSexo=$db->getData($resultadoSexo)){
			$porcentaje=round(($datoSexo["total_sexo"]/$miembroContador)*100,1);
			$porcentajeContador+=$porcentaje;
			if($datoSexo["es_hombre"]==1){
				$subPlantilla->assign("SUMMARY_BLOCK_SEX_NAME",STATIC_FORM_MEMBERSHIP_MALE);
			}else{
				$subPlantilla->assign("SUMMARY_BLOCK_SEX_NAME",STATIC_FORM_MEMBERSHIP_FEMALE);
			}
			
			$subPlantilla->assign("SUMMARY_BLOCK_SEX_MEMBERS",$datoSexo["total_sexo"]);
			$subPlantilla->assign("SUMMARY_BLOCK_SEX_MEMBERS_PORCENTAJE",$porcentaje);
			$subPlantilla->parse("contenido_principal.item_sexo");
		}
		
			//Obtenemos estadisticas(primero edades)
		$resultadoSituacionLaboral=$db->callProcedure("CALL ed_sp_web_usuario_web_estadistica_situacion_laboral(".$_SESSION["id_idioma"].")");
		$totalSituacionLaboral=$db->getNumberRows($resultadoSituacionLaboral);
		
		//Situaciones laborales...
		$porcentajeContador=0;
		while($datoSituacionLaboral=$db->getData($resultadoSituacionLaboral)){
			$porcentaje=round(($datoSituacionLaboral["total_situacion_laboral"]/$miembroContador)*100,1);
			$porcentajeContador+=$porcentaje;
			$subPlantilla->assign("SUMMARY_BLOCK_WORK_SITUATION_NAME",$datoSituacionLaboral["nombre"]);
			$subPlantilla->assign("SUMMARY_BLOCK_WORK_SITUATION_MEMBERS",$datoSituacionLaboral["total_situacion_laboral"]);
			$subPlantilla->assign("SUMMARY_BLOCK_WORK_SITUATION_MEMBERS_PORCENTAJE",$porcentaje);
			$subPlantilla->parse("contenido_principal.item_situacion_laboral");
		}
		
	} else { generalUtils::redirigir(CURRENT_DOMAIN); }
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	
	$plantilla->parse("contenido_principal.bloque_ready");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>