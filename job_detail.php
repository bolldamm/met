<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";
	
	if(count($_POST)){
		$idMenu=$_POST["hdnIdMenu"];
		$idJob=$_POST["hdnIdJob"];
		
		
		//Obtenemos detalles del trabajo
		$resultOferta = $db->callProcedure("CALL ed_sp_web_oferta_trabajo_obtener(".$idJob.")");
		$datoOferta = $db->getData($resultOferta);

		//Enviamos mail
		require "includes/load_mailer.inc.php";
		
		$mailPlantilla=new XTemplate("html/mail/mail_index.html");
		
		//Contenido mail
		$mailSubPlantilla=new XTemplate("html/mail/mail_submit_job_interest.html");
		
		//Asignamos informacion
		$mailSubPlantilla->assign("OFERTA_TRABAJO_INTERES_NOMBRE",$_POST["txtNombre"]);
		$mailSubPlantilla->assign("OFERTA_TRABAJO_INTERES_APELLIDOS",$_POST["txtApellidos"]);
		$mailSubPlantilla->assign("OFERTA_TRABAJO_INTERES_CORREO_ELECTRONICO",$_POST["txtEmail"]);
		$mailSubPlantilla->assign("OFERTA_TRABAJO_INTERES_CONTENIDO",nl2br($_POST["txtComentarios"]));
		$mailSubPlantilla->assign("ID_MENU",$idMenu);
		$mailSubPlantilla->assign("ID_JOB",$idJob);

		$mailSubPlantilla->parse("contenido_principal");
		
		//Exportamos subPlantilla a plantilla
		$mailPlantilla->assign("CONTENIDO",$mailSubPlantilla->text("contenido_principal"));
		
		$mailPlantilla->parse("contenido_principal");
		
		//Establecemos cuerpo del mensaje
		$mail->Body=$mailSubPlantilla->text("contenido_principal");
		

		
		/**
		 * 
		 * Incluimos la configuracion del componente phpmailer
		 * 
		 */
		$mail->AddAddress($datoOferta["correo_electronico"]);
		$mail->AddBCC(STATIC_MAIL_TO);
		$mail->FromName = STATIC_MAIL_FROM;
		$mail->Subject = STATIC_SUBMIT_JOB_INTEREST_MAIL_SUBJECT.": ".$datoOferta["titulo"];
		
		//Enviamos correo
		if($mail->Send()){	
			/****** Guardamos el log del correo electronico ******/
			$idUsuarioWebCorreo=$_SESSION["met_user"]["id"];
			
			//Tipo correo electronico
			$idTipoCorreoElectronico=EMAIL_TYPE_JOB_FORM_REQUEST;
			
			
			//Destinatario
			$vectorDestinatario=Array();
			array_push($vectorDestinatario,$datoOferta["correo_electronico"]);
			array_push($vectorDestinatario,STATIC_MAIL_TO);
			
			//Asunto
			$asunto=STATIC_SUBMIT_JOB_INTEREST_MAIL_SUBJECT.": ".$datoOferta["titulo"];
			$cuerpo=$mail->Body;
			
			$db->startTransaction();
			
			require "includes/load_log_email.inc.php";
			
			$db->endTransaction();
			
			
			
			generalUtils::redirigir(CURRENT_DOMAIN."/job_detail.php?menu=".$idMenu."&job=".$idJob."&c=1");
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN."/job_detail.php?menu=".$idMenu."&job=".$idJob."&c=2");
		}
	}

	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/job_detail.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	// $plantilla->assign("SECTION_FILE_CSS", "job_detail.css");
    $plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	
	if(isset($_GET["elemento"])){
		$_GET["job"]=$_GET["elemento"];
	}
	
	require "includes/load_structure.inc.php";
	
	
	//Obtenemos la oferta de trabajo activo seleccionada
	if(isset($_GET["job"]) && is_numeric($_GET["job"])) {
		$resultOferta = $db->callProcedure("CALL ed_sp_web_oferta_trabajo_obtener(".$_GET["job"].")");
		if($db->getNumberRows($resultOferta) > 0) {
			$dataOferta = $db->getData($resultOferta);
			
			if(isset($_GET["c"]) && is_numeric($_GET["c"])) {
				if($_GET["c"] == 1) {
					$subPlantilla->assign("MENSAJE_ACCION_PERFIL", STATIC_SUBMIT_JOB_INTEREST_SEND_OK);
					$subPlantilla->assign("MENSAJE_ACCION_CLASS", "msgOK");
					$subPlantilla->assign("MENSAJE_ACCION_DISPLAY", "");
				}else{
					$subPlantilla->assign("MENSAJE_ACCION_PERFIL", "");
					$subPlantilla->assign("MENSAJE_ACCION_CLASS", "msgKO");
					$subPlantilla->assign("MENSAJE_ACCION_DISPLAY", "display:none;");
				}
				//Hacemos que la pagina baje hacia la parte que le pertoca(donde se esta mostrando el mensaje)
				$plantilla->assign("ELEMENTO_ANCLAR","anclaExito");
				$plantilla->parse("contenido_principal.bloque_ready.bloque_anclar_elemento");
			}
			
			
			
			$subPlantilla->assign("ID_MENU",$idMenu);
			$subPlantilla->assign("ID_JOB",$_GET["job"]);	
			$subPlantilla->assign("ITEM_OFERTA_TITULO", $dataOferta["titulo"]);
			$subPlantilla->assign("ITEM_OFERTA_FECHA", generalUtils::conversionFechaFormato($dataOferta["fecha"], "-", "/"));
			$subPlantilla->assign("ITEM_OFERTA_DESCRIPCION", $dataOferta["descripcion_completa"]);
			
			if($dataOferta["correo_electronico"] != "") {
              //  $subPlantilla->assign("ITEM_OFERTA_EMAIL", $dataOferta["correo_electronico"]);
                $subPlantilla->assign("ITEM_OFERTA_EMAIL", "<a href='mailto:" .$dataOferta["correo_electronico"]. "?subject=" .$dataOferta["titulo"]. "'>Reply to job opportunity</a>");
				$subPlantilla->parse("contenido_principal.formulario_contacto");
				$plantilla->parse("contenido_principal.validar_offer_interesed");
			}
			
		
			
			/**** INICIO: breadcrumb ****/
			$vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
			$vectorAtributosDetalle["id_menu"] = $idMenu;
			$vectorAtributosDetalle["id_detalle"] = $dataOferta["id_oferta_trabajo"];
			$vectorAtributosDetalle["seo_url"] = $dataOferta["titulo"];
			$breadCrumbUrlDetalle = generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			$breadCrumbDescripcionDetalle = $dataOferta["titulo"];
			/**** FINAL: breadcrumb ****/
			
			
			
			
			
			
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
	}else{
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
		
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.control_superior");
	$plantilla->parse("contenido_principal.bloque_ready");
	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

    //Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>