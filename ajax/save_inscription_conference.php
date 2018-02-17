<?php
	/**
	 * 
	 * Es el script principal para realizar la inscripcion y generar el tpv
	 * 
	 */

	require "../includes/load_main_components.inc.php";
	
	$esValido=true;
	$mensajeError="";
	
	
	//Proceso donde nos indica si todo ha ido bien...
	$plantilla=new XTemplate("../html/ajax/process_form.html");
	
	$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME,$_POST["txtNombre"]));
	$_POST["txtApellidos"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES,$_POST["txtApellidos"]));
	$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL,$_POST["txtEmail"]));
	$_POST["txtTelefono"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO,$_POST["txtTelefono"]));
	$_POST["txtaComentarios"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_LEGEND_COMMENTS,$_POST["txtaComentarios"]));
	$_POST["txtaBadge"]=generalUtils::escaparCadena($_POST["txtaBadge"]);
	

	//Email usuario
	$nombreUsuario=$_POST["txtNombre"];
	$apellidosUsuario=$_POST["txtApellidos"];
	$emailUsuario=$_POST["txtEmailUser"];
	$telefonoUsuario=$_POST["txtTelefono"];
	$comentarios=$_POST["txtaComentarios"];
	$idPais="null";
	if(!isset($_POST["chkPrivacy"])){
		$esValido=false;
        $privacy=0;
	} else {
        $privacy=1;
    }
	
	//Capturamos imagen
	
	$fotoconferencia=$_POST["valorimagen"];
	
	//Establecemos el tipo de pago
	$vectorMetodoPago=Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA,INSCRIPCION_TIPO_PAGO_PAYPAL);
	$metodoPago=$_POST["rdMetodoPago"];
	
	//Quitamos los espacios extras de todos los campos
	foreach($_POST as $clave=>$valor){
		$_POST[$clave]=trim($valor);
	}
		
	
	if(!in_array($metodoPago, $vectorMetodoPago)){
		$esValido=false;
	}
	
	
	//Comprobamos que ha introducido bien el captcha
	include("../classes/secureimage/securimage.php");
  	$img = new Securimage();

  	$valid = $img->check($_POST["txtCaptcha"],false);
	if($valid){				
		//Obtenemos los bloques de fechas de los talleres
		$resultadoTallerFecha=$db->callProcedure("CALL ed_sp_web_taller_conferencia_fecha_bloque_obtener(".$_SESSION["id_idioma"].")");
		
		$vectorTalleres=Array();
		$vectorMinisessions=Array();
		
		//Fecha
		while($datoTallerFecha=$db->getData($resultadoTallerFecha)){
			if(isset($_POST["rdnFecha_".$datoTallerFecha["fecha"]])){
				array_push($vectorTalleres,$_POST["rdnFecha_".$datoTallerFecha["fecha"]]);
			}
		}

		//Obtener mini sessiones
		$resultadoTallerConferenciaMini=$db->callProcedure("CALL ed_sp_web_taller_conferencia_obtener_concreto(".$_SESSION["id_idioma"].",1)");
		if($db->getNumberRows($resultadoTallerConferenciaMini)>0){
			while($datoTallerConferenciaMini=$db->getData($resultadoTallerConferenciaMini)){
				if(isset($_POST["chkFechaMini_".$datoTallerConferenciaMini["fecha"]."_".$datoTallerConferenciaMini["id_taller_fecha"]])){
					array_push($vectorMinisessions,$datoTallerConferenciaMini["id_taller_fecha"]);
				}//end if
			}//end if
		}//end if
		
		//Miramos si alguno de los talleres
		$totalVectorTalleres=count($vectorTalleres);
		$totalVectorMinisessions=count($vectorMinisessions);
		$i=0;
		$mini=0;
		$precio=0;

		$vectorTalleresInsertar=Array();
		$vectorMinisessiones=Array();
	

		//Recorremos los talleres
		if($totalVectorTalleres>0 || $totalVectorMinisessions>1 || isset($_POST["chkNotWorkshop"])){
			$talleresNormales=0;
			$talleresMini=0;
			while($i<$totalVectorTalleres){
				$resultadoTaller=$db->callProcedure("CALL ed_sp_web_taller_fecha_conferencia_obtener_verificaciones(".$_SESSION["id_idioma"].",".$vectorTalleres[$i].")");
				$datoTaller=$db->getData($resultadoTaller);

				$vectorTalleresInsertar[$i]["id"]=$vectorTalleres[$i];
				$vectorTalleresInsertar[$i]["precio"]=$datoTaller["precio"];
				
				if($datoTaller["es_mini"]==0){
					$talleresNormales++;
				}				
		
				//if($datoTaller["es_socio"]==1 && (!isset($_SESSION["met_user"]) && $_POST["cmbAsociacionHermana"]==-1)){
				if($datoTaller["total_inscritos"]>=$datoTaller["plazas"]){
					//Pongo >= hasta que haga unas modificaciones
					$esValido=false;
					$mensajeError.="<li style='padding-top:5px'>' ".$datoTaller["nombre"]." ' ".STATIC_FORM_WORKSHOP_REGISTER_FULL_MEMBER."</li>";
				}
				$i++;
				//$precio+=$datoTaller["precio"];
			}//end while

			//While para contar las minisessions
			while($mini<$totalVectorMinisessions){
				$resultadoMiniTaller=$db->callProcedure("CALL ed_sp_web_taller_fecha_conferencia_obtener_verificaciones(".$_SESSION["id_idioma"].",".$vectorMinisessions[$mini].")");
				$datoMiniTaller=$db->getData($resultadoMiniTaller);

				$vectorMinisessiones[$mini]["id"]=$vectorMinisessions[$mini];
				$vectorMinisessiones[$mini]["precio"]=$datoMiniTaller["precio"];

				if($datoMiniTaller["es_mini"]==1){
					$talleresMini++;
				}

				$mini++;
			}//end while
			
			if($mensajeError!=""){
				$mensajeError="<ul>".$mensajeError."</ul>";
			}//end if
			
			//Miramos pais
			if(!isset($_SESSION["met_user"])){
				if($_POST["cmbPais"]<0){
					$mensajeError=STATIC_FORM_MEMBERSHIP_ERROR_COUNTRY_RESIDENCE;
					$esValido=false;
				}else{
					$idPais=$_POST["cmbPais"];
				}
			}
		}
		else{
			$mensajeError=STATIC_FORM_CONFERENCE_REGISTER_NO_SELECTED;
			$esValido=false;
		}
		
	}else{
		$esValido=false;
		$mensajeError=STATIC_CAPTCHA_ENTER_LETTERS_NUMBERS_IMAGE;
	}

	//Si todas las validaciones son correctas
	if($esValido){				
		if($_POST["cmbTitulo"]==-1){
			$_POST["cmbTitulo"]="null";
		}
		
		if($_POST["cmbAsociacionHermana"]==-1){
			$_POST["cmbAsociacionHermana"]="null";
		}
		$idUsuarioWeb="null";
		
		$esFactura=1;
		$esSpeaker=0;
		$dinnerOptout=0;
		$emailPermiso=0;
		$esCertificado=0;
      	$esAttendeeList=0;
		
		$resultadoConferencia=$db->callProcedure("CALL ed_sp_web_conferencia_actual()");
		
		$datoConferencia=$db->getData($resultadoConferencia);
		$idConferencia=$datoConferencia["id_conferencia"];
		
		
		// Assign basic prices (member, sister association and non-member)
	if (!isset($_SESSION["met_user"])) {
		if(!isset($_POST["chkSpeaker"])){
		    if ($datoConferencia["es_early"] <= 0) {
		    	if($_POST["cmbAsociacionHermana"]=="null"){
		    	        $preliminaryPrice = $datoConferencia["price_non_member_early"];
		    	        }else{
		    	        $preliminaryPrice = $datoConferencia["price_sister_association_early"];
		    	        }
		    }else{
		    	if($_POST["cmbAsociacionHermana"]=="null"){
		    	       $preliminaryPrice = $datoConferencia["price_non_member_late"];
		    	       }else{
		    	       $preliminaryPrice = $datoConferencia["price_sister_association_late"];
		    	       }
		    }
	      }else{
	      		if($_POST["cmbAsociacionHermana"]=="null"){
	      			   $preliminaryPrice = $datoConferencia["price_non_member_speaker"];
		    	       }else{
		    	       $preliminaryPrice = $datoConferencia["price_sister_association_speaker"];
		    	       }
	      }
	}else{
		if ($datoConferencia["es_early"] <= 0) {
			if(!isset($_POST["chkSpeaker"])){
				$preliminaryPrice = $datoConferencia["price_member_early"];
				}else{
				$preliminaryPrice = $datoConferencia["price_member_speaker"];
				}
			}else{
				$preliminaryPrice = $datoConferencia["price_member_late"];
			}
		}
	
		//Total payable before any extra or any discount
		$totalPayable=$preliminaryPrice;

		//Extras
		$esDinnerGuests=$_POST["cmbInvitados"];
		//$esWineReceptionGuests=$_POST["cmbWineReceptionGuests"];
		$dinnerGuestSupplement = $esDinnerGuests*$dinnerGuestPrice; 
		//$wineReceptionGuestSupplement = $esWineReceptionGuests*$wineReceptionGuestPrice;

		
		//If speaker checked, set variable for database call
		if(isset($_POST["chkSpeaker"])){
			$esSpeaker=1;
		}

		//If dinner guests, add supplement
		if($_POST["cmbInvitados"]>0){
			$totalPayable+=$dinnerGuestSupplement;
		}
		
		//If dinner optout checked, subtract discount and set variable for database call
		if(isset($_POST["chkDinner"])){
			$totalPayable-=$dinnerOptoutDiscount;
			$dinnerOptout=1;
		}
		
		//If wine reception guests, add supplement
		/*if($_POST["cmbWineReceptionGuests"]>0){
			$totalPayable+=$wineReceptionGuestSupplement;
		}*/
		
		//If email permission checked, set variable
		if(isset($_POST["chkEmailPermission"])){
			$emailPermiso=1;	
		}
				
		//If certificate checked, set variable
		if(isset($_POST["chkCertificado"])){
			$esCertificado=1;
		}

      //If attendee list permission checked, set variable
		if(isset($_POST["chkAttendeeList"])){
			$esAttendeeList=1;
		}
      
		/******* INICIO: PRECIO ADICINAL WORKSHOP, POSIBLEMENTE SE CAMBIE CADA TEMPORADA, DE MOMENTO SE ENTIENDE QUE NO *******/
		$totalTalleresInsertar=count($vectorTalleresInsertar);
		if($talleresNormales==2){
			$totalPayable+=$extraWorkshopPrice;			
		}else if($talleresNormales==1){
			if($talleresMini>0){
				$totalPayable+=($extraMinisessionPrice*$talleresMini);		
			}//end if
		}else if($talleresMini==4){
			$totalPayable+=$extraMinisessionPrice;	
		}//end else

		$totalPayable = $_POST["finalPrice"];
		//$totalPayable = "0.01";	
		
		//Datos billing
		$_POST["txtFacturacionNifCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF,$_POST["txtFacturacionNifCliente"]));
		$_POST["txtFacturacionNombreCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER,$_POST["txtFacturacionNombreCliente"]));
		$_POST["txtFacturacionNombreEmpresa"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY,$_POST["txtFacturacionNombreEmpresa"]));
		$_POST["txtFacturacionDireccion"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS,$_POST["txtFacturacionDireccion"]));
		$_POST["txtFacturacionCodigoPostal"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE,$_POST["txtFacturacionCodigoPostal"]));
		$_POST["txtFacturacionCiudad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY,$_POST["txtFacturacionCiudad"]));
		$_POST["txtFacturacionProvincia"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE,$_POST["txtFacturacionProvincia"]));
		$_POST["txtFacturacionPais"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY,$_POST["txtFacturacionPais"]));
		
		//Para guardar en base de datos en un proceso posterior
		$nifFactura=$_POST["txtFacturacionNifCliente"];
		$nombreClienteFactura=$_POST["txtFacturacionNombreCliente"];
		$nombreEmpresaFactura=$_POST["txtFacturacionNombreEmpresa"];
		$direccionFactura=$_POST["txtFacturacionDireccion"];
		$codigoPostalFactura=$_POST["txtFacturacionCodigoPostal"];
		$ciudadFactura=$_POST["txtFacturacionCiudad"];
		$provinciaFactura=$_POST["txtFacturacionProvincia"];
		$paisFactura=$_POST["txtFacturacionPais"];
					
		//Por defecto entendemos que el concepto del movimiento asociado sera el de un usuario individual normal
		$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_CONFERENCE;
				
		$db->startTransaction();
		
		if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL){
			//Si estamos pagando por paypal o tpv... el estado es pendiente hasta que se haga el pago
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
		}else{
			$pagado=0;
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
		}
		
		
		if(isset($_SESSION["met_user"])){
			$idUsuarioWeb=$_SESSION["met_user"]["id"];
		}
		
		//Insertamos inscripcion
		$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_insertar(".$idConferencia.",".$idUsuarioWeb.",".$_POST["cmbAsociacionHermana"].",".$idEstadoInscripcion.",".$idPais.",".$metodoPago.",".$_POST["cmbTitulo"].",'".$_POST["txtNombre"]."','".$_POST["txtApellidos"]."','".$_POST["txtEmail"]."','".$_POST["txtTelefono"]."','".nl2br($comentarios)."','".nl2br($_POST["txtaBadge"])."','".$_POST["txtFacturacionNifCliente"]."','".$_POST["txtFacturacionNombreCliente"]."','".$_POST["txtFacturacionNombreEmpresa"]."','".$_POST["txtFacturacionDireccion"]."','".$_POST["txtFacturacionCodigoPostal"]."','".$_POST["txtFacturacionCiudad"]."','".$_POST["txtFacturacionProvincia"]."','".$_POST["txtFacturacionPais"]."',".$esFactura.",".$esSpeaker.",".$emailPermiso.",".$dinnerOptout.",".$esCertificado.",'".$totalPayable."','".$fotoconferencia."',".$esAttendeeList.",".$privacy.")");
		$datoInscripcion=$db->getData($resultadoInscripcion);
		
		//conseguimos el id de la última inscripción
		$id_foto=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_last_id()");
	
		//Subimos la imagen a la carpeta /files/METM_attendees/
		//Cargamos clase phpthumb
		/*require "../classes/phpThumb/ThumbLib.inc.php";
		$thumb=PhpThumbFactory::create($_FILES["fileImagen"]["tmp_name"]);
		$extensionImagen=generalUtils::obtenerExtensionFichero($_FILES["fileImagen"]["name"]);
		$nombreImagen=$id_foto.".".$extensionImagen;
				
		$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL, HEIGHT_SIZE_MEMBER_INDIVIDUAL);
		$thumb->save("../files/METM_attendees/".$nombreImagen);
		
		//Redimension la imagen
		$thumb->resize(WIDTH_SIZE_MEMBER_INDIVIDUAL_THUMB, HEIGHT_SIZE_MEMBER_INDIVIDUAL_THUMB);
		$thumb->save("../files/METM_attendees/thumb/".$nombreImagen);*/
		
		//SESIONES
		for($i=0;$i<$totalVectorTalleres;$i++){
			$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_insertar(".$datoInscripcion["codigo"].",".$vectorTalleresInsertar[$i]["id"].",'".$vectorTalleresInsertar[$i]["precio"]."')");
		}
		//MINI SESIONES
		
		for($i=0;$i<$totalVectorMinisessions;$i++){
			$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_linea_insertar(".$datoInscripcion["codigo"].",".$vectorMinisessiones[$i]["id"].",'".$vectorMinisessiones[$i]["precio"]."')");
		}
		
		/************** INICIO: EXTRAS **************/
		$vectorExtras=Array();

		$vectorExtras[0]["id"]=2;
		$vectorExtras[0]["valor"]=$esDinnerGuests;		

		/*$vectorExtras[1]["id"]=7;
		$vectorExtras[1]["valor"]=$esWineReceptionGuests;*/

		//changed "$i<=1" to "$i<1" because this year (2018) there's only one item in the "extras" array
		for($i=0;$i<1;$i++){
			$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra(".$datoInscripcion["codigo"].",".$vectorExtras[$i]["id"].",".$vectorExtras[$i]["valor"].")");
		}


		/************** FIN: EXTRAS, VARIA CADA CONFERENCIA **************/

		$itemName="METM".date(y)." registration";

		//Paypal...
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$metodoPago);
		switch($metodoPago){
			case INSCRIPCION_TIPO_PAGO_PAYPAL:
				$subPlantilla=new XTemplate("../html/ajax/paypal_form.html");
				$serverActual="http://www.metmeetings.org/";
				
				//Generamos encriptacion		
				require "../classes/paypal/clase.paypal.php";
				$config = array(
		            "cert_id" => "PR3V5CJ4DN4BA",
		            "business" => "metmember@gmail.com",
		            "openssl" => "/usr/bin/openssl",
		            "my_cert" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-pubcert.pem",
		            "my_key" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-prvkey.pem",
		            "paypal_cert" => "../classes/paypal/certificados/paypal_cert.pem"
		    	);
    			
				//Informacion para paypal
				$form["charset"]="UTF-8";	
				$form["cmd"]="_xclick";

				//Variables paypal
				//$form["item_name"]="METM17 registration";
				$form["item_name"]=$itemName;
				$form["amount"]=$_POST["finalPrice"];
				//$form["amount"]="0.01";	
			
			    $paypal = new PayPal($config);
	    
			    $form["return"]=$serverActual."inscripcion_finalizada.php?modo=1&tipo=2";
			    $form["notify_url"]=$serverActual."ajax/last_step_inscription_conference_paypal.php";
			    $form["return_cancel"]=$serverActual."inscripcion_finalizada.php?tipo=0";
			    $form["rb"]="2";
			    $form["bn"]=$itemName;
			    $form["upload"]="1";
			    $form["business"]="metmember@gmail.com";
			    $form["currency_code"]="EUR";
			   	$form["custom"]=$datoInscripcion["numero_inscripcion"]."-".$datoInscripcion["codigo"]."-1";

				//Encriptacion
			   	$subPlantilla->assign("ENCRYPT",$paypal->encrypt($form));
								
				//Parseamos toda la info
				$subPlantilla->parse("contenido_principal");
				
				//Exportamos a la plantilla principal
				$plantilla->assign("FORMULARIO_ADICIONAL",$subPlantilla->text("contenido_principal"));
				break;
			case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
				$idUsuarioWebCorreo=$idUsuarioWeb;
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=1;
				$idInscripcion=$datoInscripcion["codigo"];
				
				require "../includes/load_send_mail_inscription_conference.inc.php";
				break;
		}
		$img->clearCode();
		$db->endTransaction();
		
	}else{
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$mensajeError);
	}//end else
		
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>