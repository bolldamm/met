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
      $emailToUserPaymentConfirmed=$_POST["txtToUserPaymentConfirmed"];
      
        #Certificate
        $certificateHeader=generalUtils::escaparCadena($_POST ["txtCertificateHeader"]);
		$CertificateDuration=generalUtils::escaparCadena($_POST ['txtCertificateDuration']);
  		$CertificateVenue=generalUtils::escaparCadena($_POST ['txtCertificateVenue']);
  		$CertificateFeedback=generalUtils::escaparCadena($_POST ['txtCertificateFeedback']);

		$db->startTransaction();
					
		//Insercion conferencia
		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_insertar('".$enlace."','".generalUtils::conversionFechaFormato($_POST["txtFechaInicio"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaFinal"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaEarly"])."','".$priceMemberEarly."','".$priceSisterAssociationEarly."','".$priceNonMemberEarly."','".$priceMemberLate."','".$priceSisterAssociationLate."','".$priceNonMemberLate."','".$priceMemberSpeaker."','".$priceSisterAssociationSpeaker."','".$priceNonMemberSpeaker."','".$extraWorkshopPrice."','".$extraMinisessionPrice."','".$dinnerGuestPrice."','".$dinnerOptoutDiscount."','".$receptionGuestPrice."','".$otherExtraPrice."','".$emailToMet."','".$emailToUserPaypalIntro."','".$emailToUserTransferIntro."','".$emailToUserBody."','".$emailToUserSignoff."','".$emailToUserPaymentConfirmed."',".$activo.",'".$certificateHeader."','".$CertificateDuration."','".$CertificateVenue."','".$CertificateFeedback."')");
  //    		$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_insertar('".$enlace."','".generalUtils::conversionFechaFormato($_POST["txtFechaInicio"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaFinal"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaEarly"])."','".$priceMemberEarly."','".$priceSisterAssociationEarly."','".$priceNonMemberEarly."','".$priceMemberLate."','".$priceSisterAssociationLate."','".$priceNonMemberLate."','".$priceMemberSpeaker."','".$priceSisterAssociationSpeaker."','".$priceNonMemberSpeaker."','".$extraWorkshopPrice."','".$extraMinisessionPrice."','".$dinnerGuestPrice."','".$dinnerOptoutDiscount."','".$receptionGuestPrice."','".$otherExtraPrice."','".$emailToMet."','".$emailToUserPaypalIntro."','".$emailToUserTransferIntro."','".$emailToUserBody."','".$emailToUserSignoff."','".$emailToUserPaymentConfirmed."',".$activo.")");
      //if there's a MySQL error on submit, double-check quotes around $activo

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

            $plantilla->parse("contenido_principal.carga_inicial.editor_finder");

          	


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