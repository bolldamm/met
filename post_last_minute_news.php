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
$postTime =  $_POST['post_time'];
$postStatus =  $_POST['activo'];
$postContents = generalUtils::escaparCadena($_POST['txtContenido']);
$postAction  =  $_POST['postAction'];

if ($postAction == "deletepost") {
	$db->callProcedure("CALL ed_pr_last_minute_news_delete($postID)");
} else if ($postAction == "editpost") {
    $result = $db->callProcedure("CALL ed_pr_get_specific_last_minute_news($postID)");
    $dato = $db->getData($result);
  
  $_SESSION['postId'] = $postID;
  $_SESSION['postContent'] = $dato["item"];
  if ($dato["activo"]) {
    $_SESSION['postStatus'] = $dato["activo"];
  }  

} else {  
  if ($postID) {
	  if ($postTime == "now") {
		  $db->callProcedure("CALL ed_pr_last_minute_news_insert('$postContents','$postStatus')");
		  $db->callProcedure("CALL ed_pr_last_minute_news_delete($postID)");
	  } else {
		  $db->callProcedure("CALL ed_pr_last_minute_news_update('$postContents',$postID,'$postStatus')");
	  }
 
  } else {
	$db->callProcedure("CALL ed_pr_last_minute_news_insert('$postContents','$postStatus')");  
  }
}

generalUtils::redirigir(CURRENT_DOMAIN . "/en/last-minute-news:1416");

?>