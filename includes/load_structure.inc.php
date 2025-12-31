<?php
/*
 * Code that runs every time a page ("id_menu") is loaded
 * Checks login status and loads login panel, top menu, lefthand menu and sidebar buttons
 * $idMenu is either the ID included in URL ($GET["menu"]) if set and numeric, otherwise it defaults to 1
 */


$idMenu = (isset($_GET["menu"]) && is_numeric($_GET["menu"])) ? $_GET["menu"] : 1;
$plantilla->assign("URL_ACTUAL", "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

//Hash static files
$hashScript = date("Ymd") + 1;
$plantilla->assign("HASH", "?v=" . $hashScript);

//Get id_menu, name, title and description of home page
$i = 0;
$resultadoHome = $db->callProcedure("CALL ed_sp_web_menu_home_obtener(" . $_SESSION["id_idioma"] . ")");
$datoHome = $db->getData($resultadoHome);
$idMenuHome = $datoHome["id_menu"];
$inscripcionActiva = true;

//Assign "Remember password" link to placeholder (main template is index.html)
$plantilla->assign("URL_REMEMBER_PASSWORD", "remember_password");

if (isset($esHome) && isset($_SESSION["registration_desk"])) {
    generalUtils::redirigir("https://www.metmeetings.org/en/registration:1408");
}

//If we have come here from index.php, set $idMenu to Home page (in index.php, $esHome = true)
if (isset($esHome)) {
    $idMenu = $idMenuHome;
}

//Set page-specific or default Open Graph metatags (for social media)

    $plantilla->assign('OG_TITLE', 'Mediterranean Editors and Translators');
    $plantilla->assign('OG_TYPE', 'website');
    $plantilla->assign('OG_DESCRIPTION', STATIC_LEGAL_TEXT_FOOTER);
    $plantilla->assign('OG_URL', "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $plantilla->assign('OG_IMAGE', 'https://www.metmeetings.org/images/pictures/logos/MET_logo_color_SQUARE_256x256.png');
    $plantilla->assign('OG_IMAGE_TYPE', 'image/png');
    $plantilla->assign('OG_IMAGE_ALT', 'MET logo');

$resultMetatagsOg = $db->callProcedure("SELECT * FROM ed_tb_menu_metatag_og WHERE id_menu = $idMenu AND id_idioma = 3");
if ($resultMetatagsOg->num_rows) {
    while ($metatagOg = $db->getData($resultMetatagsOg)) {
        switch ($metatagOg['id_metatag_og']) {
            case 1:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_TITLE', $metatagOg['content']);
            }
                break;
            case 2:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_TYPE', $metatagOg['content']);
            }
                break;
            case 3:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_DESCRIPTION', $metatagOg['content']);
            }
                break;
            case 4:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_URL', $metatagOg['content']);
            }
                break;
            case 5:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_IMAGE', $metatagOg['content']);
            }
                break;
            case 6:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_IMAGE_TYPE', $metatagOg['content']);
            }
                break;
            case 7:
            if ($metatagOg['content']) {
                $plantilla->assign('OG_IMAGE_ALT', $metatagOg['content']);
            }
                break;
        }
    }
} 
$plantilla->parse("contenido_principal.metatags_open_graph");


/* NOT IMPLEMENTED: $idSeo is not set anywhere
if (isset($idSeo)) {
    //Get URL, module, description and title of current page
    $resultadoTitulo = $db->callProcedure("CALL ed_sp_web_seo_obtener_concreto(" . $idSeo . "," . $_SESSION["id_idioma"] . ")");
    $datoTitulo = $db->getData($resultadoTitulo);
    //Set page title
    if ($datoTitulo["title"] != "") {
        $plantilla->assign("TITULO_WEB", $datoTitulo["title"]);
        $plantilla->assign("ITEM_MENU_MOBIL_TITULO", $datoTitulo["title"]);
        $plantilla->parse("contenido_principal.item_menu_mobil");

    }
    //Set canonical URL
    if ($datoTitulo["canonical"] != "") {
        $plantilla->assign("METATAG_OPCIONAL_CANONICAL", $datoTitulo["canonical"]);
        $plantilla->parse("contenido_principal.metatags_opcionales.metatag_canonical");
        $plantilla->parse("contenido_principal.metatags_opcionales");

    }


    //Set page-specific metatags
    $resultadoMetaTag = $db->callProcedure("CALL ed_sp_web_seo_metatag_obtener(" . $idSeo . "," . $_SESSION["id_idioma"] . ")");
    while ($datoMetaTag = $db->getData($resultadoMetaTag)) {
        $plantilla->assign("METATAG_TITULO", $datoMetaTag["nombre"]);
        $plantilla->assign("METATAG_DESCRIPCION", $datoMetaTag["descripcion"]);

        $plantilla->parse("contenido_principal.metatag_concreto");
    }

    //$esSeoTag is not set anywhere
    if (!$esSeoTag) {
        //Get SEO tag
        $resultadoSeoTag = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
        $datoSeoTag = $db->getData($resultadoSeoTag);

        $plantilla->assign("SEO_TAG", $datoSeoTag["seo_tag"]);
    }
}
*/


/*
 * Get user details and membership status (paid-up, pending, expired, etc.) and construct login panel
 * If user is logged in…
 */
if (isset($_SESSION["met_user"])) {
    //Store page access level (user type: council, editor, member) in a variable
    $idMenuTipo = $_SESSION["met_user"]["tipoUsuario"];

    /*
     * Como existe la opción de que renovemos la subscripcion ya sea por paypal y por transferencia debemos tener actualizado las variables de sesion asociadas al login, cabe tener en cuenta que la razon principal por la cual se hace esto, es porque si tu renuevas con paypal, en el fichero de confirmacion de pago, no podemos modificar variables de sesion
    *
    */
    //Get the logged-in user's most recent membership registration data (paid/unpaid, date and expiry)
    $pagado = -1;
    $resultadoUltimaInscripcion = $db->callProcedure("CALL ed_sp_web_usuario_web_ultima_inscripcion_obtener(" . $_SESSION["met_user"]["id"] . "," . $pagado . ")");
    $datoUltimaInscripcion = $db->getData($resultadoUltimaInscripcion);


    //Update the session variables with these data
    $_SESSION["met_user"]["pagado"] = $datoUltimaInscripcion["pagado"];
    $_SESSION["met_user"]["fecha_inscripcion"] = $datoUltimaInscripcion["fecha_inscripcion"];
    $_SESSION["met_user"]["fecha_finalizacion"] = $datoUltimaInscripcion["fecha_finalizacion"];

    $inscripcionActiva = false;

    //If most recent registration is unpaid, check for earlier but still unexpired registration that *is* paid
    if ($datoUltimaInscripcion["pagado"] == 0) {
        $pagado = 1;
        $resultadoUltimaInscripcion = $db->callProcedure("CALL ed_sp_web_usuario_web_ultima_inscripcion_obtener(" . $_SESSION["met_user"]["id"] . "," . $pagado . ")");
        $datoUltimaInscripcion = $db->getData($resultadoUltimaInscripcion);

        //If last paid registration is not expired, allow user to continue as logged-in member
        if (!generalUtils::esMiembroCaducado($datoUltimaInscripcion["fecha_finalizacion"])) {
            $_SESSION["met_user"]["fecha_finalizacion"] = $datoUltimaInscripcion["fecha_finalizacion"];
            $inscripcionActiva = true;
        }
    }

    //If user is a paid-up member, assign expiry date to placeholder
    if ($_SESSION["met_user"]["pagado"] == 1 || $inscripcionActiva) {
      
        $plantilla->assign("MIEMBRO_FECHA_HASTA", generalUtils::conversionFechaFormato($_SESSION["met_user"]["fecha_finalizacion"]));

	    //If member is expired, display "Expired" message and link to renewal form
        if (generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"])) {
            $plantilla->assign("EXPIRED_ACCOUNT", STATIC_EXPIRED_ACCOUNT);

            //If user is an individual member, get id_menu of renewal form
            if ($_SESSION["met_user"]["institution_id"] == "") {
                //Obtenemos el menu asociado al renew membership
                $resultadoMenuFormulario = $db->callProcedure("CALL ed_sp_web_menu_formulario_obtener(" . FORM_TYPE_RENEW_MEMBERSHIP_ID . ")");
                $datoMenuFormulario = $db->getData($resultadoMenuFormulario);
                $idMenuFormulario = $datoMenuFormulario["id_menu"];


                //Get URL of renewal form and assign to "Renew" link
                $resultadoMenuSeo = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenuFormulario . "," . $_SESSION["id_idioma"] . ")");
                $datoMenuSeo = $db->getData($resultadoMenuSeo);
                $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
                $vectorAtributosMenu["id_menu"] = $idMenuFormulario;
                $vectorAtributosMenu["seo_url"] = $datoMenuSeo["seo_url"];
                $urlActualRenew = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
                $plantilla->assign("URL_PROFILE_RENEW_MEMBERSHIP", $urlActualRenew);
                $plantilla->parse("contenido_principal.datos_usuario.bloque_miembro_hasta.bloque_renew");
            }

            //If member is not expired, insert "Edit profile" link
        } else {
            $plantilla->parse("contenido_principal.datos_usuario.bloque_perfil");
        }
        //For non-expired members, display expiry date
        $plantilla->parse("contenido_principal.datos_usuario.bloque_miembro_hasta");
    } else {
        //If registration is current but not paid, display "Payment pending" link
        $plantilla->parse("contenido_principal.datos_usuario.bloque_pago_pendiente");
    }

    //Display user panel with username, Edit profile link, Log out link and other user data
    $plantilla->assign("PANEL_LOGIN_USERNAME", $_SESSION["met_user"]["username"]);
    $plantilla->assign("MEMBERSHIP_NUMBER_TEXT", "Membership no.");
    $plantilla->assign("MEMBERSHIP_NUMBER_NUMBER", sprintf('%05d', $_SESSION["met_user"]["id"]));
    $plantilla->assign("EDIT_MY_PROFILE", "Edit profile");
    $plantilla->assign("LOGOUT", "Sign out");
    $plantilla->parse("contenido_principal.datos_usuario");

    /*
     * If user is not logged in…
     */
} else {
    // Set access level (user type) to "non-member" (-1)
    $idMenuTipo = -1;
  
  if (isset($_SESSION["registration_desk"])) {
    $plantilla->assign("PANEL_LOGIN_USERNAME", "METM REGISTRATION STAFF");
    $plantilla->assign("LOGOUT", "Sign out");
    $plantilla->parse("contenido_principal.datos_usuario");
  }elseif (isset($_SESSION["conference_user"])) {
    $plantilla->assign("PANEL_LOGIN_USERNAME", "NON-MEMBER CONFERENCE ATTENDEE");
    $plantilla->assign("LOGOUT", "Sign out");
    $plantilla->parse("contenido_principal.datos_usuario");
  }else{
    $plantilla->assign("PANEL_LOGIN_USERNAME", "Sign in");
    // If session variable "loginErrorMessage" is set (from authentication.php redirect), display message
    if (isset($_SESSION['loginErrorMessage'])) {
        $plantilla->assign("LOGIN_ERROR_MENSAJE", $_SESSION['loginErrorMessage']);
        $plantilla->assign("LOGIN_DISPLAY", "display:block;");
        // unset the session variable before continuing
        unset($_SESSION['loginErrorMessage']);
        // Otherwise display login panel as normal
    } else {
        $plantilla->assign("LOGIN_DISPLAY", "display:none;");
    }
    $plantilla->parse("contenido_principal.validate_login");
    $plantilla->parse("contenido_principal.panel_login");
  }
}

/*
 * REDIRECTS
 */
$resultadoMenuConcreto = $db->callProcedure("CALL ed_sp_web_menu_obtener_concreto(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
//If requested page does not exist, redirect to home page
if ($db->getNumberRows($resultadoMenuConcreto) == 0) {
    generalUtils::redirigir(CURRENT_DOMAIN);
//If page exists, get page details (user type and form ID)
} else {
    $datoMenuConcreto = $db->getData($resultadoMenuConcreto);
  
  	$settingsResultado=$db->callProcedure("CALL ed_pr_get_settings()");
	$settingsDato=$db->getData($settingsResultado);

    if (strpos($datoMenuConcreto['descripcion'], 'redirect-me-to=') === 0) {
        list($redirectURI) = explode('<', $datoMenuConcreto['descripcion']);
        $redirectURI = str_replace('redirect-me-to=','',$redirectURI);
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/" . $redirectURI);
    }	
  
    // Redirect new member and renewal forms if settings dictate
  
    if (($idMenu == 9 || $idMenu == 44) && $settingsDato['membership_forms']) {
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/membership-form-unavailable:1237");
    }	
	if ($idMenu == 1165 && $settingsDato['membership_forms'] == 2) {
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/membership-form-unavailable:1237");
    }
  
    //If user is NOT logged in…
    if (!isset($_SESSION["met_user"])) {
      
      if (!isset($_SESSION["stripe_form"])) {
        //if page requires login, redirect to "Access Denied" page
        if ($datoMenuConcreto["id_tipo_usuario_web"] != "") {
            $_SESSION["auth_target"] = $_SERVER["REQUEST_URI"];
            generalUtils::redirigir(CURRENT_DOMAIN . "/en/access-denied:1343");
        }
      }
        //if page is METM registration form, redirect to "Are you a member?" page
        if ($idMenu == 953 && !isset($_SESSION["met_user"])) {
            generalUtils::redirigir(CURRENT_DOMAIN . "/en/are-you-a-member:955");
        }
        //if page is Discounts & subscriptions, redirect to "Access Denied" page
        // For multiple pages: (($idMenu == 100 || $idMenu == 10 || $idMenu == 1) && !isset($_SESSION["met_user"]))
        // if ($idMenu == 17 && !isset($_SESSION["met_user"])) {
        //     $_SESSION["auth_target"] = $_SERVER["REQUEST_URI"];
        //     generalUtils::redirigir(CURRENT_DOMAIN . "/en/access-denied:1343");
        // }

        //If user IS logged in…
    } else {
        //If page is EDITOR ONLY and user is ordinary member (not admin, council or editor), redirect to home page
        if ($datoMenuConcreto["id_tipo_usuario_web"] == TIPO_USUARIO_EDITOR && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_ADMIN && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_CONSEJO && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_EDITOR) {
            generalUtils::redirigir(CURRENT_DOMAIN);
        }
        //if page is COUNCIL ONLY and user is ordinary member or editor (not council or admin), redirect to home page
        if ($datoMenuConcreto["id_tipo_usuario_web"] == TIPO_USUARIO_CONSEJO && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_ADMIN && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_CONSEJO) {
            generalUtils::redirigir(CURRENT_DOMAIN);
        }
        //if page is ADMIN ONLY and user is not admin, redirect to home page
        if ($datoMenuConcreto["id_tipo_usuario_web"] == TIPO_USUARIO_ADMIN && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_ADMIN) {
            generalUtils::redirigir(CURRENT_DOMAIN);
        }
		// if membership has lapsed and the page is not the renewal form, lapsed-member access-denied page, renewal form unavailable or payment confirmation page, redirect to lapsed-member access-denied page
		if (generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"]) && $idMenu != 1359 && $idMenu != 1165 && $idMenu != 1 && $idMenu != 1237) { 
			generalUtils::redirigir(CURRENT_DOMAIN . "/en/access-denied:1359");
		}
        //if page is NOT the membership renewal form and user is expired or pending, redirect to home page (I don't think this works)
 //       if ($datoMenuConcreto["id_tipo_usuario_web"] != "" && $datoMenuConcreto["id_formulario"] != 3 && !$inscripcionActiva && ($_SESSION["met_user"]["pagado"] == 0 || generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"]))) {
 //           generalUtils::redirigir(CURRENT_DOMAIN);
 //       }
        //if page IS the membership renewal form and user is a direct debit member, redirect to DD error pag
//        if ($datoMenuConcreto["id_formulario"] == 3 && $_SESSION["met_user"]["tipoPago"] == INSCRIPCION_TIPO_PAGO_DEBIT) {
//            generalUtils::redirigir(CURRENT_DOMAIN . "/en/direct-debit-error-message:821");
//        }
         // if user has NOT expired and page IS the membership renewal form and it is NOT October yet, redirect to too-early page
        if (!generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"]) && $datoMenuConcreto["id_formulario"] == 3 && date('m') < 10) {
            generalUtils::redirigir(CURRENT_DOMAIN . "/en/renew-membership:1400");
        }
        //if page is the "How to register" page, redirect to the METM registration form
        if ($idMenu == 953 && !isset($_SESSION["met_user"])) {
            generalUtils::redirigir(CURRENT_DOMAIN . "/en/are-you-a-member:955");
        }
    }
}


$totalGet = count($_GET);
if ($totalGet == 0 || ($totalGet == 1 && isset($_GET["idioma"]))) {
    $caracter = "?";
} else {
    $caracter = "&";
}

/**
 * Construct horizontal dropdown menu
 * First get top-level items
 * Then get dropdown items for each top-level item
 */
$resultMenuSuperior = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(null, " . $idMenuTipo . ", " . $_SESSION["id_idioma"] . ", 1)");
$cont = 0;
$totalMenus = $db->getNumberRows($resultMenuSuperior);
//Get the names and attributes of top-level menu items
while ($dataMenuSuperior = $db->getData($resultMenuSuperior)) {
    $plantilla->assign("ITEM_MENU_SUPERIOR_TITULO", $dataMenuSuperior["nombre"]);

    //Create array of top-level menu attributes
    $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
    $vectorAtributosMenu["id_menu"] = $dataMenuSuperior["id_menu"];
    $vectorAtributosMenu["seo_url"] = $dataMenuSuperior["seo_url"];
    $vectorAtributosMenu = generalUtils::generarUrlMenuContenido($db, $dataMenuSuperior["id_modulo"], $dataMenuSuperior["id_menu"], $dataMenuSuperior["descripcion"], $_SESSION["id_idioma"], $vectorAtributosMenu);

    //Generate URLs of top-level menu items
    $plantilla->assign("ITEM_MENU_SUPERIOR_URL", generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

    $plantilla->assign("ITEM_MENU_SUPERIOR_ACTIVO", ($idMenu == $dataMenuSuperior["id_menu"]) ? "current" : "");

    /**
     * Load dropdown submenu items
     * First, get list of submenu items
     */
    $resultMenuSuperiorDesplegable = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(" . $dataMenuSuperior["id_menu"] . ", " . $idMenuTipo . ", " . $_SESSION["id_idioma"] . ", 1)");
    if ($db->getNumberRows($resultMenuSuperiorDesplegable) > 0) {
        while ($dataMenuSuperiorDesplegable = $db->getData($resultMenuSuperiorDesplegable)) {
            $plantilla->assign("ITEM_MENU_SUPERIOR_DESPLEGABLE_TITULO", $dataMenuSuperiorDesplegable["nombre"]);
            $esMostrableSubMenu = true;

            //Hide membership renewal form menu item from direct debit members
//            if ($dataMenuSuperiorDesplegable["id_formulario"] == 3 && ($_SESSION["met_user"]["tipoPago"] == INSCRIPCION_TIPO_PAGO_DEBIT)) {
//                $esMostrableSubMenu = false;
//            }

            $plantilla->assign("ITEM_MENU_SUPERIOR_PRIVATE", ($dataMenuSuperior["id_tipo_usuario_web"] != "" && ($idMenuTipo == TIPO_USUARIO_ADMIN || $idMenuTipo == TIPO_USUARIO_CONSEJO || $idMenuTipo == TIPO_USUARIO_EDITOR) ? "specialMenu" : ""));

          	// Stop dropdown for Member area 1347
          if ($dataMenuSuperior["id_menu"] == 1347 ) {
          $plantilla->assign("TEST_ITEM_TEXT", "");
          } else {
          $plantilla->assign("TEST_ITEM_TEXT", "data-toggle='dropdown'");
          }
          
            if ($cont == 0) {
                $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "first");
            } elseif ($cont == $totalMenus - 1) {
                $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "last");
            } else {
                $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "");
            }

            //Create array of dropdown submenu attributes
            $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
            $vectorAtributosMenu["id_menu"] = $dataMenuSuperiorDesplegable["id_menu"];
            $vectorAtributosMenu["seo_url"] = $dataMenuSuperiorDesplegable["seo_url"];
            $vectorAtributosMenu = generalUtils::generarUrlMenuContenido($db, $dataMenuSuperiorDesplegable["id_modulo"], $dataMenuSuperiorDesplegable["id_menu"], $dataMenuSuperiorDesplegable["descripcion"], $_SESSION["id_idioma"], $vectorAtributosMenu);

            //Assign URLs of dropdown submenu items
            $plantilla->assign("ITEM_MENU_SUPERIOR_DESPLEGABLE_URL", generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

            //Assign CSS class "specialMenu" to non-public menu items
            $plantilla->assign("ITEM_MENU_SUPERIOR_DESPLEGABLE_PRIVATE", ($dataMenuSuperiorDesplegable["id_tipo_usuario_web"] != "" && ($idMenuTipo == TIPO_USUARIO_ADMIN || $idMenuTipo == TIPO_USUARIO_CONSEJO || $idMenuTipo == TIPO_USUARIO_EDITOR || $dataMenuSuperiorDesplegable["id_tipo_usuario_web"] == $idMenuTipo) ? "specialMenu" : ""));

            //If submenu item is not hidden, assign to template and display
            if ($esMostrableSubMenu) {
                $plantilla->parse("contenido_principal.item_menu_superior.menu_superior_desplegable.item_menu_superior_desplegable");
            }
        }
        $plantilla->parse("contenido_principal.item_menu_superior.menu_superior_desplegable");
    }

    $plantilla->parse("contenido_principal.item_menu_superior");
    $cont++;
}

/**
 * Construct secondary lefthand menu
 */
$resultMenuInferior = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(null, -1, " . $_SESSION["id_idioma"] . ", 4)");
$totalMenus = $db->getNumberRows($resultMenuInferior);
$cont = 1;
while ($dataMenuInferior = $db->getData($resultMenuInferior)) {
    $plantilla->assign("ITEM_MENU_INFERIOR_TITULO", $dataMenuInferior["nombre"]);

    $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
    $vectorAtributosMenu["id_menu"] = $dataMenuInferior["id_menu"];
    $vectorAtributosMenu["seo_url"] = $dataMenuInferior["seo_url"];
    $vectorAtributosMenu = generalUtils::generarUrlMenuContenido($db, $dataMenuInferior["id_modulo"], $dataMenuInferior["id_menu"], $dataMenuInferior["descripcion"], $_SESSION["id_idioma"], $vectorAtributosMenu);

    $plantilla->assign("ITEM_MENU_INFERIOR_URL", generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

    $plantilla->parse("contenido_principal.item_menu_inferior.separador");

    $cont++;
    $plantilla->parse("contenido_principal.item_menu_inferior");
}

/**
 * Load sidebar mini-banner ("botones acceso")
 */
$resultBotonesAcceso = $db->callProcedure("CALL ed_sp_web_boton_acceso_listado(" . $_SESSION["id_idioma"] . ")");
while ($dataBotonAcceso = $db->getData($resultBotonesAcceso)) {
    $plantilla->assign("ITEM_BOTON_ACCESO_IMAGEN", "files/sections/" . $dataBotonAcceso["imagen"]);
    $plantilla->assign("ITEM_BOTON_ACCESO_TARGET", ($dataBotonAcceso["es_remoto"] == 1) ? "_blank" : "_self");
    if ($dataBotonAcceso["url"] != "") {
        $plantilla->assign("ITEM_BOTON_ACCESO_URL", $dataBotonAcceso["url"]);
        $plantilla->parse("contenido_principal.item_boton_acceso.si_url");
    } else {
        $plantilla->parse("contenido_principal.item_boton_acceso.no_url");
    }

    $plantilla->parse("contenido_principal.item_boton_acceso");
}

/*
 * Assign random header image from the /files/backgrounds/active folder
 */
$background_images = glob('files/backgrounds/active/*.png');
$random_image_key = array_rand($background_images);

$plantilla->assign('RANDOM_IMAGE_URL', $background_images[$random_image_key]);

?>