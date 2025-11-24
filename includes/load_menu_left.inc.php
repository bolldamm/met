<?php
/**
 *
 * Presentamos por pantalla el menu lateral con los hijos
 * @author Edata S.L.
 * @copyright Edata S.L.
 * @version 1.0
 */


/**
 * $explodeMenuId (from load_breadcrumb.inc.php) is a string of IDs of ancestors of the currently active page ("path")
 * $explodeMenuId[0] is the first ID in the string, i.e. the top-level menu
 * $resultMenuLeft gets details (ID, tipo_usuario, name, etc.) of all the children of the top-level parent
 * these children form the "parents" of the lefthand menu (level 1)
 */
$resultMenuLeft = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[0].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
$totalMenuPadre = $db->getNumberRows($resultMenuLeft);
/**
 * If there are no children in the path, we're on the home page, so no lefthand menu
 * load_menu_left.inc.php is required on all openbox and other pages that aren't the home page
 */
if($totalMenuPadre > 0) {
    //Create an instance of the lefthand menu template
    $plantillaMenuLeft = new XTemplate("html/includes/menu_left.inc.html");
    /*
     * Iterate through the children of the top-level parent of the currently active page
     * At each ID which is in the path, loop through the children, if any
     * Continue down until completing the level of the currently active page
     */
    while($dataMenuLeft = $db->getData($resultMenuLeft)) {
        $esMostrableSubMenu=true;

        /*
        //If user is logged in…
        if(isset($_SESSION["met_user"])){
            //Hide menu item for membership renewal form (id_formulario=3) from non-members and institutional members
            if($dataMenuLeft["id_formulario"]==3 && ($_SESSION["met_user"]["tipoUsuario"]!=1 || $_SESSION["met_user"]["institution_id"]!="")){
                $esMostrableSubMenu=false;
                //Hide menu item for member profile from nominees (tipo_usuario=4)
            }else if($dataMenuLeft["id_modulo"]==12 && $_SESSION["met_user"]["tipoUsuario"]==4){
                $esMostrableSubMenu=false;
            }
        }
        */

        //Assign page title (nombre) to placeholder in menu template (menu_left.inc.html)
        $plantillaMenuLeft->assign("ITEM_MENU_LEFT_NOMBRE", $dataMenuLeft["nombre"]);

        //Assign values to menu attributes array
        $vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
        $vectorAtributosMenu["id_menu"]=$dataMenuLeft["id_menu"];
        $vectorAtributosMenu["seo_url"]=$dataMenuLeft["seo_url"];

        //Generate URL for each submenu item
        $vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeft["id_modulo"],$dataMenuLeft["id_menu"],$dataMenuLeft["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);

        //Assign submenu URL to placeholder
        $plantillaMenuLeft->assign("ITEM_MENU_LEFT_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

        //Assign the value "current" or "" to placeholder depending on whether menu is current (for CSS class)
        $plantillaMenuLeft->assign("ITEM_MENU_LEFT_ACTIVO", ($dataMenuLeft["id_menu"] == $idMenu) ? "current" : "");

        /*
         * if the lefthand parent is in the path of the currently active page ($explodeMenuId[1])
         * $resultMenuLeftHijo gets details (ID, tipo_usuario, name, etc.) of all the children of that parent
         * these are the "children" of the lefthand menu (level 2)
         */
        if(isset($explodeMenuId[1]) && $explodeMenuId[1] == $dataMenuLeft["id_menu"]) {
            $resultMenuLeftHijo = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[1].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
            if($db->getNumberRows($resultMenuLeftHijo) > 0) {
                /*
                * Iterate through the lefthand menu "children" in the path
                */
                while($dataMenuLeftHijo = $db->getData($resultMenuLeftHijo)) {

                    $plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_NOMBRE", $dataMenuLeftHijo["nombre"]);

                    $vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
                    $vectorAtributosMenu["id_menu"]=$dataMenuLeftHijo["id_menu"];
                    $vectorAtributosMenu["seo_url"]=$dataMenuLeftHijo["seo_url"];

                    $vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftHijo["id_modulo"],$dataMenuLeftHijo["id_menu"],$dataMenuLeftHijo["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);


                    $plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));

                    $plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_ACTIVO", ($dataMenuLeftHijo["id_menu"] == $idMenu) ? "current" : "");

                    /*
                     * if the lefthand child is in the path of the currently active page ($explodeMenuId[2])
                     * $resultMenuLeftSubHijo gets details (ID, tipo_usuario, name, etc.) of all the child's children
                     * these are the "grandchildren" of the lefthand menu (level 3)
                     */
                    if(isset($explodeMenuId[2]) && $explodeMenuId[2] == $dataMenuLeftHijo["id_menu"]) {
                        //This time, $explodeMenuId[2] is the id_menu of the current child page
                        $resultMenuLeftSubHijo = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[2].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
                        if($db->getNumberRows($resultMenuLeftSubHijo) > 0) {
                            /*
                            * Iterate through the lefthand menu "grandchildren" in the path
                            */
                            while($dataMenuLeftSubHijo = $db->getData($resultMenuLeftSubHijo)) {
                                $plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_NOMBRE", $dataMenuLeftSubHijo["nombre"]);

                                $vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
                                $vectorAtributosMenu["id_menu"]=$dataMenuLeftSubHijo["id_menu"];
                                $vectorAtributosMenu["seo_url"]=$dataMenuLeftSubHijo["seo_url"];

                                $vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftSubHijo["id_modulo"],$dataMenuLeftSubHijo["id_menu"],$dataMenuLeftSubHijo["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);


                                $plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));


                                $plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_ACTIVO", ($dataMenuLeftSubHijo["id_menu"] == $idMenu) ? "current" : "");

                                /*
                                 * if the lefthand grandchild is in the path of the currently active page ($explodeMenuId[3])
                                 * $resultMenuLeftNieto gets details (ID, tipo_usuario, name, etc.) of all the grandchild's children
                                 * these are the "great grandchildren" of the lefthand menu (level 4)
                                 */
                                if(isset($explodeMenuId[3]) && $explodeMenuId[3] == $dataMenuLeftSubHijo["id_menu"]) {
                                    //This time, $explodeMenuId[3] is the id_menu of the current grandchild page
                                    $resultMenuLeftNieto = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[3].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
                                    if($db->getNumberRows($resultMenuLeftNieto) > 0) {
                                        /*
                                         * Iterate through the lefthand menu "great grandchildren" in the path
                                         */
                                        while($dataMenuLeftNieto = $db->getData($resultMenuLeftNieto)) {
                                            $plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_NOMBRE", $dataMenuLeftNieto["nombre"]);

                                            $vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
                                            $vectorAtributosMenu["id_menu"]=$dataMenuLeftNieto["id_menu"];
                                            $vectorAtributosMenu["seo_url"]=$dataMenuLeftNieto["seo_url"];

                                            $vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftNieto["id_modulo"],$dataMenuLeftNieto["id_menu"],$dataMenuLeftNieto["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);


                                            $plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));


                                            //$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_URL", $dataMenuLeftSubHijo["url"]."?menu=".$dataMenuLeftSubHijo["id_menu"]);

                                            $plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_ACTIVO", ($dataMenuLeftNieto["id_menu"] == $idMenu) ? "current" : "");

                                            $plantillaMenuLeft->assign("ESTILO_NIETO","style='padding-left:8px;'");
                                            $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo.menu_left_nieto.item_menu_left_nieto");

                                        }
                                        $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo.menu_left_nieto");
                                    }
                                }
                                $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo");
                            }

                            $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo");
                        }
                    }


                    $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo");
                }
                $plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo");
            }
        }

        if($esMostrableSubMenu){
            $plantillaMenuLeft->parse("contenido_principal.item_menu_left");
        }
    }

    $plantillaMenuLeft->parse("contenido_principal");

    $plantilla->assign("MENU_LEFT", $plantillaMenuLeft->text("contenido_principal"));
}
?>