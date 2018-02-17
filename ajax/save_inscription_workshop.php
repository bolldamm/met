<?php
	/**
     * Stores result (payment method or error) of signup process in "process_form.html" template
	 * Gets data from signup form and validates it
     * Inserts signup data in database and submits data to Paypal
     * Passes data to load_save_inscription_workshop.inc.php for movement and emails
	 */

	require "../includes/load_main_components.inc.php";
	
	$esValido=true;
	$mensajeError="";
	
	
	//Initialise template that will contain the result, i.e., success or failure
    //If bank transfer and successful, all the "process_form.html" template contains is the hidden "result" variable (i.e. the payment method, which is 1)
	$plantilla=new XTemplate("../html/ajax/process_form.html");

	//Delete placeholders from form fields that haven't been filled and add backslashes before any characters that need to be escaped
	$_POST["txtNombre"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_FIRST_NAME,$_POST["txtNombre"]));
	$_POST["txtApellidos"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_LAST_NAMES,$_POST["txtApellidos"]));
	$_POST["txtEmail"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_MEMBERSHIP_EMAIL,$_POST["txtEmail"]));
	$_POST["txtTelefono"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_CONTACT_PHONE_NO,$_POST["txtTelefono"]));
	$_POST["txtaComentarios"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_WORKSHOP_REGISTER_LEGEND_COMMENTS,$_POST["txtaComentarios"]));

	//Store personal details etc. from the form in variables (for the database procedure)
	$nombreUsuario=$_POST["txtNombre"];
	$apellidosUsuario=$_POST["txtApellidos"];
	$emailUsuario=$_POST["txtEmailUser"];
	$telefonoUsuario=$_POST["txtTelefono"];
	$comentarios=$_POST["txtaComentarios"];
	$privacy=$_POST["chkPrivacy"];

	if(!isset($_POST["chkPrivacy"])){
		$esValido=false;
        $privacy=0;
	} else {
        $privacy=1;
    }
	
	//Store the selected payment method in a variable
	$vectorMetodoPago=Array(INSCRIPCION_TIPO_PAGO_TRANSFERENCIA,INSCRIPCION_TIPO_PAGO_PAYPAL);
	$metodoPago=$_POST["rdMetodoPago"];
	
	//Strip extra spaces from the content of all input fields
	foreach($_POST as $clave=>$valor){
		$_POST[$clave]=trim($valor);
	}

	//If payment method not in array of possible methods, set "Invalid" flag
	if(!in_array($metodoPago, $vectorMetodoPago)){
		$esValido=false;
	}
	
	//Check whether captcha text matches captcha image
	include("../classes/secureimage/securimage.php");
  	$img = new Securimage();

  	$valid = $img->check($_POST["txtCaptcha"],false);
	//If captcha is valid (i.e., text matches image and check returns "true")
  	if($valid){
		//Assign selected workshop IDs to $vectorTalleres array (stripping out commas between IDs)
		$vectorTalleres=array_filter(explode(",",$_POST["hdnIdTaller"]));
		$totalVectorTalleres=count($vectorTalleres);
		$i=0;
		$precio=0;
		$esAutenticado=false;

		$idUsuarioWeb="null";
		if(isset($_SESSION["met_user"])){
			$esAutenticado=true;
			$idUsuarioWeb=$_SESSION["met_user"]["id"];
		}
	
		//Initialise an empty array to hold the workshop data (ID, price and paid/unpaid) for insertion in the database
		$vectorTalleresInsertar=Array();
		
		//If there are IDs in the $vectorTalleres array
		if($totalVectorTalleres>0){
			//For each selected workshop (by ID)
		    while($i<$totalVectorTalleres){
				//Get the user type (member, sister, non-member), prices (x3), max. places, no. registered, and workshop title
			    $resultadoTaller=$db->callProcedure("CALL ed_sp_web_taller_fecha_obtener_verificaciones(".$_SESSION["id_idioma"].",".$vectorTalleres[$i].")");
				$datoTaller=$db->getData($resultadoTaller);
				
				//Insert workshop ID into workshops array for insertion into database
				$vectorTalleresInsertar[$i]["id"]=$vectorTalleres[$i];
				
				//Insert applicable price into workshops array, based on user type (member, sister, non-member)
				if($esAutenticado){
					$vectorTalleresInsertar[$i]["precio"]=$datoTaller["precio"];
				}else{
					if($_POST["cmbAsociacionHermana"]==-1){
						$vectorTalleresInsertar[$i]["precio"]=$datoTaller["precio_no_socio"];
					}else{
						$vectorTalleresInsertar[$i]["precio"]=$datoTaller["precio_asociacion"];
					}
				}
				
				//If price is zero or payment type is Paypal, set signup to "Paid", otherwise "Not paid"
				if($vectorTalleresInsertar[$i]["precio"]==0 || $metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL){
					$vectorTalleresInsertar[$i]["pagado"]=1;
				}else{
					$vectorTalleresInsertar[$i]["pagado"]=0;
				}

				//If workshop is "member only" and user is non-member (not logged in), set "Invalid" flag and assign error message
				if($datoTaller["es_socio"]==1 && (!isset($_SESSION["met_user"])/* && $_POST["cmbAsociacionHermana"]==-1*/)){
					$esValido=false;
					$mensajeError.=htmlspecialchars("<li style='padding-top:5px'>".STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_1." \"".$datoTaller["nombre"]."\" ".STATIC_FORM_WORKSHOP_REGISTER_NOT_MEMBER_2."</li>");
				//If number registered is equal to or greater than max. places, set "Invalid" flag and assign error message
				}else if($datoTaller["total_inscritos"]>=$datoTaller["plazas"]){
					//Pongo >= hasta que haga unas modificaciones
					$esValido=false;
					$mensajeError.="<li style='padding-top:5px'>' ".$datoTaller["nombre"]." ' ".STATIC_FORM_WORKSHOP_REGISTER_FULL_MEMBER."</li>";
				}
				//Add workshop price to cumulative total in workshops array
				$precio+=$vectorTalleresInsertar[$i]["precio"];
				$i++;
				
			}

			//Add format (unordered list) to error message
			if($mensajeError!=""){
				$mensajeError="<ul>".$mensajeError."</ul>";
			}
		}else{
			//If no workshops selected, assign specific error message
		    $mensajeError=STATIC_FORM_WORKSHOP_REGISTER_NO_SELECTED;
			$esValido=false;
		}
	}else{
		//If captcha is not valid, assign specific error message
  	    $esValido=false;
		$mensajeError=STATIC_CAPTCHA_ENTER_LETTERS_NUMBERS_IMAGE;
	}

	//If form data is valid, process the signup
	if($esValido){
        if($_POST["cmbTitulo"]==-1){
			$_POST["cmbTitulo"]="null";
		}
		
		if($_POST["cmbAsociacionHermana"]==-1){
			$_POST["cmbAsociacionHermana"]="null";
		}

		$esFactura=1;
		$esMailEnviado=0;
		$pagado=0;
		
		//Remove placeholders from billing detail inputs and add backslashes before any characters that need to be escaped
		$_POST["txtFacturacionNifCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CUSTOMER_NIF,$_POST["txtFacturacionNifCliente"]));
		$_POST["txtFacturacionNombreCliente"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_CUSTOMER,$_POST["txtFacturacionNombreCliente"]));
		$_POST["txtFacturacionNombreEmpresa"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_NAME_COMPANY,$_POST["txtFacturacionNombreEmpresa"]));
		$_POST["txtFacturacionDireccion"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ADDRESS,$_POST["txtFacturacionDireccion"]));
		$_POST["txtFacturacionCodigoPostal"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_ZIPCODE,$_POST["txtFacturacionCodigoPostal"]));
		$_POST["txtFacturacionCiudad"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_CITY,$_POST["txtFacturacionCiudad"]));
		$_POST["txtFacturacionProvincia"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_PROVINCE,$_POST["txtFacturacionProvincia"]));
		$_POST["txtFacturacionPais"]=generalUtils::escaparCadena(generalUtils::skipPlaceHolder(STATIC_FORM_PROFILE_BILLING_COUNTRY,$_POST["txtFacturacionPais"]));
		
		//Store values from billing detail inputs in variables
		$nifFactura=$_POST["txtFacturacionNifCliente"];
		$nombreClienteFactura=$_POST["txtFacturacionNombreCliente"];
		$nombreEmpresaFactura=$_POST["txtFacturacionNombreEmpresa"];
		$direccionFactura=$_POST["txtFacturacionDireccion"];
		$codigoPostalFactura=$_POST["txtFacturacionCodigoPostal"];
		$ciudadFactura=$_POST["txtFacturacionCiudad"];
		$provinciaFactura=$_POST["txtFacturacionProvincia"];
		$paisFactura=$_POST["txtFacturacionPais"];
			
		
		//Set the "concepto" to "Registration" (id_concepto_movimiento = 55)
		$idConceptoMovimiento=MOVIMIENTO_CONCEPTO_NEW_WORKSHOP;

		$db->startTransaction();

		//If payment method is Paypal, se registration status to "Pending"
		if($metodoPago==INSCRIPCION_TIPO_PAGO_PAYPAL){
			//Si estamos pagando por paypal o tpv... el estado es pendiente hasta que se haga el pago
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_PENDIENTE;
		//Otherwise set registration to "Not paid" and status to "Confirmed"
		}else{
			$pagado=0;
			$idEstadoInscripcion=INSCRIPCION_ESTADO_INSCRIPCION_CONFIRMADA;
		}

		//If price is zero, set email as "Sent" and registration as "Paid"
		if($precio==0){		
			$esMailEnviado=1;
			$pagado=1;
		}
		

		//Insert workshop signup in "ed_tb_inscripcion_taller" in database, using variables from above
        //All workshops are inserted, both bank transfer and Paypal
		$resultadoInscripcion=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_insertar(".$idUsuarioWeb.",".$_POST["cmbAsociacionHermana"].",".$idEstadoInscripcion.",".$metodoPago.",".$_POST["cmbTitulo"].",'".$_POST["txtNombre"]."','".$_POST["txtApellidos"]."','".$_POST["txtEmail"]."','".$_POST["txtTelefono"]."','".$comentarios."','".$_POST["txtFacturacionNifCliente"]."','".$_POST["txtFacturacionNombreCliente"]."','".$_POST["txtFacturacionNombreEmpresa"]."','".$_POST["txtFacturacionDireccion"]."','".$_POST["txtFacturacionCodigoPostal"]."','".$_POST["txtFacturacionCiudad"]."','".$_POST["txtFacturacionProvincia"]."','".$_POST["txtFacturacionPais"]."',".$esFactura.",".$pagado.",".$esMailEnviado.",".$privacy.")");
		//Retrieve signup data from the database (each signup now a database row)
		$datoInscripcion=$db->getData($resultadoInscripcion);
		
		//For each workshop signup in turn, create a record in "ed_tb_inscripcion_taller_linea" (for price and "paid", "attended" and "deleted" status)
		for($i=0;$i<$totalVectorTalleres;$i++){
			//The "codigo" field is the id_inscripcion-taller generated automatically on insertion
		    $db->callProcedure("CALL ed_sp_web_inscripcion_taller_linea_insertar(".$datoInscripcion["codigo"].",".$vectorTalleresInsertar[$i]["id"].",'".$vectorTalleresInsertar[$i]["precio"]."','".$vectorTalleresInsertar[$i]["pagado"]."')");
		}

		//Store payment method (id_tipo_pago) in TIPO_RESULTADO_INSCRIPCION in "process_form.html" template
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$metodoPago);
        //Initiate next step in process, depending on payment method
        switch($metodoPago){
            //If payment method is Paypal…
			case INSCRIPCION_TIPO_PAGO_PAYPAL:
				$subPlantilla=new XTemplate("../html/ajax/paypal_form.html");
				$serverActual="http://www.metmeetings.org/";
				
				//Generate encryption
				require "../classes/paypal/clase.paypal.php";
				$config = array(
		            "cert_id" => "PR3V5CJ4DN4BA",
		            "business" => "metmember@gmail.com",
		            "openssl" => "/usr/bin/openssl",
		            "my_cert" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-pubcert.pem",
		            "my_key" => "../classes/paypal/certificados/8708ec99f0a753dddea0757b7d35297d-prvkey.pem",
		            "paypal_cert" => "../classes/paypal/certificados/paypal_cert.pem"
		    	);

				//Information for Paypal
				$form["charset"]="UTF-8";	
				$form["cmd"]="_xclick";

				//Paypal variables
				$form["item_name"]="workshop registration";
				$form["amount"]=$precio;	
				//$form["amount"]="0.01";	

			    $paypal = new PayPal($config);

			    $form["return"]=$serverActual."inscripcion_finalizada.php?modo=2&tipo=2";
			    $form["notify_url"]=$serverActual."ajax/last_step_inscription_workshop_paypal.php";
			    $form["return_cancel"]=$serverActual."inscripcion_finalizada.php?tipo=0";
			    $form["rb"]="2";
			    $form["bn"]="workshop registration";
			    $form["upload"]="1";
			    $form["business"]="metmember@gmail.com";
			    $form["currency_code"]="EUR";
			   	$form["custom"]=$datoInscripcion["numero_inscripcion"]."-".$datoInscripcion["codigo"]."-1";

				//Encryption
			   	$subPlantilla->assign("ENCRYPT",$paypal->encrypt($form));
				
				
				//Parse all the info
				$subPlantilla->parse("contenido_principal");
				
				//Export the Paypal subtemplate (paypal_form.html) to the main template (process_form.html)
				$plantilla->assign("FORMULARIO_ADICIONAL",$subPlantilla->text("contenido_principal"));
				break;
			//If payment method is bank transfer, store signup ID etc. in variables and move to next process
			case INSCRIPCION_TIPO_PAGO_TRANSFERENCIA:
				$idUsuarioWebCorreo=$idUsuarioWeb;
				$numeroInscripcion=$datoInscripcion["numero_inscripcion"];
				$tipoInscripcion=1;
				$idInscripcion=$datoInscripcion["codigo"];
				
				require "../includes/load_send_mail_inscription_workshop.inc.php";
				break;
		}
		//Clear captcha image
		$img->clearCode();
		$db->endTransaction();
		
	//If form data is not valid, store error message in the "TIPO_RESULTADO_INSCRIPCION" placeholder in the "process_form.html" template
  	}else{
		$plantilla->assign("TIPO_RESULTADO_INSCRIPCION",$mensajeError);
	}//end else
	
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>