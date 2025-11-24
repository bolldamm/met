<?php

/**
 * 
 * Pagina de inicio desde donde se inicia el portal
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */
require "includes/load_main_components.inc.php";

// Instanciamos la clase Xtemplate con la plantilla base
$plantilla = new XTemplate("html/index.html");

// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
$subPlantilla = new XTemplate("html/directory_list_conference.html");

// Asignamos el CSS que corresponde a este apartado
$plantilla->assign("SECTION_FILE_CSS",
        "directory_list.css");

require "includes/load_structure.inc.php";

//Obtenemos la conferencia actual
$conferenciaactual = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");
$datoConferencia = $db->getData($conferenciaactual);
$numeroConferencia = $datoConferencia["id_conferencia"];

if(!$numeroConferencia) {
  generalUtils::redirigir(CURRENT_DOMAIN);
}

//Obtenemos la url asociada a members directory search
$resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
$datoMenuSeo = $db->getData($resultadoMenuSeo);
$vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
$vectorAtributosMenu["id_menu"] = $idMenu;
$vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
$urlActualAux = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

$subPlantilla->assign("CONTENIDO_DESCRIPCION",
        $datoMenuSeo["descripcion"]);

//Listamos los miembros del directorio
$codeProcedure = "CALL ed_sp_obtener_listado_conferencia('$numeroConferencia'";

$totalItemsPagina = 250;
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

      	// Split badge text into separate lines
        $badgetext = $dataMiembro["conference_badge"];
        $badgelines = explode("<br />",
                $badgetext);
      
      // Badge structure up to and including METM20 Online
//        $nameLine = $badgelines[0];
//        $firstLine = $badgelines[1];
//        $secondLine = $badgelines[2];
      // New badge structure from METM21 onwards
        $nameLine = $badgelines[0] . " " . $badgelines[1];
        $firstLine = $badgelines[2];
        $secondLine = $badgelines[3];      

      	// if attendee is a member, check whether MET profile is public or not
        if (!$dataMiembro["id_usuario_web"] == "") {
          $usuarioWeb = $db->callProcedure("CALL ed_sp_usuario_web_individual_obtener_concreto(".$dataMiembro["id_usuario_web"].")");
          $datoUsuario = $db->getData($usuarioWeb);
          $publicProfile = $datoUsuario["publico"];
          	// if profile is public, add hyperlink to MET member profile
          	if($publicProfile == 1){
          		//Get URL of member profile
		        $vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
		        //$vectorAtributosDetalle["id_menu"] = $_GET["menu"];
		        $vectorAtributosDetalle["id_menu"] = 19;
		        $vectorAtributosDetalle["id_detalle"] = $dataMiembro["id_usuario_web"];
		        $vectorAtributosDetalle["seo_url"] = $datoUsuario["nombre"] . " " . $datoUsuario["apellidos"];
		        $urlProfile = generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
          		$subPlantilla->assign("ITEM_MIEMBRO_BADGE1", "<a href='".$urlProfile."'>".$nameLine."</a>");
              // if profile is not public, just the name with no link
	          } else {
        		$subPlantilla->assign("ITEM_MIEMBRO_BADGE1", $nameLine);
	          }
          // and if not a member, just the name with no link
          } else {
          	$subPlantilla->assign("ITEM_MIEMBRO_BADGE1", $nameLine);
          }
        $subPlantilla->assign("ITEM_MIEMBRO_BADGE2",
                $firstLine);
        $subPlantilla->assign("ITEM_MIEMBRO_BADGE3",
                $secondLine);

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
require "includes/load_menu_left.inc.php";

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
$plantilla->parse("contenido_principal.menu_left");

//Parseamos y sacamos informacion por pantalla
$plantilla->parse("contenido_principal");
$plantilla->out("contenido_principal");
?>