<?php
/*
 * Code that runs every time a page ("id_menu") is loaded
 * Checks login status and loads login panel, top menu, lefthand menu and sidebar buttons
 * $idMenu is either the ID included in URL ($GET["menu"]) if set and numeric, otherwise it defaults to 1
 */
$idMenu = (isset($_GET["menu"]) && is_numeric($_GET["menu"])) ? $_GET["menu"] : 1;
$plantilla->assign("URL_ACTUAL", "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

//Hash static files
$hashScript = date("Ymd") + 1;
$plantilla->assign("HASH", "?v=" . $hashScript);

//Get page ID (id_menu) of home page
$i = 0;
$resultadoHome = $db->callProcedure("CALL ed_sp_web_menu_home_obtener(" . $_SESSION["id_idioma"] . ")");
$datoHome = $db->getData($resultadoHome);
$idMenuHome = $datoHome["id_menu"];
$inscripcionActiva = true;

//Assign "Remember password" link
$plantilla->assign("URL_REMEMBER_PASSWORD", "remember_password.php");

if (isset($esHome)) {
    $idMenu = $idMenuHome;
}

if (isset($idSeo)) {
    //Get the page title
    $resultadoTitulo = $db->callProcedure("CALL ed_sp_web_seo_obtener_concreto(" . $idSeo . "," . $_SESSION["id_idioma"] . ")");
    $datoTitulo = $db->getData($resultadoTitulo);
    if ($datoTitulo["title"] != "") {
        $plantilla->assign("TITULO_WEB", $datoTitulo["title"]);
        $plantilla->assign("ITEM_MENU_MOBIL_TITULO", $datoTitulo["title"]);
        $plantilla->parse("contenido_principal.item_menu_mobil");

    }
    if ($datoTitulo["canonical"] != "") {
        $plantilla->assign("METATAG_OPCIONAL_CANONICAL", $datoTitulo["canonical"]);
        $plantilla->parse("contenido_principal.metatags_opcionales.metatag_canonical");
        $plantilla->parse("contenido_principal.metatags_opcionales");

    }


    //Get page metadata
    $resultadoMetaTag = $db->callProcedure("CALL ed_sp_web_seo_metatag_obtener(" . $idSeo . "," . $_SESSION["id_idioma"] . ")");
    while ($datoMetaTag = $db->getData($resultadoMetaTag)) {
        $plantilla->assign("METATAG_TITULO", $datoMetaTag["nombre"]);
        $plantilla->assign("METATAG_DESCRIPCION", $datoMetaTag["descripcion"]);

        $plantilla->parse("contenido_principal.metatag_concreto");
    }

    if (!$esSeoTag) {
        //Get SEO tag
        $resultadoSeoTag = $db->callProcedure("CALL ed_sp_web_menu_seo_obtener(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
        $datoSeoTag = $db->getData($resultadoSeoTag);

        $plantilla->assign("SEO_TAG", $datoSeoTag["seo_tag"]);
    }
}

/*
 * If user is logged in…
 * Get user details and membership status (paid-up, pending, expired, etc.) and construct login panel
 */
if (isset($_SESSION["met_user"])) {
    //Set the user type for menu display purposes to that of the currently logged-in user
    $idMenuTipo = $_SESSION["met_user"]["tipoUsuario"];
    //If user is not a "nominee" (now obsolete), i.e. if user is an ordinary member
    if ($idMenuTipo != TIPO_USUARIO_INVITADO) {
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

        //If user is a paid-up member, assign expiry date
        if ($_SESSION["met_user"]["pagado"] == 1 || $inscripcionActiva) {
            $plantilla->assign("MIEMBRO_FECHA_HASTA", generalUtils::conversionFechaFormato($_SESSION["met_user"]["fecha_finalizacion"]));


            //If member is expired, assign "expired" message
            if (generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"])) {
                $plantilla->assign("EXPIRED_ACCOUNT", STATIC_EXPIRED_ACCOUNT);

                //If user is not an institutional member, get ID of renewal form
                if ($_SESSION["met_user"]["institution_id"] == "") {
                    //Obtenemos el menu asociado al renew membership
                    $resultadoMenuFormulario = $db->callProcedure("CALL ed_sp_web_menu_formulario_obtener(" . FORM_TYPE_RENEW_MEMBERSHIP_ID . ")");
                    $datoMenuFormulario = $db->getData($resultadoMenuFormulario);
                    $idMenuFormulario = $datoMenuFormulario["id_menu"];


                    //Get the URL of the renewal page
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
            $plantilla->parse("contenido_principal.datos_usuario.bloque_miembro_hasta");
        } else {
            $plantilla->parse("contenido_principal.datos_usuario.bloque_pago_pendiente");
        }
    }


    //Display the user panel with username and details
    $plantilla->assign("PANEL_LOGIN_USERNAME", $_SESSION["met_user"]["username"]);
    $plantilla->assign("EDIT_MY_PROFILE", "Edit profile");
    $plantilla->assign("LOGOUT", "Log out");
    $plantilla->parse("contenido_principal.datos_usuario");

    /*
     * If user is not logged in…
     */
} else {
    // Set user type for menu display purposes to "non-member" (-1)
    $idMenuTipo = -1;
    $plantilla->assign("PANEL_LOGIN_USERNAME", "Log in");
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

/*
 * REDIRECTS
 */
$resultadoMenuConcreto = $db->callProcedure("CALL ed_sp_web_menu_obtener_concreto(" . $idMenu . "," . $_SESSION["id_idioma"] . ")");
//If requested page does not exist, redirect to home page
if ($db->getNumberRows($resultadoMenuConcreto) == 0) {
    generalUtils::redirigir(CURRENT_DOMAIN);
} else {
    $datoMenuConcreto = $db->getData($resultadoMenuConcreto);
    //If requested page requires login and user is not logged in, redirect to home page
    if ($datoMenuConcreto["id_tipo_usuario_web"] != "" && !isset($_SESSION["met_user"])) {
        $_SESSION["auth_target"] = $_SERVER["REQUEST_URI"];
        generalUtils::redirigir(CURRENT_DOMAIN);
        //If requested page is council only and user is ordinary member, redirect to home page
    } else if (isset($_SESSION["met_user"]) && $_SESSION["met_user"]["tipoUsuario"] == TIPO_USUARIO_SOCIO && $datoMenuConcreto["id_tipo_usuario_web"] == TIPO_USUARIO_CONSEJO) {
        generalUtils::redirigir(CURRENT_DOMAIN);
        //If requested page is admin only and user is not admin, redirect to home page
    } else if (isset($_SESSION["met_user"]) && $datoMenuConcreto["id_tipo_usuario_web"] == TIPO_USUARIO_ADMIN && $_SESSION["met_user"]["tipoUsuario"] != TIPO_USUARIO_ADMIN) {
        generalUtils::redirigir(CURRENT_DOMAIN);
        //If requested page is member only and is not membership renewal form and user is expired or pending, redirect to home page
    } else if ($datoMenuConcreto["id_tipo_usuario_web"] != "" && $datoMenuConcreto["id_formulario"] != 3 && isset($_SESSION["met_user"]) && !$inscripcionActiva && ($_SESSION["met_user"]["pagado"] == 0 || generalUtils::esMiembroCaducado($_SESSION["met_user"]["fecha_finalizacion"]))) {
        generalUtils::redirigir(CURRENT_DOMAIN);
        //If requested page is membership renewal form and user is a direct debit member, redirect to DD error page
    } else if ($datoMenuConcreto["id_formulario"] == 3 && $_SESSION["met_user"]["tipoPago"] == INSCRIPCION_TIPO_PAGO_DEBIT) {
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/direct-debit-error-message:821");
        //If requested page is METM registration form and user is not logged in, redirect to "Are you a member?" page
    } else if ($idMenu == 953 && !isset($_SESSION["met_user"])) {
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/are-you-a-member:955");
        //If requested page is "How to register" page and user is logged in, redirect to METM registration form
    } else if ($idMenu == 950 && isset($_SESSION["met_user"])) {
        generalUtils::redirigir(CURRENT_DOMAIN . "/en/conference-registration-form-members:953");
    }
}


$totalGet = count($_GET);
if ($totalGet == 0 || ($totalGet == 1 && isset($_GET["idioma"]))) {
    $caracter = "?";
} else {
    $caracter = "&";
}

/**
 * Load top-level items for main (horizontal) menu
 * First, get list of top-level menu items
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
    //$plantilla->assign("ITEM_MENU_SUPERIOR_URL", $vectorAtributosMenu["seo_url"]."?menu=".$vectorAtributosMenu["id_menu"]);

    //$plantilla->assign("ITEM_MENU_SUPERIOR_URL", $dataMenuSuperior["url"]."?menu=".$dataMenuSuperior["id_menu"]);
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


            $plantilla->assign("ITEM_MENU_SUPERIOR_PRIVATE", ($dataMenuSuperior["id_tipo_usuario_web"] != "" && ($idMenuTipo == TIPO_USUARIO_ADMIN || $idMenuTipo == TIPO_USUARIO_CONSEJO || $dataMenuSuperiorDesplegable["id_tipo_usuario_web"] == $idMenuTipo) ? "specialMenu" : ""));

    if ($cont == 0) {
        $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "first");
    } elseif ($cont == $totalMenus - 1) {
        $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "last");
    } else {
        $plantilla->assign("ITEM_MENU_SUPERIOR_ID", "");
    }

            /*
            if(isset($_SESSION["met_user"])){
                //Hide membership form from direct debit institutional members (obsolete)
                if($dataMenuSuperiorDesplegable["id_formulario"]==3 && ($_SESSION["met_user"]["institution_id"]!="" || $_SESSION["met_user"]["tipoPago"]==INSCRIPCION_TIPO_PAGO_DEBIT)){
                    $esMostrableSubMenu=false;
                }

                //Hide Edit profile menu item from nominees (obsolete)
                if($dataMenuSuperiorDesplegable["id_modulo"]==12 && $_SESSION["met_user"]["tipoUsuario"]==4){
                    $esMostrableSubMenu=false;
                }
            }
            */

            //Create array of dropdown submenu attributes
            $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
            $vectorAtributosMenu["id_menu"] = $dataMenuSuperiorDesplegable["id_menu"];
            $vectorAtributosMenu["seo_url"] = $dataMenuSuperiorDesplegable["seo_url"];
            $vectorAtributosMenu = generalUtils::generarUrlMenuContenido($db, $dataMenuSuperiorDesplegable["id_modulo"], $dataMenuSuperiorDesplegable["id_menu"], $dataMenuSuperiorDesplegable["descripcion"], $_SESSION["id_idioma"], $vectorAtributosMenu);

            //Assign URLs of dropdown submenu items
            $plantilla->assign("ITEM_MENU_SUPERIOR_DESPLEGABLE_URL", generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

            //Assign CSS class "specialMenu" to non-public menu items
            $plantilla->assign("ITEM_MENU_SUPERIOR_DESPLEGABLE_PRIVATE", ($dataMenuSuperiorDesplegable["id_tipo_usuario_web"] != "" && ($idMenuTipo == TIPO_USUARIO_ADMIN || $idMenuTipo == TIPO_USUARIO_CONSEJO || $dataMenuSuperiorDesplegable["id_tipo_usuario_web"] == $idMenuTipo || ($dataMenuSuperiorDesplegable["id_tipo_usuario_web"] == TIPO_USUARIO_SOCIO && $_SESSION["met_user"]["tipoUsuario"] == TIPO_USUARIO_INVITADO)) ? "specialMenu" : ""));

            //If submenu items is not hidden, assign to template and display
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
 * Load the secondary lefthand menu
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
 * Load sidebar buttons ("botones acceso")
 * First get list of sidebar buttons
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