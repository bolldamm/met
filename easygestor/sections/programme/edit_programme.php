<?php
	/**
	 * 
	 * Script for tweaking global settings
	 * @Author Mike
	 * 
     * You have to edit /easygestor/config/controller.php
     * and /easygestor/config/sections.php too!
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)){
      
      $permitted_chars = '0123456789';
	function generate_string($input, $strength)
	{
	    $input_length = strlen($input);
	    $random_string = '';
	    for ($i = 0; $i < $strength; $i++) {
	        $random_character = $input[mt_rand(0, $input_length - 1)];
	        $random_string .= $random_character;
	    }
	    return $random_string;
	}

	//Store name of existing image or empty variable
	if (isset($_POST["hdnNombreImagen_1"]) && $_POST["hdnNombreImagen_1"] != "default.jpg") {
    	$fileName = $_POST["hdnNombreImagen_1"];
	} else {
    	$fileName = "";
	}

	//If a new image has been added
	if ($_FILES["fileAttendeePhoto"]["tmp_name"]) {
	    $imageFileExtension = generalUtils::obtenerExtensionFichero($_FILES["fileAttendeePhoto"]["name"]);
	    $imageFileName = generate_string($permitted_chars, 10) . "-" . date("his");
	    $fileName = $imageFileName . "." . $imageFileExtension;
	    $target = $_SERVER['DOCUMENT_ROOT'] . "/files/certificates/" . $fileName;
	    move_uploaded_file($_FILES['fileAttendeePhoto']['tmp_name'], $target);
        $fileName = "/files/certificates/".$fileName;
	}

      //Guardamos datos
      
      $Chair = generalUtils::escaparCadena($_POST["txtChair"]);
      $Vicechair = generalUtils::escaparCadena($_POST["txtVicechair"]); 
      $Secretary = generalUtils::escaparCadena($_POST["txtSecretary"]);
      $Treasurer = generalUtils::escaparCadena($_POST["txtTreasurer"]);
      $Membership = generalUtils::escaparCadena($_POST["txtMembership"]);
      $Development = generalUtils::escaparCadena($_POST["txtDevelopment"]);
      $Promotion = generalUtils::escaparCadena($_POST["txtPromotion"]);
      $Webmaster = generalUtils::escaparCadena($_POST["txtWebmaster"]);
      $membershipForms = generalUtils::escaparCadena($_POST["txtmembershipForms"]);
      
      $Newmember = generalUtils::escaparCadena($_POST["txtNewmember"]);
      $Renewmember = generalUtils::escaparCadena($_POST["txtRenewmember"]);
      $Workshopform = generalUtils::escaparCadena($_POST["txtWorkshopform"]);
      $Conferenceform = generalUtils::escaparCadena($_POST["txtConferenceform"]);
      $Individual = generalUtils::escaparCadena($_POST["txtIndividual"]);
      $Discount = generalUtils::escaparCadena($_POST["txtDiscount"]);
      $Institution = generalUtils::escaparCadena($_POST["txtInstitution"]);
      $Username = generalUtils::escaparCadena($_POST["txtUsername"]);
      $Deskpw = generalUtils::escaparCadena($_POST["txtDeskpw"]);
      $Password = generalUtils::escaparCadena($_POST["txtPassword"]);
	  $conferenceFormtest = generalUtils::escaparCadena($_POST["txtconferenceFormtest"]);
      
//      $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_save_settings('".$Chair."','".$Vicechair."','".$Secretary."','".$Treasurer."','".$Membership."','".$Development."','".$Promotion."','".$Webmaster."','".$fileName."','".$membershipForms."','".$Newmember."','".$Renewmember."','".$Workshopform."','".$Conferenceform."',".$Individual.",".$Discount.",".$Institution.")");
      
//       $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_save_settings('".$Chair."','".$Vicechair."','".$Secretary."','".$Treasurer."','".$Membership."','".$Development."','".$Promotion."','".$Webmaster."','".$fileName."','".$membershipForms."','".$Newmember."','".$Renewmember."','".$Workshopform."','".$Conferenceform."',".$Individual.",".$Discount.",".$Institution.",'".$Username."','".$Deskpw."','".$Password."')");
      $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_save_settings('".$Chair."','".$Vicechair."','".$Secretary."','".$Treasurer."','".$Membership."','".$Development."','".$Promotion."','".$Webmaster."','".$fileName."','".$membershipForms."','".$Newmember."','".$Renewmember."','".$Workshopform."','".$Conferenceform."',".$Individual.",".$Discount.",".$Institution.",'".$Username."','".$Deskpw."','".$Password."','".$conferenceFormtest."')");
      generalUtils::redirigir($_SERVER['HTTP_REFERER']);
      
    }
	
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");

	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");

	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/settings/global_settings.html");

	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	// $vectorMigas[1]["url"]="";
	$vectorMigas[1]["texto"]="Settings";

	require "includes/load_breadcumb.inc.php";
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>