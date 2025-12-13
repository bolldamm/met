<?php
/**
 *
 * Listamos todos los menus existentes en el sistema
 * @Author eData
 *
 */

//Plantilla principal
$plantilla = new XTemplate("html/principal.html");

//Plantilla secundaria
$subPlantilla = new XTemplate("html/sections/workshop/workshop_date/view_workshop_date.html");

$mostrarPaginador = true;
$valorDefecto = 0; // default sort is by name
$campoOrden = "nombre_completo"; // default sort is by name
$direccionOrden = "DESC";
//$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;
$numeroRegistrosPagina = -1; // default is All per page

/**
 *
 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
 *
 */
$matrizOrden[0]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_NAME_FIELD;
$matrizOrden[0]["valor"] = "nombre_completo";
$matrizOrden[1]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_EMAIL_FIELD;
$matrizOrden[1]["valor"] = "correo_electronico";
$matrizOrden[2]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_INSCRIPTION_NUMBER_FIELD;
$matrizOrden[2]["valor"] = "numero_inscripcion";
$matrizOrden[3]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_PAID_FIELD;
$matrizOrden[3]["valor"] = "pagado";
$matrizOrden[4]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_ASSISTED_FIELD;
$matrizOrden[4]["valor"] = "asistido";
$matrizOrden[5]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_USER_TYPE_FIELD;
$matrizOrden[5]["valor"] = "tipo_usuario";


//Gestion del campo de orden y filtro de numero de registros
$campoOrdenDefecto = "";
require "includes/load_filter_list.inc.php";


if ($campoOrden != "numero_inscripcion" && $campoOrden != "pagado") {
    $direccionOrden = "ASC";
}


$idFechaTaller = 0;
$pagado = -1;
$asistido = -1;
$idTipoUsuario = 0;
$correoElectronico = "";
$nombre = "";
$numeroInscripcion = "";


$filtroPaginador = "";
if (isset($_GET["cmbFecha"])) {
  
  	// Display workshop number
  	$subPlantilla->assign("WORKSHOP_NUMBER", $_GET["cmbFecha"]);

    //filtro correo electronico
    $filtroPaginador = "txtEmail=" . $_GET["txtEmail"];
    $correoElectronico = $_GET["txtEmail"];
    $subPlantilla->assign("VIEW_WORKSHOP_DATE_EMAIL_ADDRESS_SEARCH_VALUE", $correoElectronico);


    //filtro nombre
    $filtroPaginador .= "&txtNombre=" . $_GET["txtNombre"];
    $nombre = $_GET["txtNombre"];
    $subPlantilla->assign("VIEW_WORKSHOP_DATE_NAME_SEARCH_VALUE", $nombre);


    //filtro numero inscripcion
    $filtroPaginador .= "&txtNumeroInscripcion=" . $_GET["txtNumeroInscripcion"];
    $numeroInscripcion = $_GET["txtNumeroInscripcion"];
    $subPlantilla->assign("VIEW_WORKSHOP_DATE_INSCRIPTION_NUMBER_SEARCH_VALUE", $numeroInscripcion);

    //Filtro fecha
    $idFechaTaller = $_GET["cmbFecha"];
    $filtroPaginador .= "&cmbFecha=" . $_GET["cmbFecha"];
    $subPlantilla->assign("FECHA_TALLER", $idFechaTaller);

    //Filtrado asistido
    if (isset($_GET["cmbPagado"])) {
        $pagado = $_GET["cmbPagado"];
        $filtroPaginador .= "&cmbPagado=" . $_GET["cmbPagado"];
    }

    if (isset($_GET["cmbAsistido"])) {
        //Filtrado asistido
        $asistido = $_GET["cmbAsistido"];
        $filtroPaginador .= "&cmbAsistido=" . $_GET["cmbAsistido"];
    }

    if (isset($_GET["cmbTipoUsuario"])) {
        //Filtrado asistido
        $idTipoUsuario = $_GET["cmbTipoUsuario"];
        $filtroPaginador .= "&cmbTipoUsuario=" . $_GET["cmbTipoUsuario"];
    }


    $filtroPaginador .= "&";
}


/**
 *
 * El total de paginas que mostraremos por pantalla
 * @var int
 *
 */
$totalPaginasMostrar = 4;

//check whether the selected workshop is a conference workshop or not (es_conferencia = 1 or 0)
$resultadoTipoWorkshop = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_taller_fecha_obtener_concreta(" . $idFechaTaller . ")");
$datoTipoWorkshop = $db->getData($resultadoTipoWorkshop);

if ($datoTipoWorkshop["es_conferencia"] == 0) {
    $esConferencia = 0;
    //query finds workshop registrations for a specified workshop date ($idFechaTaller)
    $codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_taller_concreto_listar('" . $nombre . "','" . $correoElectronico . "','" . $numeroInscripcion . "','" . $idFechaTaller . "'," . $pagado . "," . $asistido . "," . $idTipoUsuario . ",'" . $campoOrden . "','" . $direccionOrden . "',";
    $subPlantilla->assign("TR_STYLE", "class='light'");

} else {
    $esConferencia = 1;
    //query finds conference registrations associated with a specified conference workshop date ($idFechaTaller)
    $codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_conferencia_concreto_listar('" . $nombre . "','" . $correoElectronico . "','" . $numeroInscripcion . "','" . $idFechaTaller . "'," . $pagado . "," . $asistido . ",'" . $campoOrden . "','" . $direccionOrden . "',";
    $subPlantilla->assign("TR_STYLE", "class='light'");
}

$urlActual = "main_app.php?section=workshop_date&action=view&id_taller=" . $_GET["id_taller"] . "&hdnOrden=" . $valorDefecto . "&hdnRegistros=" . $numeroRegistrosPagina . "&" . $filtroPaginador;

//Paginator
require "includes/load_paginator.inc.php";


//get title of workshop (from workshop ID included in URL)
$resultadoTaller = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_taller_obtener_concreta(" . $_GET["id_taller"] . "," . $_SESSION["user"]["language_id"] . ")");
$datoTaller = $db->getData($resultadoTaller);

//Current page
$subPlantilla->assign("PAGINA_ACTUAL", $paginaActual);

$subPlantilla->parse("contenido_principal.boton_cancel_inscripcion");

//autoload PHPSpreadsheet class via composer;
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if (isset($_GET["excel"]) && $_GET["excel"] > 0) {
    $spreadsheet = new Spreadsheet();
    if ($_GET["excel"] == 1) {
        $fichero = "Workshop_participants.xls";
        $spreadsheet->getActiveSheet()->setCellValue("A1", "Reg ID");
        $spreadsheet->getActiveSheet()->setCellValue("B1", "Email");
        $spreadsheet->getActiveSheet()->setCellValue("C1", "Name");
        $spreadsheet->getActiveSheet()->setCellValue("D1", "Member");
        $spreadsheet->getActiveSheet()->setCellValue("E1", "Paid");
        $spreadsheet->getActiveSheet()->setCellValue("F1", "Attended");
        $spreadsheet->getActiveSheet()->setCellValue("G1", "Comment");
        $filaInicial = 2;
    }
}

/*
 * Get workshop signup data from database
 * and store in an array ($vectorTaller)
 * output to HTML template or Excel, as case may be
 */
$resultado = $db->callProcedure($codeProcedure);
$i = 0;
while ($dato = $db->getData($resultado)) {
    $nombreTaller = $datoTaller["nombre"];
    if ($i % 2 == 0) {
        $subPlantilla->assign("TR_STYLE", "class='dark'");
    } else {
        $subPlantilla->assign("TR_STYLE", "class='light'");
    }

    if ($esConferencia == 1) {
        $vectorTaller["ID"] = $dato["id_inscripcion_conferencia"] . "-" . $dato["id_inscripcion_conferencia_linea"];
        $subPlantilla->assign("ID_DESTINO", "2");
        $subPlantilla->assign("ID_DESTINO_PAGADO", "5");


        if ($dato["comentarios"] != "") {
            $vectorTaller["COMMENT_EXCEL"] = $dato["comentarios"];
            $vectorTaller["COMMENT"] = "<a href='main_app.php?section=conference_registered&action=edit&id_inscripcion=" . $dato["id_inscripcion_conferencia"] . "&id_conferencia=" . $dato["id_conferencia"] . "' title='" . htmlspecialchars($dato["comentarios"]) . "'><img src='images/comment.png' /></a>";
        } else {
            $vectorTaller["COMMENT"] = "";
            $vectorTaller["COMMENT_EXCEL"] = "";
        }//end else


    } else {
        $vectorTaller["ID"] = $dato["id_inscripcion_taller"] . "-" . $dato["id_inscripcion_taller_linea"];
        $subPlantilla->assign("ID_DESTINO", "1");
        $subPlantilla->assign("ID_DESTINO_PAGADO", "4");
    }


    //Get participant's membership status( member, sister association, non-member)
    if ($dato["id_usuario_web"] != "") {
        $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
        $vectorTaller["TYPE"] = "<img src='images/add_close_user_icon.png' title='" . $tipoUsuario . "'>";
    } else {
        if ($dato["id_asociacion_hermana"] == "") {
            $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
            $vectorTaller["TYPE"] = "<img src='images/add_remove_user_icon.png' title='" . $tipoUsuario . "'>";
        } else {
            $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
            $vectorTaller["TYPE"] = "<img src='images/female_user_icon.png' title='" . $tipoUsuario . "'>";
        }
    }

    $vectorTaller["TYPE_NAME"] = $tipoUsuario;
    $vectorTaller["REGISTRATION_NUMBER"] = $dato["numero_inscripcion"];
    $vectorTaller["NAME"] = $dato["nombre_completo"];
    $vectorTaller["EMAIL"] = $dato["correo_electronico"];
    $vectorTaller["WORKSHOP"] = $datoTaller["nombre"];

    if ($dato["pagado"] == 1) {
        $vectorTaller["PAID"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["pagado"] == 0) {
        $vectorTaller["PAID"] = STATIC_GLOBAL_BUTTON_NO;

    }

    if ($dato["asistido"] == 1) {
        $vectorTaller["ATTENDED"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["asistido"] == 0) {
        $vectorTaller["ATTENDED"] = STATIC_GLOBAL_BUTTON_NO;

    }
    $vectorTaller["PAID_NUMERIC"] = $dato["pagado"];
    $vectorTaller["ATTENDED_NUMERIC"] = $dato["asistido"];
  	$pdfFileName = "../files/customers/workshops/".$_GET['id_taller']."-".$idFechaTaller."-".$dato["numero_inscripcion"].".pdf";
  	if (file_exists($pdfFileName)) {
        $vectorTaller["CERTIFICATE_PDF"] = "<a href=../get_certificate.php?hash=".$dato['hash_generado']."><img src='images/icon.pdf.png' alt='Download'></a>";
    	// $vectorTaller["CERTIFICATE_PDF"] = "<a href='".$pdfFileName."'><img src='images/icon.pdf.png' alt='Download'></a>";
	} else {
    	$vectorTaller["CERTIFICATE_PDF"] = "";
	}

    if (isset($_GET["excel"]) && $_GET["excel"] > 0) {

        $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorTaller["REGISTRATION_NUMBER"]);
        $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorTaller["EMAIL"]);
        $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorTaller["NAME"]);
        $spreadsheet->getActiveSheet()->setCellValue("D" . $filaInicial, $vectorTaller["TYPE_NAME"]);
        $spreadsheet->getActiveSheet()->setCellValue("E" . $filaInicial, $vectorTaller["PAID"]);
        $spreadsheet->getActiveSheet()->setCellValue("F" . $filaInicial, $vectorTaller["ATTENDED"]);
        $spreadsheet->getActiveSheet()->setCellValue("G" . $filaInicial, $vectorTaller["COMMENT_EXCEL"]);
        $filaInicial++;

    } else {
        $subPlantilla->assign("WORKSHOP_DATE", $vectorTaller);
        $subPlantilla->parse("contenido_principal.item_taller");
    }

    $i++;
}

if (isset($_GET["excel"]) && $_GET["excel"] > 0) {
    //Insert new rows at top of worksheet with today's date and workshop title
    $spreadsheet->getActiveSheet()->insertNewRowBefore(1, 2);
    $spreadsheet->getActiveSheet()->setCellValue('A1','=TODAY()');
    $spreadsheet->getActiveSheet()->getStyle('A1')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
    $spreadsheet->getActiveSheet()->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getActiveSheet()->setCellValue("A2", "Workshop: $nombreTaller");

    //Format header rows
    $spreadsheet->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getStyle("A2")->getFont()->setSize(14);
    $spreadsheet->getActiveSheet()->getStyle("A3:G3")->getFont()->setBold(true);

    //Set column widths
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    // Rename worksheet (i.e. tab)
    $spreadsheet->getActiveSheet()->setTitle('Participants');

    // Redirect output to a client’s web browser (Xls)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $fichero);
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = IOFactory::createWriter($spreadsheet, 'Xls');
    $writer->save('php://output');
    exit;
}


//User type dropdown
$subPlantilla->assign("COMBO_FECHA", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_taller_fecha_obtener_combo(" . $_GET["id_taller"] . ")", "cmbFecha", "cmbFecha", $idFechaTaller, "fecha_formateada", "id_taller_fecha", STATIC_GLOBAL_COMBO_DEFAULT, 0, ""));


$matriz[1]["descripcion"] = STATIC_GLOBAL_BUTTON_YES;
$matriz[0]["descripcion"] = STATIC_GLOBAL_BUTTON_NO;


$subPlantilla->assign("COMBO_ASISTIDO", generalUtils::construirComboMatriz($matriz, "cmbAsistido", "cmbAsistido", $asistido, STATIC_GLOBAL_COMBO_DEFAULT, -1, ""));

$subPlantilla->assign("COMBO_PAGADO", generalUtils::construirComboMatriz($matriz, "cmbPagado", "cmbPagado", $pagado, STATIC_GLOBAL_COMBO_DEFAULT, -1, ""));


$matriz[3]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
$matriz[2]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
$matriz[1]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
$matriz[0]["descripcion"] = STATIC_GLOBAL_COMBO_DEFAULT;

$subPlantilla->assign("COMBO_USER_TYPE", generalUtils::construirComboMatriz($matriz, "cmbTipoUsuario", "cmbTipoUsuario", $idTipoUsuario, "", -1, ""));


//Breadcrumbs
$vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
$vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
$vectorMigas[1]["url"] = STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_LINK;
$vectorMigas[1]["texto"] = STATIC_BREADCUMB_WORKSHOP_VIEW_WORKSHOP_TEXT;
$vectorMigas[2]["url"] = STATIC_BREADCUMB_WORKSHOP_EDIT_WORKSHOP_LINK . "&id_taller=" . $_GET["id_taller"];
$vectorMigas[2]["texto"] = $datoTaller["nombre"];
$vectorMigas[3]["url"] = STATIC_BREADCUMB_WORKSHOP_DATE_VIEW_WORKSHOP_DATE_LINK . "&id_taller=" . $_GET["id_taller"];
$vectorMigas[3]["texto"] = STATIC_BREADCUMB_WORKSHOP_DATE_VIEW_WORKSHOP_DATE_TEXT;


//Workshop ID
$subPlantilla->assign("ID_TALLER", $_GET["id_taller"]);

require "includes/load_breadcumb.inc.php";

//User information
require "includes/load_information_user.inc.php";

//Contruimos plantilla secundaria
$subPlantilla->parse("contenido_principal");

//Incluimos proceso onload
$plantilla->parse("contenido_principal.carga_inicial");

//Exportamos plantilla secundaria a la plantilla principal
$plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

//Construimos plantilla principal
$plantilla->parse("contenido_principal");

//Mostramos plantilla principal por pantalla
$plantilla->out("contenido_principal");
