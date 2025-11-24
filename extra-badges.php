<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
     * https://www.metmeetings.org/en/extra-badges:1474
     * ed_tb_metm_special_rate
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
	$badgetext =  $_SESSION['postContent'];
	unset($_SESSION['postContent']);
      $badgelines = explode("<br />", $badgetext);
      $badgelines = str_replace("&comma;", ",", $badgelines);
	$SpeakerHelper =  $_SESSION['postSpeaker'];
	unset($_SESSION['postSpeaker']);

  $cb1 = $_SESSION['postcb1'];
unset($_SESSION['postcb1']);
  $cb2 = $_SESSION['postcb2'];
unset($_SESSION['postcb2']);
  $cb3 = $_SESSION['postcb3'];
unset($_SESSION['postcb3']);
  $cb4 = $_SESSION['postcb4'];
unset($_SESSION['postcb4']);
  $cb5 = $_SESSION['postcb5'];
unset($_SESSION['postcb5']);
  $noPrint = $_SESSION['noPrint'];
unset($_SESSION['noPrint']);


	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/forms/form_extra_badges.html");
	
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

          $results = $db->callProcedure("CALL ed_pr_get_extra_badges(2)");
          $newsString = "<table style='width:100%;'>";
          if ($db->getNumberRows($results) > 0) {
          	while ($newsData = $db->getData($results)) {  
            $newsItem = $newsData["conference_badge"];
            $newsItem = str_replace("<p>", "", $newsItem);
            $newsItem = str_replace("</p>", "", $newsItem);
            $newsDate =  $newsData["name_type"];
               
          	$newsString = $newsString . "<tr style='border: 1px solid black;'><td>" . $newsDate . "</td><td>" . $newsItem . "</td><td><form action='post_extra_badge.php' method='post'><input type='hidden' id='postAction' name='postAction' value='editpost'><input type='hidden' id='postId' name='postId' value='".$newsData["ID"]."'><input type='button' class='btn btn-primary float-left' id='btnSendForm' value='Edit' onclick='submit()'></form></td><td><form action='post_extra_badge.php' method='post'><input type='hidden' id='postAction' name='postAction' value='deletepost'><input type='hidden' id='postId' name='postId' value='".$newsData["ID"]."'><input onClick='return confirmSubmit()' type='submit' class='btn btn-primary float-left' id='btnSendForm' value='Delete'></form></tr></td>";      
          	}
          }
$newsString = $newsString . "</table>";
      $subPlantilla->assign("LAST_MINUTE_NEWS",$newsString);

      if ($postID) {
        $subPlantilla->assign("POST_CONTENT",$postContent);
        $subPlantilla->assign("POST_NUMBER",$postID);
        
        
        if ($cb1 > 0) {
            $subPlantilla->assign("CB1_CHECKED","checked");
        }
        if ($cb2 > 0) {
            $subPlantilla->assign("CB2_CHECKED","checked");
                }
        if ($cb3 > 0) {
            $subPlantilla->assign("CB3_CHECKED","checked");
                }
        if ($cb4 > 0) {
            $subPlantilla->assign("CB4_CHECKED","checked");
                }
        if ($cb5 > 0) {
            $subPlantilla->assign("CB5_CHECKED","checked");
                }
        if ($noPrint > 0) {
            $subPlantilla->assign("NP_CHECKED","checked");
                }        
      }

$resultCB = $db->callProcedure("CALL ed_pr_get_coffee_breaks");

// Array to store quantities
    $quantitiescb = array();
	
// Fetch quantities and store them in the array
    while ($row = $resultCB->fetch_assoc()) {
        $quantitiecb[] = $row['coffee_break_name'];
    }
	
// Assign quantities to output
for ($i = 0; $i < count($quantitiecb); $i++) {
    $subPlantilla->assign("CB_" . ($i + 1), $quantitiecb[$i]);
}

$plantilla->assign("TEXTAREA_ID", "postContent");

      require "includes/load_badge_code.php";
      $subPlantilla->assign("CONFERENCE_BADGE0", substr($badgelines[0],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE1", substr($badgelines[1],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE2", substr($badgelines[2],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE3", substr($badgelines[3],0,30));
      $subPlantilla->assign("CONFERENCE_BADGE4", substr($badgelines[5],0,30));

     $subPlantilla->assign("DYNAMIC_SRATE",$jsonsRate);
     $subPlantilla->assign("DYNAMIC_COLOUR",$jsonColour);
     $subPlantilla->assign("DYNAMIC_COUNCIL_MEMBER",$jsonsCouncil);
     $subPlantilla->assign("DYNAMIC_COUNCIL_COLOUR",$jsonsCouncilcol); 
     $subPlantilla->assign("DYNAMIC_BADGE_TEMPLATE",$badgeTemplate);
     $subPlantilla->assign("DYNAMIC_BADGE_JAVASCRIPT",$badgeJavascript);
     $specialRateSQL = "CALL ed_pr_metm_special_rate()";

      //Assign special rate drop down to placeholder in form template
     $subPlantilla->assign("COMBO_SPECIAL_RATE",
        generalUtils::construirCombo($db,
                $specialRateSQL,
                "cmbSpeaker",
                "cmbSpeaker",
                $SpeakerHelper,
                "name_type",
                "id_type",
                STATIC_GLOBAL_COMBO_DEFAULT,
                -1,
                "",
                'class="form-control" style="color:slategray;" onchange="speaker()"'));

     $subPlantilla->parse("contenido_principal.bloque_special_rate");

	$subPlantilla->parse("contenido_principal");

	/**
	 * Realizamos todos los parse realcionados con este apartado
	 */
	$plantilla->parse("contenido_principal.css_form");
	
	//Exportamos plantilla secundaria a la principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.full_width_content");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>
    