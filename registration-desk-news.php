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

	$postID =  $_SESSION['postId'];
	unset($_SESSION['postId']);
	$postContent =  $_SESSION['postContent'];
	unset($_SESSION['postContent']);
	if (isset($_SESSION['postStatus'])) {
      $postStatus =  "checked";
    } else {
      $postStatus =  "";
    }	
	unset($_SESSION['postStatus']);

	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/forms/form_metm_news.html");
	
	$subPlantilla->assign("ID_MENU",$_GET["menu"]);
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "openbox.css");
	$plantilla->parse("contenido_principal.css_seccion");
	
	require "includes/load_structure.inc.php";
	
	if(isset($_GET["menu"]) && is_numeric($_GET["menu"])) {
		//Cargamos toda la información de openbox
		$resultOpenbox = $db->callProcedure("CALL ed_sp_web_openbox_obtener(".$_SESSION["id_idioma"].", ".$_GET["menu"].")");
		if($db->getNumberRows($resultOpenbox) > 0) {
			$dataOpenbox = $db->getData($resultOpenbox);
			$subPlantilla->assign("OPENBOX_DESCRIPCION", $dataOpenbox["descripcion"]);
		}
	}
	
	$plantilla->assign("TITULO_WEB", STATIC_TITLE_WEB_HOME);
	
	//Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";

          $results = $db->callProcedure("CALL ed_pr_get_last_minute_news_full()");
          $newsString = "<table>";
          if ($db->getNumberRows($results) > 0) {
          	while ($newsData = $db->getData($results)) {  
            $newsItem = $newsData["item"];
            if (!$newsData["activo"]) {
              $newsOff = "color:red;";
              } else {
              $newsOff = "";
            }
            $newsItem = str_replace("<p>", "", $newsItem);
            $newsItem = str_replace("</p>", "", $newsItem);
            $newsDate =  date('d/m/Y H:i', strtotime($newsData["time"]) - STATIC_CONFERENCE_TMIE_OFFSET_MINUTES);
          	$newsString = $newsString . "<tr style='border: 1px solid black;" . $newsOff . "'><td>" . $newsDate . "</td><td>" . $newsItem . "</td><td><form action='post_last_minute_news.php' method='post'><input type='hidden' id='postAction' name='postAction' value='editpost'><input type='hidden' id='postId' name='postId' value='".$newsData["ID"]."'><input type='button' class='btn btn-primary float-left' id='btnSendForm' value='Edit' onclick='submit()'></form></td><td><form action='post_last_minute_news.php' method='post'><input type='hidden' id='postAction' name='postAction' value='deletepost'><input type='hidden' id='postId' name='postId' value='".$newsData["ID"]."'><input onClick='return confirmSubmit()' type='submit' class='btn btn-primary float-left' id='btnSendForm' value='Delete'></form></tr></td>";      
          	}
          }
$newsString = $newsString . "</table>";
      $subPlantilla->assign("LAST_MINUTE_NEWS",$newsString);

      if ($postID) {
        $subPlantilla->assign("POST_CONTENT",$postContent);
        $subPlantilla->assign("POST_NUMBER",$postID);
        $subPlantilla->assign("ACTIVO",$postStatus);
      } else {
        $subPlantilla->assign("POST_TIME","style='display:none;'");
        $subPlantilla->assign("ACTIVO","checked");
      }



$plantilla->assign("TEXTAREA_ID", "postContent");
$plantilla->assign("TEXTAREA_TOOLBAR", "Minimo");
$plantilla->parse("contenido_principal.bloque_ready.inicializar_ckeditor");

	$subPlantilla->parse("contenido_principal");

	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.editor_script");
	$plantilla->parse("contenido_principal.css_form");
	$plantilla->parse("contenido_principal.bloque_ready");
	$plantilla->parse("contenido_principal.control_superior");


	
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.full_width_content");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>