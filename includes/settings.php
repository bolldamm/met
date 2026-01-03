<?php
	/**
	 * 
	 * Definicion de los settings
	 * @author Mike
	 * 
	 */
$absolutePath=dirname(__FILE__);
require_once $absolutePath."/../classes/databaseConnection.php";
require_once $absolutePath."/../database/connection.php";

	//Obtenemos datos	
	$resultado=$db->callProcedure("CALL ed_pr_get_settings()");
	$dato=$db->getData($resultado);

      $emailOpening=$dato['w_email_1'];
      $emailClosing=$dato['w_email_2'];
	  $txtChair=$dato['chair'];
	  $txtVicechair=$dato['vice_chair'];
	  $txtSecretary=$dato['secretary'];
	  $txtSecretarysig=$dato['secretary_sig'];
	  $txtTreasurer=$dato['treasurer'];
	  $txtDevelopment=$dato['development'];
	  $txtMembership=$dato['membership'];
	  $txtPromotion=$dato['promotion'];
	  $txtWebmaster=$dato['webmaster'];
	  $txtMembershipcode=$dato['membership_code'];
	  $txtRenewalcode=$dato['renewal_code'];
	  $txtWorkshopcode=$dato['workshop_code'];
	  $txtConferencecode=$dato['conference_code'];
	  $txtFullindividual=$dato['full_individual'];
	  $txtDiscountindividual=$dato['discount_individual'];
	  $txtInstitutional=$dato['institutional'];
	  $txtDeskusername=$dato['desk_username'];
	  $txtDeskpassword=$dato['desk_password'];
	  $txtNoreplypassword=$dato['no_reply_password'];
	  $txtConferencetestmode=$dato['conference_test_mode'];

// Initialize radio button variables to avoid warnings
$txtmembershipForms = '';
$txtmembershipForms2 = '';
$txtmembershipForms0 = '';

// Set the correct radio button as "checked" based on database value
if ($dato['membership_forms']==1) {
  $txtmembershipForms="checked";
} else if ($dato['membership_forms']==2) {
  $txtmembershipForms2="checked";
} else {
  $txtmembershipForms0="checked";
}

if ($dato['conference_test_mode']==1) {
  $txtStatictest="checked";
} else {
  $txtStatictest="";
}

 define("STATIC_CHAIR", $txtChair);
 define("STATIC_VICE_CHAIR", $txtVicechair);
 define("STATIC_SECRETARY", $txtSecretary);
 define("STATIC_SECRETARY_SIG", $txtSecretarysig);
 define("STATIC_TREASURER", $txtTreasurer);
 define("STATIC_MEMBERSHIP", $txtMembership);
 define("STATIC_CPD", $txtDevelopment);
 define("STATIC_PROMOTION", $txtPromotion);
 define("STATIC_WEBMASTER", $txtWebmaster);
 define("EMAIL_OPENING", $emailOpening);
 define("EMAIL_CLOSING",$emailClosing);
 define("MEMBERSHIP_FORMS",$txtmembershipForms);
 define("MEMBERSHIP_FORMS2",$txtmembershipForms2);
 define("MEMBERSHIP_FORMS0",$txtmembershipForms0);
 define("STATIC_DISCOUNT_MEMBERSHIP", $txtMembershipcode);
 define("STATIC_DISCOUNT_RENEW_MEMBERSHIP", $txtRenewalcode);
 define("STATIC_DISCOUNT_WORKSHOP", $txtWorkshopcode);
 define("STATIC_DISCOUNT_CONFERENCE", $txtConferencecode);
 define("PRECIO_MODALIDAD_USUARIO_INDIVIDUAL", $txtFullindividual);
 define("PRECIO_MODALIDAD_USUARIO_INSTITUTIONAL", $txtInstitutional);
 define("PRECIO_MODALIDAD_USUARIO_INDIVIDUAL_DESCUENTO", $txtDiscountindividual);
 define("STATIC_REGISTRATION_DESK_USERNAME", $txtDeskusername);
 define("STATIC_REGISTRATION_DESK_PASSWORD", $txtDeskpassword);
 define("STATIC_EMAIL_PASSWORD", $txtNoreplypassword);
 define("STATIC_CONFERENCE_FORM_TEST", $txtStatictest);
 define("STATIC_CONFERENCE_TEST", $txtConferencetestmode);

// For local testing
if (file_exists(__DIR__ . '/settings.local.php')) {
    require __DIR__ . '/settings.local.php';
}
