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
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "directory_list.css");
	
	require "includes/load_structure.inc.php";

	//Obtenemos la url asociada a members directory search
	
	//Listamos los miembros del directorio
	//$codeProcedure = "CALL ed_sp_obtener_listado_conferencia_archived('.$idConferencia.'";
	$codeProcedure = "CALL ed_sp_obtener_listado_conferencia_archived('17'";

		$totalItemsPagina = 150;
		//$totalItemsPagina = 2;
		
		$urlActual= $urlActualAux."-";
		require "includes/load_paginator.php";
	
		$resultMiembros = $db->callProcedure($codeProcedure);

		$cont = 0;
		if($db->getNumberRows($resultMiembros) > 0) {
			while($dataMiembro = $db->getData($resultMiembros)) {
				
				if(!$dataMiembro["imagen"] == ""){
					
					$imagen = "files/METM_attendees/".$dataMiembro["imagen"];
				}
				else if (!$dataMiembro["id_usuario_web"]==""){
					$resultadoUsuarioIndividual=$db->callProcedure("CALL ed_sp_obtener_imagen(".$dataMiembro["id_usuario_web"].")");
				$datoUsuarioIndividual=$db->getData($resultadoUsuarioIndividual);
					if(!$datoUsuarioIndividual["imagen"]==""){
						$imagen = "files/members/".$datoUsuarioIndividual["imagen"];
						}
						else{
							$imagen = "files/members/default.jpg";
							}
					} 
				else{
					$imagen = "files/members/default.jpg";
					}
				$subPlantilla->assign("ITEM_MIEMBRO_NOMBRE", $dataMiembro["nombre"].(($dataMiembro["apellidos"] == "") ? "" : " ".$dataMiembro["apellidos"]));

$badgetext = $dataMiembro["conference_badge"];
$badgelines = explode("<br />",$badgetext);
				
				//$subPlantilla->assign("ITEM_MIEMBRO_BADGE", $dataMiembro["conference_badge"]);
				$subPlantilla->assign("ITEM_MIEMBRO_BADGE1", $badgelines[0]);
				$subPlantilla->assign("ITEM_MIEMBRO_BADGE2", $badgelines[1]);
				$subPlantilla->assign("ITEM_MIEMBRO_BADGE3", $badgelines[2]);

				$subPlantilla->assign("ITEM_MIEMBRO_IMAGEN", $imagen);
				
				$subPlantilla->parse("contenido_principal.listado_miembros.tr.item_miembro");
				
				++$cont;
				if($cont % 2 == 0) {
					$subPlantilla->parse("contenido_principal.listado_miembros.tr");
				}
			}
			if($cont % 2 != 0) {
				$subPlantilla->parse("contenido_principal.listado_miembros.tr");
			}
			
			$subPlantilla->parse("contenido_principal.listado_miembros");
		}else{
			$subPlantilla->parse("contenido_principal.no_miembros");
		}
	//}
	
	$subPlantilla->assign("MENU_ID", $idMenu);
	
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
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
    //Parse inner page content with lefthand menu
    $plantilla->parse("contenido_principal.menu_left");

	//Parseamos y sacamos informacion por pantalla
	$plantilla->parse("contenido_principal");
	$plantilla->out("contenido_principal");
?>