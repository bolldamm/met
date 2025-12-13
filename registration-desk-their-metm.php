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

$attendeeName = $_SESSION["conference_attendee_name"];
unset($_SESSION["conference_attendee_name"]);

// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");

// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
$subPlantilla = new XTemplate("html/directory_list_conference.html");

// Asignamos el CSS que corresponde a este apartado
$plantilla->assign("SECTION_FILE_CSS",
        "directory_list.css");

require "includes/load_structure.inc.php";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$numeroConferencia = $row["current_id_conferencia"];

//Obtenemos la url asociada a members directory search
$resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
$datoMenuSeo = $db->getData($resultadoMenuSeo);
$vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
$vectorAtributosMenu["id_menu"] = $idMenu;
$vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
$urlActualAux = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

$subPlantilla->assign("CONTENIDO_DESCRIPCION",
        $datoMenuSeo["descripcion"]);

$resultInits = $db->callProcedure("CALL ed_sp_obtener_listado_conferencia_inits('$numeroConferencia','2')");

$initMenu = "<table style='font-size:150%;width:100%;table-layout:fixed;text-align:center;'><tr><td style='width:100%;border:solid black 1px;'><a href='menu-letter-setter.php'>All</a></td>";

while ($dataInit = $db->getData($resultInits)) {
  $initMenu = $initMenu . "<td style='width:100%;border:solid black 1px;'><a href='menu-letter-setter.php?name=" . $dataInit["Init"] . "'>". strtoupper($dataInit["Init"]) . "</a></td>";
  
  ++$tcont;
  if ($tcont == 12) {
            $initMenu = $initMenu . "<tr></tr>";
        }
}

$initMenu = $initMenu . "</tr></table><br />";

$subPlantilla->assign("CONTENIDO_MENU", $initMenu);

//Listamos los miembros del directorio
if (!$attendeeName) {
  $codeProcedure = "CALL ed_sp_obtener_listado_conferencia_completo('$numeroConferencia','2'";
} else {
  $codeProcedure = "CALL ed_sp_obtener_listado_conferencia_filtrado('$numeroConferencia','".$attendeeName."','2'";
}

$totalItemsPagina = 300;
//$totalItemsPagina = 2;	

$urlActual = $urlActualAux . "-";
require "includes/load_paginator.php";

$resultMiembros = $db->callProcedure($codeProcedure);

$cont = 0;
if ($db->getNumberRows($resultMiembros) > 0) {
    while ($dataMiembro = $db->getData($resultMiembros)) {
        //Get the photo for each attendee
      	//if there's a photo in the METM photo upload location, use that
        if (!$dataMiembro["imagen"] == "") {
            $imagen = "files/METM_attendees/" . $dataMiembro["imagen"];
        // otherwise, if the attendee is a member
        } else if (!$dataMiembro["id_usuario_web"] == "") {
            $resultadoUsuarioIndividual = $db->callProcedure("CALL ed_sp_obtener_imagen(" . $dataMiembro["id_usuario_web"] . ")");
            $datoUsuarioIndividual = $db->getData($resultadoUsuarioIndividual);
          	//if the member has a photo in her profile, use that
            if (!$datoUsuarioIndividual["imagen"] == "") {
                $imagen = "files/members/" . $datoUsuarioIndividual["imagen"];
            //otherwise use the default image (MET logo)
            } else {
                $imagen = "files/members/default.jpg";
            }
        //and if the attendee is not a member, use the default image (MET logo)
        } else {
            $imagen = "files/members/default.jpg";
        }

        $badgelines[0] = $dataMiembro["nombre"] . " " . $dataMiembro["apellidos"];
      
        if($dataMiembro["asistido"]){
          $badgelines[1] = "REGISTERED";
        }else{
          $badgelines[1] = "NOT REGISTERED";
        }

            if($dataMiembro["id_usuario_web"]){
        $badgelines[2] = "MET member ";
      } elseif($dataMiembro["id_asociacion_hermana"]) {
        $badgelines[2] = "Sister member ";
      } else {
        $badgelines[2] = "Non-member ";
      }
      
      $resultSpecialrate = $db->callProcedure("CALL ed_pr_metm_special_rate_name(".$dataMiembro["speaker"].")");
      $specialRate = $resultSpecialrate->fetch_assoc();
      if ($specialRate["name_type"]) {
      $speakerHelper = "/ <span style='color:".$specialRate['colour'].";'>" . $specialRate["name_type"] . "</span> ";
      } else{
        $speakerHelper = "";
      }
      
      $resultFTCouncil = $db->callProcedure("CALL ed_pr_first_timer_or_council_name(".$dataMiembro["first_timer_or_council"].")");
      $FTCouncil = $resultFTCouncil->fetch_assoc();
      if ($FTCouncil["name_role"]) {
      $firstTimer = "/ <span style='color:".$FTCouncil['colour'].";'>" . $FTCouncil['name_role'] . "</span> ";
      } else{
        $firstTimer = "";
      }
      
      if($dataMiembro["observaciones"]) {
        $notes = "<br />" . $dataMiembro["observaciones"];
      } else {
        $notes = "";
      }
      
      if ($dataMiembro["comentarios"]) {
        if ($notes) {
          $notes = $notes."<br /><em>".$dataMiembro["comentarios"]."</em>";
        } else {
          $notes = "<em>".$dataMiembro["comentarios"]."</em>";
        }
      } 
      
      $badgelines[2] = $badgelines[2] . $firstTimer . $speakerHelper . $notes;

        $subPlantilla->assign("ITEM_MIEMBRO_ONCLICK", "location.href='"."/their-metm.php?attendee=".$dataMiembro["id_inscripcion_conferencia"]."&name=".str_replace("'","%27",$badgelines[0])."'");
       	$subPlantilla->assign("ITEM_MIEMBRO_BADGE1", "<a href='"."/their-metm.php?attendee=".$dataMiembro["id_inscripcion_conferencia"]."&name=".str_replace("'","%27",$badgelines[0])."'>".$badgelines[0]."</a>");
        $subPlantilla->assign("ITEM_MIEMBRO_BADGE2",
                $badgelines[1]);
        $subPlantilla->assign("ITEM_MIEMBRO_BADGE3",
                $badgelines[2]);

        $subPlantilla->assign("ITEM_MIEMBRO_IMAGEN",
                $imagen);

        $subPlantilla->parse("contenido_principal.listado_miembros.tr.item_miembro");

        ++$cont;
        if ($cont % 2 == 0) {
            $subPlantilla->parse("contenido_principal.listado_miembros.tr");
        }
    }
    if ($cont % 2 != 0) {
        $subPlantilla->parse("contenido_principal.listado_miembros.tr");
    }

    $subPlantilla->parse("contenido_principal.listado_miembros");
} else {
    $subPlantilla->parse("contenido_principal.no_miembros");
}
//}

$subPlantilla->assign("MENU_ID",
        $idMenu);

//Cargamos el breadcrumb
require "includes/load_breadcrumb.inc.php";

//Cargamos los menus hijos del lateral derecho
// require "includes/load_menu_left.inc.php";

//Cargamos el slider en caso de que tenga imagenes
require "includes/load_slider.inc.php";

$subPlantilla->parse("contenido_principal");

/**
 * Realizamos todos los parse realcionados con este apartado
 */
$plantilla->parse("contenido_principal.css_form");
$plantilla->parse("contenido_principal.control_superior");
$plantilla->parse("contenido_principal.bloque_ready");


//Exportamos plantilla secundaria a la principal
$plantilla->assign("CONTENIDO",
$subPlantilla->text("contenido_principal"));

//Parse inner page content with lefthand menu
$plantilla->parse("contenido_principal.full_width_content");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>