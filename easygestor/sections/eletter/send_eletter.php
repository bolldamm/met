<?php
	/**
	 * 
	 * Script que muestra y realiza de un evio
	 * @Author eData
	 * 
	 */

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idTipo=$_POST["hdnIdTipo"];
		$destinatarios=Array();
		//Introducimos todos los destinatarios existentes en el para
		$destinatarios=array_merge($destinatarios,array_filter(explode(";",$_POST["txtPara"])));
		$totalDestinatarios=count($destinatarios);


		//Destinatarios excel
		if($_FILES["fileExcel"]["tmp_name"]!=""){

            $inputFileName = $_FILES["fileExcel"]["tmp_name"];

            /**  Identify the type of $inputFileName  **/
            try {
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
            } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                die('Error identifying input file: '.$e->getMessage());
            }
            /**  Create a new Reader of the type that has been identified  **/
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                die('Error creating reader: '.$e->getMessage());
            }
            /**  Advise the Reader that we only want to load cell data  **/
            $reader->setReadDataOnly(true);
            /**  Load $inputFileName to a Spreadsheet Object  **/
            try {
                $spreadsheet = $reader->load($inputFileName);
            } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                die('Error loading file: '.$e->getMessage());
            }

			try {
                $highestDataRow = $spreadsheet->getActiveSheet()->getHighestRow();
            } catch(\PhpOffice\PhpSpreadsheet\Exception $e) {
                die('Error getting highest row: '.$e->getMessage());
            }
            for($i=2;$i<=$highestDataRow;$i++){
                try {
                    $cellValue = $spreadsheet->getActiveSheet()->getCell('A' . $i)->getValue();
                } catch(\PhpOffice\PhpSpreadsheet\Exception $e) {
                    die('Error getting cell value: '.$e->getMessage());
                }
				$destinatarios[$totalDestinatarios]=$cellValue;
				$totalDestinatarios++;
			}
		}

		
		//Miembros seleccionados
		$miembros=array_filter(explode(",",$_POST["hdnUsuariosSeleccionados"]));
		$totalMiembrosSeleccionados=count($miembros);
		if($totalMiembrosSeleccionados>0){
			$totalDestinatarios=count($destinatarios);
			for($i=0;$i<$totalMiembrosSeleccionados;$i++){
				$resultadoUsuario=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_propiedades_obtener(".$miembros[$i].")");
				$datoUsuario=$db->getData($resultadoUsuario);
				$destinatarios[$totalDestinatarios]=$datoUsuario["correo_electronico"];
				$vectorMiembros[$datoUsuario["correo_electronico"]]=$miembros[$i];
				$totalDestinatarios++;
			}
		}

		$destinatarios=array_unique($destinatarios);

		require "../includes/load_mailer.inc.php";
		$plantilla=new XTemplate("html/mail/eletter.html");
		$_POST["txtaDescripcion"]=str_replace("/documentacion/","http://www.metmeetings.org/documentacion/",$_POST["txtaDescripcion"]);
		$plantilla->assign("ELETTER_CONTENIDO",generalUtils::reeamplazarAntiBarras($_POST["txtaDescripcion"]));
      	$plantilla->assign("ELETTER_TIPO", $_POST["htnTipoEletter"]);
      
      if ($_POST["txtPreview"]) {      
			$plantilla->assign("ELETTER_PREVIEW", $_POST["txtPreview"]);
        } else {
        	$plantilla->assign("ELETTER_PREVIEW", substr(strip_tags($_POST["txtaDescripcion"]), 0, 500));
        }
		$plantilla->assign("ELETTER_TITULO", $_POST["txtNombre"]);
		$plantilla->assign("ELETTER_URL", $_POST["htnUrlEletter"]);
		$plantilla->assign("ELETTER_URL_CAT", $_POST["htnUrlEletterCat"]);
		$plantilla->assign("ELETTER_URL_ENG", $_POST["htnUrlEletterEng"]);
		
		
		$hashGenerado=md5(session_id().time().$_POST["hdnIdElemento"]);
		
		$plantilla->assign("NEWSLETTER_VIEW_CORRECTLY",STATIC_VIEW_ELETTER_CORRECTLY_1." <a style='text-decoration:underline; color:#9b9b9b;' href='http://www.metmeetings.org/view_newsletter.php?id=".$hashGenerado."'>".STATIC_VIEW_ELETTER_CORRECTLY_2."</a> ".STATIC_VIEW_ELETTER_CORRECTLY_3);		
		$plantilla->parse("contenido_principal");
		
		
		$mail->From = STATIC_MAIL_FROM;
	  	$mail->FromName = "MET";
	  	$mail->Subject = $_POST["txtNombre"];
	  	$mail->Body = $plantilla->text("contenido_principal");
	  		
	  	$esCorrecto=false;
	  		  	
	  	
	 	//Guardamos registro en base de datos
		$resultadoEletter=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_eletter_insertar(".$idTipo.",".$_POST["hdnIdElemento"].",'".generalUtils::escaparCadena($_POST["txtNombre"])."','".generalUtils::escaparCadena($_POST["txtaDescripcion"])."','".$hashGenerado."')");
	  	$datoEletter=$db->getData($resultadoEletter);
	  	
	  	$totalDestinatarios=count($destinatarios);
	  	foreach($destinatarios as $valor){
			// Mail a donde enviar
	  		$mail->AddAddress($valor);
	  		if(isset($vectorMiembros[$valor])){
	  			$idMiembro=$vectorMiembros[$valor];
	  		}else{
	  			$idMiembro="null";
	  		}
	  		
	  		$hashGenerado=md5(session_id().time().$idMiembro.$valor);
	  		$imagenTrack="<img src='verificacion_lectura.php?id=".$hashGenerado."' width='1px' height='1px' alt=''>";
	  							
	  		$mail->Body=str_replace("@image@",$imagenTrack,$mail->Body);
	  		if($mail->Send()){
	  			
	  			$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_eletter_envio_insertar(".$datoEletter["id_eletter"].",$idMiembro,'".$valor."','".$hashGenerado."')");
	  			
	  		}
	  		$mail->Body=str_replace($imagenTrack,"@image@",$mail->Body);
	  		//Limpiamos address
	  		$mail->ClearAllRecipients();
	  	}
		generalUtils::redirigir("main_app.php?section=".$_POST["hdnSection"]."&action=view");
	}

	
	if(!isset($_GET["id_elemento"])){
		generalUtils::redirigir("index.php");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/eletter/send_eletter.html");
	
	
	//Mostramos informacion de lo que vamos a enviar
	switch($_GET["id_tipo"]){
		case 1:
			//Novedad
			$resultadoNovedad=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_obtener_concreta(".$_GET["id_elemento"].")");
			$datoNovedad=$db->getData($resultadoNovedad);
			$asuntoDefecto=$datoNovedad["titulo"];
			$descripcionDefecto=$datoNovedad["descripcion"];
            $teaserDefecto=$datoNovedad["teaser"];
			$migaPanOrigenEnlace=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK;
			$migaPanOrigenTexto=STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_TEXT;
			$seccionPrevia=SECTION_ELETTER;
			//$tipoDefecto=SECTION_NOVELTY_TYPE;
            $tipoDefecto=STATIC_NEW_TYPE;
			$imgCabecera="../../images/mail/bannerTopNovedad.jpg";
			break;
		case 2:
			//Noticias
			$resultadoNoticia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_obtener_concreta(".$_GET["id_elemento"].",".$_SESSION["user"]["language_id"].")");
			$datoNoticia=$db->getData($resultadoNoticia);
			$asuntoDefecto=$datoNoticia["titulo"];
			$descripcionDefecto=$datoNoticia["descripcion_completa"];
			$tipoDefecto=STATIC_NEW_TYPE;
			$migaPanOrigenEnlace=STATIC_BREADCUMB_NEW_VIEW_NEW_LINK;
			$migaPanOrigenTexto=STATIC_BREADCUMB_NEW_VIEW_NEW_TEXT;
			$seccionPrevia=SECTION_NEW;
			
			$vectorAtributosDetalle["idioma"]="es";
			$vectorAtributosDetalle["id_menu"]=279;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoNoticia["titulo"];
			$urlDefecto=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			//versión català
			$resultadoNoticia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_obtener_concreta(".$_GET["id_elemento"].",'2')");
			$datoNoticia=$db->getData($resultadoNoticia);
			$vectorAtributosDetalle["idioma"]="ca";
			$vectorAtributosDetalle["id_menu"]=279;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoNoticia["titulo"];
			$urlDefectoCat=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			//versión english
			$resultadoNoticia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_obtener_concreta(".$_GET["id_elemento"].",'3')");
			$datoNoticia=$db->getData($resultadoNoticia);
			$vectorAtributosDetalle["idioma"]="en";
			$vectorAtributosDetalle["id_menu"]=279;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoNoticia["titulo"];
			$urlDefectoEng=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			$imgCabecera="../../images/mail/bannerTopNoticia.jpg";
			break;
		case 3:
			//Notas de prensa
			$resultadoPrensa=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_nota_prensa_obtener_concreta(".$_GET["id_elemento"].",".$_SESSION["user"]["language_id"].")");
			$datoPrensa=$db->getData($resultadoPrensa);
			$asuntoDefecto=$datoPrensa["titulo"];
			$descripcionDefecto=$datoPrensa["descripcion_completa"];
			$migaPanOrigenEnlace=STATIC_BREADCUMB_PRESS_VIEW_PRESS_LINK;
			$migaPanOrigenTexto=STATIC_BREADCUMB_PRESS_VIEW_PRESS_TEXT;
			$seccionPrevia=SECTION_PRESS;
			$tipoDefecto=SECTION_PRESS_TYPE;
			
			$vectorAtributosDetalle["idioma"]="es";
			$vectorAtributosDetalle["id_menu"]=834;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoPrensa["titulo"];
			$urlDefecto=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			//versión català
			$resultadoPrensa=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_nota_prensa_obtener_concreta(".$_GET["id_elemento"].",'2')");
			$datoPrensa=$db->getData($resultadoPrensa);
			$vectorAtributosDetalle["idioma"]="ca";
			$vectorAtributosDetalle["id_menu"]=834;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoPrensa["titulo"];
			$urlDefectoCat=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			//versión english
			$resultadoPrensa=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_nota_prensa_obtener_concreta(".$_GET["id_elemento"].",'3')");
			$datoPrensa=$db->getData($resultadoPrensa);
			$vectorAtributosDetalle["idioma"]="en";
			$vectorAtributosDetalle["id_menu"]=279;
			$vectorAtributosDetalle["id_detalle"]=$_GET["id_elemento"];
			$vectorAtributosDetalle["seo_url"]=$datoPrensa["titulo"];
			$urlDefectoEng=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
			
			$imgCabecera="../../images/mail/bannerTopPrensa.jpg";
			break;
		case 4:
			//Agenda
			$vectorElemento=explode(",",$_GET["id_elemento"]);
			$totalElemento=count($vectorElemento);
			$descripcionDefecto="";
			for($i=0;$i<$totalElemento;$i++){
				$resultadoAgenda=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_obtener_concreta(".$vectorElemento[$i].",".$_SESSION["user"]["language_id"].")");
				$datoAgenda=$db->getData($resultadoAgenda);
				if($i==0){
					$asuntoDefecto=$datoAgenda["titulo"];
					
					$vectorAtributosDetalle["idioma"]="es";
					$vectorAtributosDetalle["id_menu"]=270;
					$vectorAtributosDetalle["id_detalle"]=$vectorElemento[$i];
					$vectorAtributosDetalle["seo_url"]=$datoAgenda["titulo"];
					$urlDefecto=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
					
					//versión català
					$resultadoPrensa=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_obtener_concreta(".$vectorElemento[$i].",'2')");
					$datoPrensa=$db->getData($resultadoPrensa);
					$vectorAtributosDetalle["idioma"]="ca";
					$vectorAtributosDetalle["id_menu"]=270;
					$vectorAtributosDetalle["id_detalle"]=$vectorElemento[$i];
					$vectorAtributosDetalle["seo_url"]=$datoAgenda["titulo"];
					$urlDefectoCat=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
					
					//versión english
					$resultadoPrensa=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_obtener_concreta(".$vectorElemento[$i].",'3')");
					$datoPrensa=$db->getData($resultadoPrensa);
					$vectorAtributosDetalle["idioma"]="en";
					$vectorAtributosDetalle["id_menu"]=270;
					$vectorAtributosDetalle["id_detalle"]=$vectorElemento[$i];
					$vectorAtributosDetalle["seo_url"]=$datoAgenda["titulo"];
					$urlDefectoEng=generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
				}
				$descripcionDefecto.="<strong>".trim($datoAgenda["titulo"])."</strong><br>".trim($datoAgenda["descripcion_completa"])."<br><br><br>";
			}
			$migaPanOrigenEnlace=STATIC_BREADCUMB_DIARY_VIEW_DIARY_LINK;
			$migaPanOrigenTexto=STATIC_BREADCUMB_DIARY_VIEW_DIARY_TEXT;
			$seccionPrevia=SECTION_DIARY;
			$tipoDefecto=SECTION_DIARY_TYPE;
			
			$imgCabecera="../../images/mail/bannerTopAgenda.jpg";
			break;
	}
	
	//Datos defecto
	$subPlantilla->assign("ELETTER_ASUNTO",htmlspecialchars($asuntoDefecto));
	//$descripcionDefecto = str_replace('<tr class="openAltImage"><td', '<tr class="openAltImage" style="padding:5px;font-size:10px; background-color: #f8f8f8; border-bottom:1px solid; border-right:1px solid; border-left:1px solid; border-color: #d9d9d9;"><td  style="font-family:verdana, arial; font-size:11px;"', $descripcionDefecto);
    $subPlantilla->assign("ELETTER_PREVIEW",$teaserDefecto);
	$subPlantilla->assign("ELETTER_DESCRIPCION",$descripcionDefecto);
	$subPlantilla->assign("ELETTER_TIPO", $tipoDefecto);
	/*$subPlantilla->assign("ELETTER_URL", $urlDefecto);
	$subPlantilla->assign("ELETTER_URL_CAT", $urlDefectoCat);
	$subPlantilla->assign("ELETTER_URL_ENG", $urlDefectoEng);
	$subPlantilla->assign("ELETTER_IMG_CABECERA", $imgCabecera);*/

	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=$migaPanOrigenEnlace;
	$vectorMigas[1]["texto"]=$migaPanOrigenTexto;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_ELETTER_SEND_ELETTER_LINK."&id_elemento=".$_GET["id_elemento"]."&id_tipo=".$_GET["id_tipo"];
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_ELETTER_SEND_ELETTER_TEXT;
	
	//Boton volver
	$subPlantilla->assign("SECCION_VOLVER",$migaPanOrigenEnlace);
	$subPlantilla->assign("SECCION_PREVIA",$seccionPrevia);

	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_ELEMENTO",$_GET["id_elemento"]);
	$subPlantilla->assign("ID_TIPO",$_GET["id_tipo"]);
	$subPlantilla->assign("ACTION","send");
	
	
	
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