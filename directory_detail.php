<?php
	/**
	 * 
	 * Pagina de inicio desde donde se inicia el portal
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */

	require "includes/load_main_components.inc.php";

 header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
 header('Cache-Control: no-store, no-cache, must-revalidate');
 header('Cache-Control: post-check=0, pre-check=0', FALSE);
 header('Pragma: no-cache');
	
	// Instanciamos la clase Xtemplate con la plantilla base
	$plantilla = new XTemplate("html/index.html");
	
	// Instanciamos la clase Xtemplate con la plantilla que va a contener la información del apartado
	$subPlantilla = new XTemplate("html/directory_detail.html");
	
	/**
	 * Asignamos el CSS que corresponde a este apartado
	 */
	$plantilla->assign("SECTION_FILE_CSS", "directory_detail.css");
	
	if(isset($_GET["elemento"])){
		$_GET["member"]=$_GET["elemento"];
	}
	
	require "includes/load_structure.inc.php";
	
	
	//Comprobamos si existe el miembro y presentamos su información
	if(isset($_GET["member"]) && is_numeric($_GET["member"])) {
		$resultMiembro = $db->callProcedure("CALL ed_sp_web_usuario_web_obtener(".$idMenuTipo.",".$_GET["member"].")");
		if($db->getNumberRows($resultMiembro) > 0) {
			$dataMiembro = $db->getData($resultMiembro);

// Added by Stephen on 16 Jan 2016 in order to include Country in profile
$idPais = $dataMiembro["id_pais"];
$resultPais = $db->callProcedure("CALL ed_pr_get_country_name($idPais)");
$dataPais = $db->getData($resultPais);
if($dataPais["nombre_original"] != "") {
$subPlantilla->assign("ITEM_MIEMBRO_PAIS", $dataPais["nombre_original"] . "<br />");
$subPlantilla->parse("contenido_principal.pais");
}

				$nombreMiembro = $dataMiembro["nombre"].(($dataMiembro["apellidos"] == "") ? "" : " ".$dataMiembro["apellidos"]);
				$imagen = "files/members/".(($dataMiembro["imagen"] == "") ? "default.jpg" : $dataMiembro["imagen"]);
				$subPlantilla->assign("ITEM_MIEMBRO_NOMBRE", $nombreMiembro);
				$subPlantilla->assign("ITEM_MIEMBRO_IMAGEN", $imagen);
				
				if($dataMiembro["telefono_casa"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_TELEFONO1", $dataMiembro["telefono_casa"]);
					$subPlantilla->parse("contenido_principal.telefono.telefono1");
					if($dataMiembro["telefono2"] == "" && $dataMiembro["fax"] == "") {
						$subPlantilla->parse("contenido_principal.telefono");
					}
				}
				
				if($dataMiembro["telefono_trabajo"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_TELEFONO2", $dataMiembro["telefono_trabajo"]);
					$subPlantilla->parse("contenido_principal.telefono.telefono2");
					if($dataMiembro["telefono_casa"] == "" && $dataMiembro["fax"] == "") {
						$subPlantilla->parse("contenido_principal.telefono");
					}
				}
				
				if($dataMiembro["fax"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_FAX", $dataMiembro["fax"]);
					$subPlantilla->parse("contenido_principal.telefono.fax");
					$subPlantilla->parse("contenido_principal.telefono");
				}
				
				if($dataMiembro["correo_electronico"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_MAIL", $dataMiembro["correo_electronico"]);
					$subPlantilla->parse("contenido_principal.mail.mail1");
					if($dataMiembro["correo_electronico_alternativo"] == "") {
						$subPlantilla->parse("contenido_principal.mail");
					}
				}
				
				if($dataMiembro["correo_electronico_alternativo"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_MAIL", $dataMiembro["correo_electronico_alternativo"]);
					$subPlantilla->parse("contenido_principal.mail.mail2");
					$subPlantilla->parse("contenido_principal.mail");
				}
				
				if($dataMiembro["web"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_WEB", generalUtils::agregarProtocoloUrl($dataMiembro["web"]));
                    $subPlantilla->parse("contenido_principal.web");
				}
				
				if($dataMiembro["ciudad"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_CIUDAD", $dataMiembro["ciudad"] . "<br />");
					$subPlantilla->parse("contenido_principal.ciudad");
				}
				
				if($dataMiembro["provincia"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_PROVINCIA", $dataMiembro["provincia"] . "<br />");
					$subPlantilla->parse("contenido_principal.provincia");
				}
				
				if($dataMiembro["codigo_postal"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_CODIGO_POSTAL", $dataMiembro["codigo_postal"]. "<br />");
					$subPlantilla->parse("contenido_principal.codigo_postal");
				}
				
				if($dataMiembro["direccion2"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_DIRECCION2", $dataMiembro["direccion2"]);
					$subPlantilla->parse("contenido_principal.direccion.direccion2");
				}
				
				if($dataMiembro["direccion"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_DIRECCION", $dataMiembro["direccion"]);
					$subPlantilla->parse("contenido_principal.direccion");
				}
				
	
				
				if($dataMiembro["descripcion"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_DESCRIPCION", $dataMiembro["descripcion"]);
					$subPlantilla->parse("contenido_principal.descripcion");
				}
				
				if($dataMiembro["otros"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_OTROS", $dataMiembro["otros"]);
					$subPlantilla->parse("contenido_principal.otros");
				}
				
				if($dataMiembro["publicaciones"] != "") {
					$subPlantilla->assign("ITEM_MIEMBRO_PUBLICACION", $dataMiembro["publicaciones"]);
					$subPlantilla->parse("contenido_principal.publicacion");
				}
				

				//Get member's professional activities (standard and "Other") from database
				$resultActividadesProfesionales = $db->callProcedure("CALL ed_sp_web_usuario_web_actividad_profesional_obtener_listado(".$_GET["member"].",".$_SESSION["id_idioma"].")");
				// if list of activities is not empty, initiate variables
				if($db->getNumberRows($resultActividadesProfesionales) > 0) {
					$txtActividadProfesional = "";
					$actividadOther = "";
					// for each activity in the list
                    while($dataActividadProfesional = $db->getData($resultActividadesProfesionales)) {
						// provided there is a member ID
					    if($dataActividadProfesional["id_usuario_web"] != "") {
							// if the activity is not "Other" (i.e. activity ID 8)
					        if($dataActividadProfesional["id_actividad_profesional"]!=8){
								// assign activity description to list of standard activities
					            $txtActividadProfesional .= $dataActividadProfesional["descripcion"];
							}else{
								// otherwise assign activity description to "Other activities" variable
					            $actividadOther=$dataActividadProfesional["descripcion"];
							}
							// if activity name is not empty and activity ID is not 8 (i.e. "Other")
							if($dataActividadProfesional["actividad_profesional"] != "" && $dataActividadProfesional["id_actividad_profesional"]!=8) {
								// assign activity name to the list of activities followed by a line break
					            $txtActividadProfesional .= $dataActividadProfesional["actividad_profesional"];
							}
							// if activity ID is not 8 (i.e. "Other")
							if($dataActividadProfesional["id_actividad_profesional"]!=8){
								//$txtActividadProfesional .= ", ";
                                $txtActividadProfesional .= "<br />    ";
							}
						}
					}
					if($actividadOther==""){
						$txtActividadProfesional=substr($txtActividadProfesional, 0, -2);
					}
					$txtActividadProfesional.=$actividadOther;

	
					$subPlantilla->assign("ITEM_MIEMBRO_ACTIVIDAD_PROFESIONAL", $txtActividadProfesional);
				}

        //Get language pairs of this user
        $resultSourceLanguages = $db->callProcedure("CALL ed_sp_get_member_profile_source_languages(" . $_SESSION["id_idioma"] . "," . $_GET["member"] . ")");
        $resultTargetLanguages = $db->callProcedure("CALL ed_sp_get_member_profile_target_languages(" . $_SESSION["id_idioma"] . "," . $_GET["member"] . ")");

        if ($db->getNumberRows($resultSourceLanguages) > 0 && $db->getNumberRows($resultTargetLanguages) > 0) {
            //Initialize variables for later use...
            $targetLanguages = [];
            $txtLanguagePairs = "";

//Retrieve all target languages in an array to loop through multiple times
            while ($dataTargetLanguages = $db->getData($resultTargetLanguages)) {
                $targetLanguages[] = $dataTargetLanguages;
            }

//Retrieve and loop through all source languages
            while ($dataSourceLanguages = $db->getData($resultSourceLanguages)) {
//As we have $targetLanguages as an array, this will always loop though all target langs
                foreach ($targetLanguages as $targetLanguage) {
                    $txtLanguagePairs .= $dataSourceLanguages["nombre"] . " > " . $targetLanguage["nombre"] . "<br />";
                }
            }
/* IN REVERSE ORDER
            $sourceLanguages = [];
            $txtLanguagePairs = "";

//Retrieve all source languages in an array to loop through multiple times
            while ($dataSourceLanguage = $db->getData($resultSourceLanguages)) {
                $sourceLanguages[] = $dataSourceLanguage;
            }

//Retrieve and loop through all target languages
            while ($dataTargetLanguage = $db->getData($resultTargetLanguages)) {
//As we have $sourceLanguages as an array, this will always loop though all source langs
                foreach ($sourceLanguages as $sourceLanguage) {
                    $txtLanguagePairs .= $sourceLanguage["nombre"] . " > " . $dataTargetLanguage['nombre'] . "<br />";
                }
            }
*/

            $subPlantilla->assign("ITEM_MIEMBRO_LANGUAGE_PAIRS", $txtLanguagePairs);
            $subPlantilla->parse("contenido_principal.language_pairs");
        }

                //Get areas of expertise of this user
        $resultAreas = $db->callProcedure("CALL ed_sp_get_member_profile_areas_of_expertise(" . $_SESSION["id_idioma"] . "," . $_GET["member"] . ")");
        if ($db->getNumberRows($resultAreas) > 0) {
            //Initialize variables for later use...
            $areas = [];
            $txtAreas = "";
//Retrieve all areas in an array
            while ($dataAreas = $db->getData($resultAreas)) {
                    $txtAreas .= $dataAreas["nombre"] . "<br />";
            }
            $subPlantilla->assign("ITEM_MIEMBRO_AREAS_OF_EXPERTISE", $txtAreas);
            $subPlantilla->parse("contenido_principal.areas_of_expertise");
        }

				//Conferencias de este usuario
				$resultadoConferencia=$db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_usuario_web_obtener(".$_GET["member"].",".$_SESSION["id_idioma"].")");
				$i=0;
				$txtConferenciaProfesional = "";
				while($datoConferencia=$db->getData($resultadoConferencia)){
					if($datoConferencia["enlace"]!=""){
						$txtConferenciaProfesional .= "<a href='".$datoConferencia["enlace"]."'>".$datoConferencia["nombre"]."</a>";
						//$subPlantilla->assign("CONFERENCIA_NOMBRE","<a href='".$datoConferencia["enlace"]."'>".$datoConferencia["nombre"]."</a>");
					}else{
						$txtConferenciaProfesional.=$datoConferencia["nombre"];
						//$subPlantilla->assign("CONFERENCIA_NOMBRE",$datoConferencia["nombre"]);
					}
					$txtConferenciaProfesional .= ", ";
					//$subPlantilla->parse("contenido_principal.conferencias.item_conferencia");
					$i++;
				}
	
				if($i>0){
					$subPlantilla->assign("ITEM_MIEMBRO_CONFERENCIA", substr($txtConferenciaProfesional, 0, -2));
					$subPlantilla->parse("contenido_principal.conferencias");
				}
				
				//Talleres de este usuario
				$resultadoTaller=$db->callProcedure("CALL ed_sp_web_inscripcion_taller_usuario_web_taller_obtener(".$_GET["member"].",".$_SESSION["id_idioma"].")");
				$j=0;
				while($datoTaller=$db->getData($resultadoTaller)){
					if($datoTaller["enlace"]!=""){
						$subPlantilla->assign("ITEM_MIEMBRO_TALLER","<a href='".$datoTaller["enlace"]."'>".$datoTaller["nombre_largo"]."</a>");
					}else{
						$subPlantilla->assign("ITEM_MIEMBRO_TALLER",$datoTaller["nombre_largo"]);
					}
					
					$subPlantilla->parse("contenido_principal.talleres.item_taller");
					$j++;
				}
	
				if($j>0){
					$subPlantilla->parse("contenido_principal.talleres");
				}
				
				if($i>0 || $j>0){
					$subPlantilla->parse("contenido_principal.bloque_formacion_continua");
				}
				
				
				/**** INICIO: breadcrumb ****/
				$vectorAtributosDetalle["idioma"] = $_SESSION["siglas"];
				$vectorAtributosDetalle["id_menu"] = $idMenu;
				$vectorAtributosDetalle["id_detalle"] = $dataMiembro["id_usuario_web"];
				$vectorAtributosDetalle["seo_url"] = $nombreMiembro;
				$breadCrumbUrlDetalle = generalUtils::generarUrlAmigableDetalle($vectorAtributosDetalle);
				$breadCrumbDescripcionDetalle = $nombreMiembro;
				/**** FINAL: breadcrumb ****/
		}else{
			generalUtils::redirigir(CURRENT_DOMAIN);
		}
	}else{
		generalUtils::redirigir(CURRENT_DOMAIN);
	}
	
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