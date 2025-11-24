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
$subPlantilla = new XTemplate("html/sections/eletter/view_eletter.html");

$mostrarPaginador = true;
$valorDefecto = 0;
$campoOrden = "id_novedad";
$direccionOrden = "DESC";
$numeroRegistrosPagina = DEFAULT_FILTER_NUMBER;

/*
 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
 *
 */
$matrizOrden[0]["descripcion"] = STATIC_ORDER_ELETTER_ID_FIELD;
$matrizOrden[0]["valor"] = "id_novedad";
$matrizOrden[1]["descripcion"] = STATIC_ORDER_ELETTER_TITLE_FIELD;
$matrizOrden[1]["valor"] = "titulo";


//Gestion del campo de orden y filtro de numero de registros
$campoOrdenDefecto = "";
require "includes/load_filter_list.inc.php";

if ($campoOrden != "id_novedad") {
    $direccionOrden = "ASC";
}

$keywords = "";
$filtroPaginador = "";

if (isset($_GET["txtKeywords"])) {
    $keywords = $_GET["txtKeywords"];
    $subPlantilla->assign("VIEW_ELETTER_DESCRIPTION_SEARCH_VALUE", $keywords);
    $filtroPaginador .= "&txtKeywords=" . $keywords;
}

/**
 *
 * El total de paginas que mostraremos por pantalla
 * @var int
 *
 */
$totalPaginasMostrar = 4;

$codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_novedad_listar('" . generalUtils::escaparCadena($keywords) . "','" . $campoOrden . "','" . $direccionOrden . "',";

$urlActual = "main_app.php?section=eletter&action=view&hdnOrden=" . $valorDefecto . "&hdnRegistros=" . $numeroRegistrosPagina."&".$filtroPaginador;

//Paginador
require "includes/load_paginator.inc.php";

//Pagina actual
$subPlantilla->assign("PAGINA_ACTUAL", $paginaActual);

$resultado = $db->callProcedure($codeProcedure);
$i = 0;
while ($dato = $db->getData($resultado)) {
    if ($i % 2 == 0) {
        $subPlantilla->assign("TR_STYLE", "class='dark'");
    } else {
        $subPlantilla->assign("TR_STYLE", "class='light'");
    }
    $vectorEletter["ID"] = $dato["id_novedad"];
    $vectorEletter["NAME"] = $dato["titulo"];
    $subPlantilla->assign("ELETTER", $vectorEletter);
    $subPlantilla->parse("contenido_principal.item_eletter");
    $i++;
}

//Migas de pan
$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
$vectorMigas[1]["url"] = STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_LINK;
$vectorMigas[1]["texto"] = STATIC_BREADCUMB_ELETTER_VIEW_ELETTER_TEXT;


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