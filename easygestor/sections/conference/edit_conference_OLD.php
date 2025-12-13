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
		$idConferencia=$_POST["hdnIdConferencia"];
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
      
		$db->startTransaction();
		
                $resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_editar(".$idConferencia.",'".$enlace."','".generalUtils::conversionFechaFormato($_POST["txtFechaInicio"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaFinal"])."','".generalUtils::conversionFechaFormato($_POST["txtFechaEarly"])."','".$priceMemberEarly."','".$priceSisterAssociationEarly."','".$priceNonMemberEarly."','".$priceMemberLate."','".$priceSisterAssociationLate."','".$priceNonMemberLate."','".$priceMemberSpeaker."','".$priceSisterAssociationSpeaker."','".$priceNonMemberSpeaker."','".$extraWorkshopPrice."','".$extraMinisessionPrice."','".$dinnerGuestPrice."','".$dinnerOptoutDiscount."','".$receptionGuestPrice."','".$otherExtraPrice."',".$activo.")");
	
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

		}

		$subPlantilla->assign("CONFERENCIA_NOMBRE",$datoConferencia["nombre"]);
		$subPlantilla->assign("CONFERENCIA_DESCRIPCION",$datoConferencia["nombre_largo"]);

		$subPlantilla->parse("contenido_principal.item_contenido_idioma");
		$i++;
	}

		
		

	
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