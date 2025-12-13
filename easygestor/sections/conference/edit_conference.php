<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de un menu
	 * @Author eData
	 * 
	 */
	
	require "includes/load_remove_attending_essential_attendees.php"; 
	//Si hemos enviado el formulario...
	if(count($_POST)){
		//Datos generales del menu
		if(isset($_POST["hdnActivo"])){
			$activo=$_POST["hdnActivo"];
		}else{
			$activo=0;
		}
		$idConferencia=$_POST["hdnIdConferencia"];
		$enlace=generalUtils::escaparCadena($_POST["txtEnlace"]);
		
		//Conference attendees		
		$essentialNonmember=$_POST["txtEssentialNonMembers"];
		$regMax=$_POST["txtCutOffLimit"];
		$essentialMember = removeAttendingEssentialAttendees($db, $idConferencia);
      
		//Conference prices
		#MET members
		$priceMemberSpeaker=$_POST["txtPriceMemberSpeaker"];
		$priceMemberEarly=$_POST["txtPriceMemberEarly"];
		$priceMemberLate=$_POST["txtPriceMemberLate"];
		
		#Sister association members
		$priceSisterAssociationSpeaker=$_POST["txtPriceSisterAssociationSpeaker"];
		$priceSisterAssociationEarly=$_POST["txtPriceSisterAssociationEarly"];
		$priceSisterAssociationLate=$_POST["txtPriceSisterAssociationLate"];
		
		#Non-members
		$priceNonMemberSpeaker=$_POST["txtPriceNonMemberSpeaker"];
		$priceNonMemberEarly=$_POST["txtPriceNonMemberEarly"];
		$priceNonMemberLate=$_POST["txtPriceNonMemberLate"];
		
		//Extra workshops, dinner guests, dinner optout, etc
		#MET members
		$extraWorkshopPrice=$_POST["txtExtraWorkshopPrice"];
		$extraMinisessionPrice=$_POST["txtExtraMinisessionPrice"];
		$dinnerGuestPrice=$_POST["txtDinnerGuestPrice"];
		
		#Sister association members
		$dinnerOptoutDiscount=$_POST["txtDinnerOptoutDiscount"];
		$receptionGuestPrice=$_POST["txtReceptionGuestPrice"];
		$otherExtraPrice=$_POST["txtOtherExtraPrice"];

        #Emails
        $emailToMet=$_POST["txtToMet"];
        $emailToUserPaypalIntro=$_POST["txtToUserPaypalIntro"];
        $emailToUserTransferIntro=$_POST["txtToUserTransferIntro"];
        $emailToUserBody=$_POST["txtToUserBody"];
        $emailToUserSignoff=$_POST["txtToUserSignoff"];
        $emailToUserPaymentConfirmed=$_POST ["txtToUserPaymentConfirmed"];
      
        #Certificate
        $certificateHeader=generalUtils::escaparCadena($_POST ["txtCertificateHeader"]);
		$CertificateDuration=generalUtils::escaparCadena($_POST ['txtCertificateDuration']);
  		$CertificateVenue=generalUtils::escaparCadena($_POST ['txtCertificateVenue']);
  		$CertificateFeedback=generalUtils::escaparCadena($_POST ['txtCertificateFeedback']);
		
		#Programme		
		if (isset($_POST["txtForProgramme"])) {
            // It exists and is not null
            $ProgrammeData=generalUtils::escaparCadena($_POST ["txtForProgramme"]);
//            $filename = '/home/metmeetings/www/www/documentacion/conference_data/sessions.24.1703.txt';
            $filename = $_SERVER['DOCUMENT_ROOT'] . '/documentacion/conference_data/sessions.' . $idConferencia . '.*.txt';
            $files = glob($filename);
            $filename = $files[0];
            file_put_contents($filename, $ProgrammeData);
        } 
		    
		$db->startTransaction();
		
$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_editar(".$idConferencia.",'".$enlace."','".generalUtils::conversionFechaFormato($_POST["txtFechaInicio"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaFinal"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaEarly"])."','".$priceMemberEarly."','".$priceSisterAssociationEarly."','".$priceNonMemberEarly."','".$priceMemberLate."','".$priceSisterAssociationLate."','".$priceNonMemberLate."','".$priceMemberSpeaker."','".$priceSisterAssociationSpeaker."','".$priceNonMemberSpeaker."','".$extraWorkshopPrice."','".$extraMinisessionPrice."','".$dinnerGuestPrice."','".$dinnerOptoutDiscount."','".$receptionGuestPrice."','".$otherExtraPrice."','".$emailToMet."','".$emailToUserPaypalIntro."','".$emailToUserTransferIntro."','".$emailToUserBody."','".$emailToUserSignoff."','".$emailToUserPaymentConfirmed."',".$activo.",'".$certificateHeader."','".$CertificateDuration."','".$CertificateVenue."','".$CertificateFeedback."','".$essentialNonmember."','".$regMax."')");
      
  		require "language_conference.php";
		
		$db->endTransaction();
		
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=conference&action=view");
		}else{
			generalUtils::redirigir("main_app.php?section=conference&action=edit&id_conferencia=".$idConferencia);
		}
	}
	
	if(!isset($_GET["id_conferencia"]) || !is_numeric($_GET["id_conferencia"])){
		generalUtils::redirigir("main_app.php?section=conference&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/conference/manage_conference.html");

	//Obtenemos bloque multidioma	
	$resultadoIdioma=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_idioma_obtener()");
	$i=0;
	//Generamos contenido multidioma
	while($datoIdioma=$db->getData($resultadoIdioma)){
      
     
		// $essentialMember = removeAttendingEssentialAttendees($db, $idConferencia);
		$essentialMember = removeAttendingEssentialAttendees($db, $_GET["id_conferencia"]);
		$subPlantilla->assign("ESSENTIAL_MEMBERS",$essentialMember);      
      
		//Sacamos la informacion del menu en cada idioma
		$resultadoConferencia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_obtener_concreta(".$_GET["id_conferencia"].",".$datoIdioma["id_idioma"].")");
		$datoConferencia=$db->getData($resultadoConferencia);
		$subPlantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		$subPlantilla->assign("IDIOMA_DESCRIPCION",$datoIdioma["nombre"]);
		$plantilla->assign("IDIOMA_ID",$datoIdioma["id_idioma"]);
		if($i==0){
			$nombre=$datoConferencia["nombre"];
			
			//La primera vez, miramos si la conferencia está activa o no
			if($datoConferencia["activo"]==1){
				$subPlantilla->assign("ESTADO_CLASE","checked");
			}else{
				$subPlantilla->assign("ESTADO_CLASE","unChecked");
			}
          	$subPlantilla->assign("ESSENTIAL_NON_MEMBERS",$datoConferencia["essential_non_member"]);
			$subPlantilla->assign("REG_MAX",$datoConferencia["reg_max"]);      
			$subPlantilla->assign("TOTAL_REGISTRADOS",$datoConferencia["total_inscritos"]);
			$subPlantilla->assign("TOTAL_REGISTRADOS_PAGADOS",$datoConferencia["total_inscritos_pagados"]);
			$subPlantilla->assign("CONFERENCIA_ENLACE",$datoConferencia["enlace"]);
			$subPlantilla->assign("CONFERENCIA_FECHA_INICIO",generalUtils::conversionFechaFormato($datoConferencia["fecha_inicio"]));
			$subPlantilla->assign("CONFERENCIA_FECHA_FIN",generalUtils::conversionFechaFormato($datoConferencia["fecha_fin"]));
			$subPlantilla->assign("CONFERENCIA_FECHA_EARLY",generalUtils::conversionFechaFormato($datoConferencia["fecha_early"]));
			$subPlantilla->assign("CONFERENCIA_ESTADO",$datoConferencia["activo"]);
			
			$subPlantilla->assign("CONFERENCE_PRICE_MEMBER_SPEAKER",$datoConferencia["price_member_speaker"]);
			$subPlantilla->assign("CONFERENCE_PRICE_MEMBER_EARLY",$datoConferencia["price_member_early"]);
			$subPlantilla->assign("CONFERENCE_PRICE_MEMBER_LATE",$datoConferencia["price_member_late"]);

            $subPlantilla->assign("CONFERENCE_PRICE_SISTER_ASSOCIATION_SPEAKER",$datoConferencia["price_sister_association_speaker"]);
			$subPlantilla->assign("CONFERENCE_PRICE_SISTER_ASSOCIATION_EARLY",$datoConferencia["price_sister_association_early"]);
			$subPlantilla->assign("CONFERENCE_PRICE_SISTER_ASSOCIATION_LATE",$datoConferencia["price_sister_association_late"]);

            $subPlantilla->assign("CONFERENCE_PRICE_NON_MEMBER_SPEAKER",$datoConferencia["price_non_member_speaker"]);			
			$subPlantilla->assign("CONFERENCE_PRICE_NON_MEMBER_EARLY",$datoConferencia["price_non_member_early"]);						
			$subPlantilla->assign("CONFERENCE_PRICE_NON_MEMBER_LATE",$datoConferencia["price_non_member_late"]);

            $subPlantilla->assign("CONFERENCE_PRICE_EXTRA_WORKSHOP",$datoConferencia["price_extra_workshop"]);			
			$subPlantilla->assign("CONFERENCE_PRICE_EXTRA_MINISESSION",$datoConferencia["price_extra_minisession"]);						
			$subPlantilla->assign("CONFERENCE_PRICE_DINNER_GUEST",$datoConferencia["price_dinner_guest"]);

            $subPlantilla->assign("CONFERENCE_PRICE_DINNER_OPTOUT",$datoConferencia["price_dinner_optout_discount"]);			
			$subPlantilla->assign("CONFERENCE_PRICE_RECEPTION_GUEST",$datoConferencia["price_wine_reception_guest"]);						
			$subPlantilla->assign("CONFERENCE_PRICE_OTHER_EXTRA",$datoConferencia["price_other_extra"]);

            $subPlantilla->assign("EMAIL_TO_MET",$datoConferencia["email_to_met"]);
            $subPlantilla->assign("EMAIL_TO_USER_PAYPAL_INTRO",$datoConferencia["email_to_user_paypal_intro"]);
            $subPlantilla->assign("EMAIL_TO_USER_TRANSFER_INTRO",$datoConferencia["email_to_user_transfer_intro"]);
            $subPlantilla->assign("EMAIL_TO_USER_BODY",$datoConferencia["email_to_user_body"]);
            $subPlantilla->assign("EMAIL_TO_USER_SIGNOFF",$datoConferencia["email_to_user_signoff"]);
            $subPlantilla->assign("EMAIL_TO_USER_PAYMENT_CONFIRMED",$datoConferencia["email_to_user_payment_confirmed"]);
            $subPlantilla->assign("CERTIFICATE_HEAD",$datoConferencia["certificate_text"]);
            $subPlantilla->assign("CERTIFICATE_DURATION",$datoConferencia["certificate_duration"]);
            $subPlantilla->assign("CERTIFICATE_VENUE",$datoConferencia["certificate_venue"]);
            $subPlantilla->assign("CERTIFICATE_FEEDBACK",$datoConferencia["feedback"]);
		}

		$subPlantilla->assign("CONFERENCIA_NOMBRE",$datoConferencia["nombre"]);
		$subPlantilla->assign("CONFERENCIA_DESCRIPCION",$datoConferencia["nombre_largo"]);
      
		// Get the query string (e.g., section=conference&action=edit&id_conferencia=24)
		$query = $_SERVER['QUERY_STRING'];

		// Parse it into an associative array
		parse_str($query, $params);

		// Access the specific parameter
		$idConference = isset($params['id_conferencia']) ? $params['id_conferencia'] : null;

		$filename = $_SERVER['DOCUMENT_ROOT'] . '/documentacion/conference_data/sessions.' . $idConference . '.*.txt';
		$files = glob($filename);
      
		if (empty($files)) {
			$programmeData = "Create a programme webpage for this conference and come back here to edit the data.";
		    $programmeDatadisable = 'disabled';
		} else {
			$programmeData = file_get_contents($files[0]);
		    $programmeDatadisable = '';
		}      
       
		$subPlantilla->assign("PROGRAMME_DATA",$programmeData);
		$subPlantilla->assign("PROGRAMME_DATA_DISABLED",$programmeDatadisable);

		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}

		
/*            $plantilla->assign("TEXTAREA_ID","txtToMet");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");*/

            $plantilla->assign("TEXTAREA_ID","txtToUserPaypalIntro");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->assign("TEXTAREA_ID","txtToUserTransferIntro");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->assign("TEXTAREA_ID","txtToUserBody");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->assign("TEXTAREA_ID","txtToUserSignoff");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->assign("TEXTAREA_ID","txtToUserPaymentConfirmed");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->assign("TEXTAREA_ID","txtCertificateHeader");
            $plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
            $plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");

            $plantilla->parse("contenido_principal.carga_inicial.editor_finder");

          	
	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONFERENCE_EDIT_CONFERENCE_LINK."&id_conferencia=".$_GET["id_conferencia"];
	$vectorMigas[2]["texto"]=$datoConferencia["nombre"];

	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_CONFERENCIA",$_GET["id_conferencia"]);
	$subPlantilla->assign("ACTION","edit");
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos save & close
	$subPlantilla->parse("contenido_principal.item_button_close");
	
	$subPlantilla->parse("contenido_principal.item_button_registered");

	
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