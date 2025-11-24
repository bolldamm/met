<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */
	function hideEmail($email){ 
  $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
  $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999);
  for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])];
  $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
  $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
  $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
  $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; 
  $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';
  return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
}

require "includes/load_main_components.inc.php";
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/directory_list.html");
	
	// Asignamos el CSS que corresponde a este apartado
	$plantilla->assign("SECTION_FILE_CSS", "directory_list.css");
	
	require "includes/load_structure.inc.php";

	/*
       if(isset($_GET["filtro"]) && $_GET["filtro"]!=""){
		//El primer campo pertenece a la query y el segundo a la fecha
		$camposFiltro=explode("_",$_GET["filtro"]);
		if($camposFiltro[0]!=""){
			$_GET["p"]=$camposFiltro[0];
		}
		if($camposFiltro[1]!=""){
			$_GET["ae"]=$camposFiltro[1];
		}
		if($camposFiltro[2]!=""){
			$_GET["so"]=$camposFiltro[2];
		}
		if($camposFiltro[3]!=""){
			$_GET["ta"]=$camposFiltro[3];
		}
		if($camposFiltro[4]!=""){
			$_GET["c"]=$camposFiltro[4];
		}
		if($camposFiltro[5]!=""){
			$_GET["ci"]=$camposFiltro[5];
		}
		if($camposFiltro[6]!=""){
			$_GET["t"]=$camposFiltro[6];
		}
		if($camposFiltro[7]!=""){
			$_GET["w"]=$camposFiltro[7];
		}
	}
*/

	/**
	 * Información del buscador
	 */
	$descripcionBuscador = "";
	$urlBusqueda = "";
	$idPais = 0;
	$ciudad = "";
	$idActividadProfesional = 0;
	$wholeWord = 0;
        $sourceLanguage="";
        $targetLanguage="";
        $areaExpertise="";
	$filtro="#p_#ae_#so_#ta_#c_#ci_#t_#w";
	$esPais=false;
	$esDescripcion=false;
	$esActividad=false;
	$esPalabraConcreta=false;
	$esCiudad=false;
        $esSourceLang=false;
        $esTargetLang=false;
        $esArea=false;

//if (isset($_GET["p"]) || isset($_GET["ae"]) || isset($_GET["so"]) || isset($_GET["ta"]) || isset($_GET["c"]) || isset($_GET["ci"]) || isset($_GET["t"]) || isset($_GET["w"])) {
        //Professional activity
        if (isset($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"] > 0) {
            $idActividadProfesional = $_GET["p"];
            $urlBusqueda .= "&p=" . $_GET["p"];
            $esActividad = true;
        }

        //Area of expertise
        if (isset($_GET["ae"])) {
            $areaExpertise = $_GET["ae"];
            $urlBusqueda .= "&ae=" . $_GET["ae"];
            $esArea = true;
        }

        //Source language
        if (isset($_GET["so"])) {
            $sourceLanguage = $_GET["so"];
            $urlBusqueda .= "&so=" . $_GET["so"];
            $esSourceLang = true;
        }

        //Target language
        if (isset($_GET["ta"])) {
            $targetLanguage = $_GET["ta"];
            $urlBusqueda .= "&ta=" . $_GET["ta"];
            $esTargetLang = true;
        }

        //Country
        if (isset($_GET["c"]) && is_numeric($_GET["c"]) && $_GET["c"] > 0) {
            $idPais = $_GET["c"];
            $urlBusqueda .= "&c=" . $_GET["c"];
            $esPais = true;
        }

        //City
        if (isset($_GET["ci"]) && trim($_GET["ci"]) != "" && $_GET["ci"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_CITY) {
            $ciudad = generalUtils::escaparCadena($_GET["ci"]);
            $subPlantilla->assign("CITY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["ci"])));
            if (trim($_GET["ci"]) != "") {
                $urlBusqueda .= "&ci=" . $_GET["ci"];
                $esCiudad = true;
            }
        }/*else{
		$subPlantilla->assign("CITY_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_CITY);
	}*/

        //Free text
        if (isset($_GET["t"]) && trim($_GET["t"]) != "" && $_GET["t"] != STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TEXT_SEARCH) {
            $descripcionBuscador = generalUtils::escaparCadena($_GET["t"]);
            $subPlantilla->assign("QUERY_VALUE", generalUtils::reeamplazarAntiBarras(htmlspecialchars($_GET["t"])));
            if (trim($_GET["t"]) != "") {
                $urlBusqueda .= "&t=" . $_GET["t"];
                $esDescripcion = true;
            }
        }/*else{
		$subPlantilla->assign("QUERY_VALUE", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TEXT_SEARCH);
	}*/

        //Whole word only
        if (isset($_GET["w"]) && $_GET["w"] == "on") {
            $wholeWord = 1;
            $urlBusqueda .= "&w=1";
            $esPalabraConcreta = true;
            $subPlantilla->assign("CHECKED_WHOLE_WORD", "checked");

        }

        //Build search filter
        if ($esActividad || $esArea || $esSourceLang || $esTargetLang || $esPais || $esCiudad || $esDescripcion || $esPalabraConcreta) {
            $filtro = str_replace("#p", $_GET["p"], $filtro);
            $filtro = str_replace("#ae", $areaExpertise, $filtro);
            $filtro = str_replace("#so", $sourceLanguage, $filtro);
            $filtro = str_replace("#ta", $targetLanguage, $filtro);
            $filtro = str_replace("#c", $_GET["c"], $filtro);
            $filtro = str_replace("#ci", $ciudad, $filtro);
            $filtro = str_replace("#t", $descripcionBuscador, $filtro);
            $filtro = str_replace("#w", $wholeWord, $filtro);
        } else {
            $filtro = "";
        }

        //Obtenemos la url asociada a members directory search
        $resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
        $datoMenuSeo = $db->getData($resultadoMenuSeo);
        $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
        $vectorAtributosMenu["id_menu"] = $idMenu;
        $vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
        $urlActualAux = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);

        $subPlantilla->assign("CONTENIDO_DESCRIPCION", $datoMenuSeo["descripcion"]);

if ($filtro != "") {

        //Listamos los miembros del directorio
        //if($descripcionBuscador != "" || $idPais > 0 || $idActividadProfesional > 0 || $ciudad!="") {
        $codeProcedure = "CALL ed_sp_web_usuario_web_obtener_listado(" . $idMenuTipo . "," . $idActividadProfesional . ",'" . $areaExpertise . "','" . $sourceLanguage . "','" . $targetLanguage . "'," . $idPais . ",'" . $ciudad . "','" . $descripcionBuscador . "'," . $wholeWord . ",";
        $totalItemsPagina = 300;
        //$totalItemsPagina = 2;
        $totalPaginasMostrar = 8;

        //if ($filtro != "") {
            $urlBusqueda = "-" . $filtro;
        //}


        $urlActual = $urlActualAux . "-";
        //$urlActual = "directory_list.php?menu=".$idMenu.$urlBusqueda."&";
        require "includes/load_paginator.php";

        $resultMiembros = $db->callProcedure($codeProcedure);

        //$cont = 0;
        if ($db->getNumberRows($resultMiembros) > 0) {
            while ($dataMiembro = $db->getData($resultMiembros)) {
                $imagen = "files/members/thumb/" . (($dataMiembro["imagen"] == "") ? "default.jpg" : $dataMiembro["imagen"]);
                $subPlantilla->assign("ITEM_MIEMBRO_NOMBRE", $dataMiembro["nombre"] . (($dataMiembro["apellidos"] == "") ? "" : " " . $dataMiembro["apellidos"]));
                $subPlantilla->assign("ITEM_MIEMBRO_IMAGEN", $imagen);

                if ($dataMiembro["correo_electronico"] != "") {
                    $plainEmail = $dataMiembro["correo_electronico"];
                    $hiddenEmail = hideEmail($plainEmail);
                    $subPlantilla->assign("ITEM_MIEMBRO_MAIL", $hiddenEmail);
                    $subPlantilla->parse("contenido_principal.listado_miembros.item_miembro.mail");
                }

                if ($dataMiembro["web"] != "") {
                    $subPlantilla->assign("ITEM_MIEMBRO_WEB", generalUtils::agregarProtocoloUrl($dataMiembro["web"]));
                    $subPlantilla->assign("ITEM_MIEMBRO_WEB_CORTAR", substr($dataMiembro["web"], 0, 25));
                    $subPlantilla->parse("contenido_principal.listado_miembros.item_miembro.web");
                }


                //Url detalle
                $vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
                $vectorAtributosDetalle["id_menu"] = $_GET["menu"];
                $vectorAtributosDetalle["id_detalle"] = $dataMiembro["id_usuario_web"];
                $vectorAtributosDetalle["seo_url"] = $dataMiembro["nombre"] . " " . $dataMiembro["apellidos"];
                $subPlantilla->assign("ITEM_MIEMBRO_URL", generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle));


                //$subPlantilla->assign("ITEM_MIEMBRO_URL", "directory_detail.php?menu=".$idMenu."&member=".$dataMiembro["id_usuario_web"]);

                $subPlantilla->parse("contenido_principal.listado_miembros.item_miembro");
                /*
                                ++$cont;
                                if($cont % 2 == 0) {
                                    $subPlantilla->parse("contenido_principal.listado_miembros.tr");
                                }
                            }
                            if($cont % 2 != 0) {
                                $subPlantilla->parse("contenido_principal.listado_miembros.tr");
                            }
                */
            }
            $subPlantilla->parse("contenido_principal.listado_miembros");
        } else {
            $subPlantilla->parse("contenido_principal.no_miembros");
        }

    }
	
	$subPlantilla->assign("MENU_ID", $idMenu);
	
	//Professional activities dropdown
	$subPlantilla->assign("COMBO_ACTIVIDAD_PROFESIONAL", generalUtils::construirCombo($db, "CALL ed_sp_web_usuario_web_actividad_profesional_obtener_combo(".$_SESSION["id_idioma"].")", "p", "cmbActividadProfesional", $idActividadProfesional, "descripcion", "id_actividad_profesional", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_PROFESSIONAL_ACTIVITY, 0, "class='form-control'"));
	
	//Areas of expertise dropdown   
    $subPlantilla->assign("COMBO_AREAS_OF_EXPERTISE", generalUtils::construirCombo($db, "CALL ed_sp_get_areas_of_expertise(".$_SESSION["id_idioma"].")", "ae", "cmbAreas", $areaExpertise, "nombre", "id_area", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_AREAS_OF_EXPERTISE, 0, "class='form-control'"));

	//Source languages dropdown   
    $subPlantilla->assign("COMBO_SOURCE_LANGUAGES", generalUtils::construirCombo($db, "CALL ed_sp_get_source_languages(".$idMenuTipo.",".$_SESSION["id_idioma"].")", "so", "cmbSrc", $sourceLanguage, "nombre", "id_working_language", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_SOURCE_LANGUAGE, 0, "class='form-control'"));

	//Target languages dropdown   
    $subPlantilla->assign("COMBO_TARGET_LANGUAGES", generalUtils::construirCombo($db, "CALL ed_sp_get_target_languages(".$idMenuTipo.",".$_SESSION["id_idioma"].")", "ta", "cmbTgt", $targetLanguage, "nombre", "id_working_language", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_TARGET_LANGUAGE, 0, "class='form-control'"));

	//Countries dropdown
	$subPlantilla->assign("COMBO_PAISES", generalUtils::construirCombo($db, "CALL ed_sp_web_usuario_web_pais_obtener_combo(".$idMenuTipo.")", "c", "cmbPais", $idPais, "nombre_original", "id_pais", STATIC_DIRECTORY_MEMBER_DATABASE_SEARCH_COUNTRY, 0, "class='form-control' autocomplete='country-name'"));
	
        
    //Cargamos el breadcrumb
	require "includes/load_breadcrumb.inc.php";
	
	//Cargamos los menus hijos del lateral derecho
	require "includes/load_menu_left.inc.php";
		
	//Cargamos el slider en caso de que tenga imagenes
	require "includes/load_slider.inc.php";
	
	$subPlantilla->parse("contenido_principal");
	
	/**
	 * Realizamos todos los parse relacionados con este apartado
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