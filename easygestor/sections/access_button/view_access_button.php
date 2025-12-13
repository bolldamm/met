<?php
/**
 *
 * Listamos todos las areas en el sistema
 * @Author eData
 *
 */

//Plantilla principal
$plantilla = new XTemplate("html/principal.html");

//Plantilla secundaria
$subPlantilla = new XTemplate("html/sections/access_button/view_access_button.html");

$mostrarPaginador = true;
$valorDefecto = -1;
$campoOrden = "orden";
$direccionOrden = "ASC";
$numeroRegistrosPagina = DEFAULT_FILTER_NUMBER;

/**
 *
 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
 *
 */
$matrizOrden[0]["descripcion"] = STATIC_ORDER_ACCESS_BUTTON_ID_FIELD;
$matrizOrden[0]["valor"] = "id_boton_acceso";


//Gestion del campo de orden y filtro de numero de registros
require "includes/load_filter_list.inc.php";

/**
 *
 * El total de paginas que mostraremos por pantalla
 * @var int
 *
 */
$totalPaginasMostrar = 4;
$codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_boton_acceso_listar(" . $_SESSION["user"]["language_id"] . ",'" . $campoOrden . "','" . $direccionOrden . "',";

$urlActual = "main_app.php?section=access_button&action=view&hdnOrden=" . $valorDefecto . "&hdnRegistros=" . $numeroRegistrosPagina . "&";

//Paginador
require "includes/load_paginator.inc.php";

//Pagina actual
$subPlantilla->assign("PAGINA_ACTUAL", $paginaActual);

$resultado = $db->callProcedure($codeProcedure);
$i = 0;
while ($dato = $db->getData($resultado)) {
    if ($dato["activo"] == 0) {
        $subPlantilla->assign("STATE_STYLE", "class='disabled' title='" . STATIC_VIEW_ACCESS_BUTTON_ITEM_DISABLED . "'");
    } else {
        $subPlantilla->assign("STATE_STYLE", "");
    }
    if ($i % 2 == 0) {
        $subPlantilla->assign("TR_STYLE", "class='dark'");
    } else {
        $subPlantilla->assign("TR_STYLE", "class='light'");
    }
    $vectorBotonAcceso["ID"] = $dato["id_boton_acceso"];
    if ($dato["url"] == "") {
        $dato["url"] = "&nbsp;";
    }
    $vectorBotonAcceso["URL"] = substr($dato["url"], 0, 50);
    $vectorBotonAcceso["ORDER"] = $dato["orden"];
    if ($i == 0) {
        $plantilla->assign("PRIMER_ORDEN", $dato["orden"]);
    }
    $subPlantilla->assign("ACCESS_BUTTON", $vectorBotonAcceso);
    $subPlantilla->parse("contenido_principal.item_boton_acceso");
    $i++;
}

//Si no hemos seleccionado un filtro de orden...
if ($valorDefecto == -1) {
    //Carga inicial
    $i = 0;
    $vectorTablaOrden[$i]["ID_CONTAINER"] = "tblList";
    $vectorTablaOrden[$i]["ID_CONTAINER_BODY"] = "tblListItem";
    $vectorTablaOrden[$i]["NAME"] = "access_button";

    require "includes/load_order_table.inc.php";

    //Construimos
    $plantilla->parse("contenido_principal.carga_inicial");
}

//Migas de pan
$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
$vectorMigas[1]["url"] = STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_LINK;
$vectorMigas[1]["texto"] = STATIC_BREADCUMB_ACCESS_BUTTON_VIEW_ACCESS_BUTTON_TEXT;


require "includes/load_breadcumb.inc.php";

//Informacion del usuario
require "includes/load_information_user.inc.php";

//Contruimos plantilla secundaria
$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la plantilla principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Construimos plantilla principal
$plantilla->parse("contenido_principal");

//Mostramos plantilla principal por pantalla
$plantilla->out("contenido_principal");
?>