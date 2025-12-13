<?php
/**
 *
 * Presentamos por pantalla las migas de pan
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */

/**
 * BREADCRUMB
 */
$vectorMenuId = Array();
$vectorMenuNombre = Array();
$vectorMenuURL = Array();

$vectorMigas = Array();

// If the URL of the requested page includes a menu ID (otherwise ID = 1 = home),
// call procedure to get details of the requested menu (page)
// (ID, module, parent, title, description, SEO, etc)
if (isset($idMenu)) {
    $resultBreadcrumb = $db->callProcedure("CALL ed_sp_web_menu_breadcrumb(null, " . $idMenu . ", " . $_SESSION["id_idioma"] . ",'','','','','','','','')");
    $datoBreadcrumb = $db->getData($resultBreadcrumb);


    // Menu details include all parents [requested~parent~grandparent-greatgrandparent]
    // so first they have to be exploded
    $explodeMenuId = explode("~", $datoBreadcrumb["id_menu"]);
    $explodeModuleId = explode("~", $datoBreadcrumb["id_modulo"]);
    $explodeMenuNombre = explode("~", $datoBreadcrumb["nombre_menu"]);
    $explodeMenuDescripcion = explode("~", $datoBreadcrumb["descripcion_menu"]);
    $explodeMenuUrl = explode("~", $datoBreadcrumb["url"]);
    $explodeMenuSeoTitle = explode("~", $datoBreadcrumb["seo_title_menu"]);
    $explodeMenuSeoCanonical = explode("~", $datoBreadcrumb["seo_canonical_menu"]);
    $explodeMenuSeoUrl = explode("~", $datoBreadcrumb["seo_url_menu"]);

    // Invert the arrays, so that the order is greatgrandparent-grandparent~parent~requested
    // This means that $explodeMenuId[0] is the ultimate parent of the requested page
    $explodeMenuId = array_reverse($explodeMenuId);
    $explodeModuleId = array_reverse($explodeModuleId);
    $explodeMenuNombre = array_reverse($explodeMenuNombre);
    $explodeMenuDescripcion = array_reverse($explodeMenuDescripcion);
    $explodeMenuUrl = array_reverse($explodeMenuUrl);
    $explodeMenuSeoTitle = array_reverse($explodeMenuSeoTitle);
    $explodeMenuSeoCanonical = array_reverse($explodeMenuSeoCanonical);
    $explodeMenuSeoUrl = array_reverse($explodeMenuSeoUrl);
    // Count number of IDs, i.e. how many pages deep
    $contMenu = count($explodeMenuId);

    // Counting down through the hierarchy…
    $contMigas = 0;
    for ($cont = 0; $cont < $contMenu; $cont++) {
        // Build array of name and URL of each menu (i.e. page) in the hierarchy
        $vectorMigas[$cont]["descripcion"] = $explodeMenuNombre[$cont];
        $vectorMigas[$cont]["url"] = $explodeMenuUrl[$cont] . "?menu=" . $explodeMenuId[$cont];
        // Build array of language, ID and SEO of each breadcrumb
        $vectorAtributosMenu["idioma"] = $_SESSION["siglas"];
        $vectorAtributosMenu["id_menu"] = $explodeMenuId[$cont];
        $vectorAtributosMenu["seo_url"] = $explodeMenuSeoUrl[$cont];
        // Construct URL for each item in the breadcrumbs
        $vectorAtributosMenu = generalUtils::generarUrlMenuContenido($db, $explodeModuleId[$cont], $explodeMenuId[$cont], $explodeMenuDescripcion[$cont], $_SESSION["id_idioma"], $vectorAtributosMenu);
        /**
         * pretty URL parameters
         */
        $vectorMigas[$cont]["url"] = generalUtils::generarUrlAmigableMenu($vectorAtributosMenu);
        /**
         * end pretty URL parameters
         */
        $vectorMigas[$cont]["seo_title"] = $explodeMenuSeoTitle[$cont];
        $vectorMigas[$cont]["seo_canonical"] = $explodeMenuSeoCanonical[$cont];

    }
}

//Create an instance of the breadcrumb template
$plantillaBreadcrumb = new XTemplate("html/includes/breadcrumb.html");

/*
 * 10 Sep 2019: changes made to eliminate non-fatal PHP errors
 * $idMenu is explicitly set to null on certain pages, namely,
 * inscripcion_finalizada.php, reset_password.php, remember_password.php, stripe_form.php
 * When $idMenu is null, $totalMigas = 0 and $vectorMigas[$cont] is undefined
 * causing errors in the "for" loop, so added the extra if($totalMigas == 0)
 */
$totalMigas = count($vectorMigas);

if ($totalMigas == 0) {
    $plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb");
} else {
    //Assign breadcrumbs to template placeholders
    $cont = 0;
    for ($cont = 0; $cont < $totalMigas; $cont++) {
        $plantillaBreadcrumb->assign("BREADCRUMB_DESCRIPCION", $vectorMigas[$cont]["descripcion"]);
        $plantillaBreadcrumb->assign("BREADCRUMB_URL", $vectorMigas[$cont]["url"]);
        if ($cont != $totalMigas - 1 || ($cont == $totalMigas - 1 && isset($breadCrumbUrlDetalle))) {
            $plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb.separador");
        }
        $plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb");
    }
}

//If there is a "Details" page
if (isset($breadCrumbDescripcionDetalle)) {
    $plantillaBreadcrumb->assign("BREADCRUMB_DESCRIPCION", $breadCrumbDescripcionDetalle);
    /*
     * Commented out $breadCrumbUrlDetalle on inscripcion_finalizada.php and stripe_form.php
     * and added "if" to avoid having a page link on the breadcrumb on those pages
     */
    if (isset($breadCrumbUrlDetalle)) {
        $breadCrumbDescripcionDetalle = " - " . $breadCrumbDescripcionDetalle;
        $plantillaBreadcrumb->assign("BREADCRUMB_URL", $breadCrumbUrlDetalle);
    }
    $plantillaBreadcrumb->parse("contenido_principal.item_breadcrumb");
} else {
    $breadCrumbDescripcionDetalle = "";
}

//Page title (text on tab in Google)
if ($totalMigas > 0) {
    $plantilla->assign("TITULO_WEB", $vectorMigas[$totalMigas - 1]["seo_title"]);
    $plantilla->assign("TITULO_WEB", $vectorMigas[$totalMigas - 1]["seo_title"] . $breadCrumbDescripcionDetalle);
} else {
    $plantilla->assign("TITULO_WEB", $breadCrumbDescripcionDetalle);
}
$plantillaBreadcrumb->parse("contenido_principal");

$plantilla->assign("BREADCRUMB", $plantillaBreadcrumb->text("contenido_principal"));
?>