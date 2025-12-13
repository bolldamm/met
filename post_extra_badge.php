<?php

/**
 * 
 * Pagina de inicio desde donde se inicia el portal
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
require "includes/load_main_components.inc.php";

if($_SESSION["met_user"]["tipoUsuario"] < TIPO_USUARIO_EDITOR)
{
	if(!isset($_SESSION["registration_desk"]))
		{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
}

$postID =  $_POST['postId'];
// $postContents = generalUtils::escaparCadena($_POST['txtContenido']);
$BadgeNombre = generalUtils::escaparCadena($_POST["txtBadgeNombre"]);
$BadgeApellidos = generalUtils::escaparCadena($_POST["txtBadgeApellidos"]);
$BadgeFirst = substr(generalUtils::escaparCadena($_POST["txtBadgeFirst"]), 0, STATIC_CONFERENCE_BADGE_LENGTH);
$BadgeSecond = substr(generalUtils::escaparCadena($_POST["txtBadgeSecond"]), 0, STATIC_CONFERENCE_BADGE_LENGTH);
$BadgeThird = substr(generalUtils::escaparCadena($_POST["txtBadgePronouns"]), 0, STATIC_CONFERENCE_BADGE_LENGTH);
$postContents = $BadgeNombre . "<br />" . $BadgeApellidos . "<br />" . $BadgeFirst . "<br />" . $BadgeSecond . "<br />" . $newCouncilrole . "<br />" . $BadgeThird;

if (isset($_POST["twBreak"])) {
  $twBreak = 1;
  } else {
  $twBreak = 0;
} 
if (isset($_POST["fwBreak"])) {
  $fwBreak = 1;
  } else {
  $fwBreak = 0;
} 
if (isset($_POST["faBreak"])) {
  $faBreak = 1;
  } else {
  $faBreak = 0;
} 
if (isset($_POST["smBreak"])) {
  $smBreak = 1;
  } else {
  $smBreak = 0;
} 
if (isset($_POST["saBreak"])) {
  $saBreak = 1;
  } else {
  $saBreak = 0;
} 
if (isset($_POST["noPrint"])) {
  $noPrint = 1;
  } else {
  $noPrint = 0;
} 

if (isset($_POST["cmbSpeaker"])) {
  $SpeakerHelper = $_POST["cmbSpeaker"];
 } else {
  $SpeakerHelper = 1;
}
if ($SpeakerHelper < 1) {
  $SpeakerHelper = 1;
 } 

$postAction  =  $_POST['postAction'];

if ($postAction == "deletepost") {
	$db->callProcedure("CALL ed_pr_extra_badge_delete($postID)");
} else if ($postAction == "editpost") {
    $result = $db->callProcedure("CALL ed_pr_get_specific_extra_badge($postID)");
    $dato = $db->getData($result);
  
  $_SESSION['postId'] = $postID;
  $_SESSION['postContent'] = $dato["conference_badge"];
  $_SESSION['postSpeaker'] = $dato["speaker"];
  $_SESSION['postcb1'] = $dato["coffee_break_1"];
  $_SESSION['postcb2'] = $dato["coffee_break_2"];
  $_SESSION['postcb3'] = $dato["coffee_break_3"];
  $_SESSION['postcb4'] = $dato["coffee_break_4"];
  $_SESSION['postcb5'] = $dato["coffee_break_5"];
  $_SESSION['noPrint'] = $dato["no_print"];

} else {  
  if ($postID) {
    $db->callProcedure("CALL ed_pr_extra_badge_update('$postContents',$SpeakerHelper,$postID,$twBreak,$fwBreak,$faBreak,$smBreak,$saBreak,$noPrint)");
 
  } else {
	$db->callProcedure("CALL ed_pr_extra_badge_insert('$postContents',$SpeakerHelper,$twBreak,$fwBreak,$faBreak,$smBreak,$saBreak,$noPrint)");  
  }
}
// echo "Ciao";
// echo $twBreak;
generalUtils::redirigir(CURRENT_DOMAIN . "/en/extra-badges:1474");

?>