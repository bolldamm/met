<?php
/**
 * Displays list of signups for selected conference
 * Buttons to export subsets of the conference signup data
 *
 */

//Main template
$plantilla = new XTemplate("html/principal.html");

//Secondary template
$subPlantilla = new XTemplate("html/sections/conference/conference_registered/view_conference_registered.html");

$mostrarPaginador = true;
$valorDefecto = 0; // default sort is by name (name = 0, RegID = 2, see matrizOrden below)
$campoOrden = "nombre_completo";
$direccionOrden = "DESC";
$numeroRegistrosPagina = 200; //200 records (i.e. all signups) displayed on first page

$matrizOrden[0]["descripcion"] = STATIC_ORDER_CONFERENCE_REGISTERED_NAME_FIELD;
$matrizOrden[0]["valor"] = "nombre_completo";
$matrizOrden[1]["descripcion"] = STATIC_ORDER_CONFERENCE_REGISTERED_EMAIL_FIELD;
$matrizOrden[1]["valor"] = "correo_electronico";
$matrizOrden[2]["descripcion"] = STATIC_ORDER_CONFERENCE_REGISTERED_INSCRIPTION_NUMBER_FIELD;
$matrizOrden[2]["valor"] = "numero_inscripcion";
$matrizOrden[3]["descripcion"] = STATIC_ORDER_CONFERENCE_REGISTERED_AMOUNT_FIELD;
$matrizOrden[3]["valor"] = "importe_total";
$matrizOrden[4]["descripcion"] = STATIC_ORDER_CONFERENCE_REGISTERED_PAID_FIELD;
$matrizOrden[4]["valor"] = "pagado";
$matrizOrden[5]["descripcion"] = STATIC_ORDER_WORKSHOP_DATE_USER_TYPE_FIELD;
$matrizOrden[5]["valor"] = "tipo_usuario";

//Gestion del campo de orden y filtro de numero de registros
$campoOrdenDefecto = "";
require "includes/load_filter_list.inc.php";

//Show accepted signups by default
$idEstadoInscripcion = 2;
$nombre = "";
$idTipoUsuario = 0;

$filtroPaginador = "";

//Registration status filter
if (isset($_GET["cmbEstadoInscripcion"])) {
    $idEstadoInscripcion = $_GET["cmbEstadoInscripcion"];
    $filtroPaginador = "&cmbEstadoInscripcion=" . $idEstadoInscripcion;
}

//Name filter
if (isset($_GET["txtNombre"]) && $nombre != $_GET["txtNombre"]) {
    $nombre = $_GET["txtNombre"];
    $filtroPaginador .= "&txtNombre=" . $nombre;
    $subPlantilla->assign("VIEW_CONFERENCE_DATE_NAME_SEARCH_VALUE", $nombre);
}

//User type filter
if (isset($_GET["cmbTipoUsuario"])) {
    $idTipoUsuario = $_GET["cmbTipoUsuario"];
    $filtroPaginador .= "&cmbTipoUsuario=" . $idTipoUsuario;
}

if ($campoOrden != "numero_inscripcion" && $campoOrden != "pagado") {
    $direccionOrden = "ASC";
}

$totalPaginasMostrar = 4;

//Store database call in variable $codeProcedure
$codeProcedure = "CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_conferencia_listar(" . $_GET["id_conferencia"] . "," . $idEstadoInscripcion . ",'" . $nombre . "','" . $idTipoUsuario . "'," . $_SESSION["user"]["language_id"] . ",'" . $campoOrden . "','" . $direccionOrden . "',";

//Store current URL in variable $urlActual (for paginator)
$urlActual = "main_app.php?section=conference_registered&action=view&id_conferencia=" . $_GET["id_conferencia"] . "&hdnOrden=" . $valorDefecto . "&hdnRegistros=" . $numeroRegistrosPagina . "&" . $filtroPaginador . "&";

//Paginator
if (isset($_GET["excel"]) && $_GET["excel"] > 0) {
    $numeroRegistrosPagina = -1;
}
require "includes/load_paginator.inc.php";

//Set current page
$subPlantilla->assign("PAGINA_ACTUAL", $paginaActual);

//autoload PHPSpreadsheet class via composer;
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/*
 * Buttons set "excel" variable in HTML
 * 0=Display search result in EG
 * 1=Excel: Master list
 * 2=Excel: Certificates
 * 3=Excel: Badges
 * 4=Excel: Workshops
 * 5=Excel: Workshop signups for pre-conference email
 * If !isset($_GET["excel"]), display results without filters
 * First, set up header row for Master list and Certificates (outside the "while" loop)
 * Set styles (bold for header, column width, text wrap for line breaks)
 */
if (isset($_GET["excel"]) && $_GET["excel"] > 0) {
    $spreadsheet = new Spreadsheet();
    if ($_GET["excel"] == 1) {
        $fichero = "Master list.xls";
        $spreadsheet->getActiveSheet()->setCellValue("A1", "Reg ID");
        $spreadsheet->getActiveSheet()->setCellValue("B1", "Email");
        $spreadsheet->getActiveSheet()->setCellValue("C1", "Full name");
        $spreadsheet->getActiveSheet()->setCellValue("D1", "First name");
        $spreadsheet->getActiveSheet()->setCellValue("E1", "Last name(s)");
        $spreadsheet->getActiveSheet()->setCellValue("F1", "Country");
        $spreadsheet->getActiveSheet()->setCellValue("G1", "Contact");
        $spreadsheet->getActiveSheet()->setCellValue("H1", "Member");
        $spreadsheet->getActiveSheet()->setCellValue("I1", "Sister assoc.");
        $spreadsheet->getActiveSheet()->setCellValue("J1", "Speaker");
        $spreadsheet->getActiveSheet()->setCellValue("K1", "Badge");
        $spreadsheet->getActiveSheet()->setCellValue("L1", "Dinner");
        $spreadsheet->getActiveSheet()->setCellValue("M1", "Email permit");
        $spreadsheet->getActiveSheet()->setCellValue("N1", "Payment method");
        $spreadsheet->getActiveSheet()->setCellValue("O1", "Dinner guests");
        $spreadsheet->getActiveSheet()->setCellValue("P1", "Reception guests");
        $spreadsheet->getActiveSheet()->setCellValue("Q1", "Amount");
        $spreadsheet->getActiveSheet()->setCellValue("R1", "Paid");
        $spreadsheet->getActiveSheet()->setCellValue("S1", "Attended");
        $spreadsheet->getActiveSheet()->setCellValue("T1", "Comments");
        $spreadsheet->getActiveSheet()->setCellValue("U1", "Attendee list");
        $spreadsheet->getActiveSheet()->setCellValue("V1", "Password");
		$spreadsheet->getActiveSheet()->setCellValue("W1", "First-timer");
        $filaInicial = 2;

        $spreadsheet->getActiveSheet()->getStyle("A1:W1")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:T500')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(60);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(8);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
        $spreadsheet->getActiveSheet()->getStyle('K1:K500')->getAlignment()->setWrapText(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Master list');

    } elseif ($_GET["excel"] == 2) {
        $fichero = "Certificates.xls";
        $spreadsheet->getActiveSheet()->setCellValue("A1", "Reg ID");
        $spreadsheet->getActiveSheet()->setCellValue("B1", "Name");
        $spreadsheet->getActiveSheet()->setCellValue("C1", "Speaker");
        $spreadsheet->getActiveSheet()->setCellValue("D1", "Paid");
        $spreadsheet->getActiveSheet()->setCellValue("E1", "Comments");
        $spreadsheet->getActiveSheet()->setCellValue("F1", "Workshops attended");

        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:F500')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(60);
        $spreadsheet->getActiveSheet()->getStyle('F1:F500')->getAlignment()->setWrapText(true);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Certificates');

        //First row at start of main loop
        $filaInicial = 2;
    } elseif ($_GET["excel"] == 5) {
        $fichero = "Workshops_for_email_reminder.xls";
        $spreadsheet->getActiveSheet()->setCellValue("A1", "Email address");
        $spreadsheet->getActiveSheet()->setCellValue("B1", "Full name");
        $spreadsheet->getActiveSheet()->setCellValue("C1", "First name");
        $spreadsheet->getActiveSheet()->setCellValue("D1", "Workshops");

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:D500')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $spreadsheet->getActiveSheet()->getStyle('D1:D500')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(45);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(60);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Workshop signups');

        //First row at start of main loop
        $filaInicial = 2;
    }
}

/*
 * Get conference signup data from DB
 * and store in an array ($vectorConferencia)
 * output to HTML template or Excel, as requested
 * NB Master list and Certificates need to be
 * in the same "while" loop as the HTML output.
 * Badges and Workshops are completely separate.
 */
$resultado = $db->callProcedure($codeProcedure);
$i = 0;
while ($dato = $db->getData($resultado)) {
    if ($i % 2 == 0) {
        $subPlantilla->assign("TR_STYLE", "class='dark'");
    } else {
        $subPlantilla->assign("TR_STYLE", "class='light'");
    }

    $vectorConferencia["REGISTRATION_NUMBER"] = $dato["numero_inscripcion"];
    $dato["nombre_completo"] = ucfirst($dato["nombre_completo"]);
    $vectorConferencia["FULL_NAME"] = $dato["nombre_completo"];
    $dato["nombre_inscrito"] = ucfirst($dato["nombre_inscrito"]);
    $vectorConferencia["FIRSTNAME"] = $dato["nombre_inscrito"];
    $vectorConferencia["LASTNAME"] = $dato["apellidos_inscrito"];
    $nombreCertificado = $dato["nombre_inscrito"] . " " . $dato["apellidos_inscrito"];

    $vectorConferencia["NAME_CERTIFICATE"] = $nombreCertificado;
    $vectorConferencia["COUNTRY"] = $dato["pais"];
    $vectorConferencia["EMAIL"] = $dato["correo_electronico"];


    if ($dato["pagado"] == 1) {
        $vectorConferencia["PAID"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["pagado"] == 0) {
        $vectorConferencia["PAID"] = STATIC_GLOBAL_BUTTON_NO;
    }

    $subPlantilla->assign("ID_DESTINO", "3");
    $subPlantilla->assign("ID_DESTINO_PAGADO", "6");

    $vectorConferencia["ID"] = $dato["id_inscripcion_conferencia"];
    $vectorConferencia["AMOUNT"] = $dato["importe_total"];

    if ($dato["id_usuario_web"] != "") {
        $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
        $vectorConferencia["MEMBER"] = "M";
        $vectorConferencia["PASSWORD"] = "";
    } else {
        if ($dato["id_asociacion_hermana"] == "") {
            $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
            $vectorConferencia["MEMBER"] = "-";
        } else {
            $tipoUsuario = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
            $vectorConferencia["MEMBER"] = "S";
        }
        $vectorConferencia["PASSWORD"] = $dato["conference_password"];
    }

    if ($dato["asistido"] == 1) {
        $vectorConferencia["ATTENDED"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["asistido"] == 0) {
        $vectorConferencia["ATTENDED"] = STATIC_GLOBAL_BUTTON_NO;

    }
  
    $pdfFileName = "../files/customers/conferences/".$_GET["id_conferencia"]."-".$dato['numero_inscripcion'].".pdf";
  	if (file_exists($pdfFileName)) {
    	$vectorConferencia["CERTIFICATE_PDF"] = "<a href=../get_metm_certificate.php?hash=".$dato['hash_generado']."><img src='images/icon.pdf.png' alt='Download'></a>";
	} else {
    	$vectorConferencia["CERTIFICATE_PDF"] = "";
	}

    if ($dato["observaciones"] != "") {
        $vectorConferencia["COMMENT"] = "<a href='main_app.php?section=conference_registered&action=edit&id_inscripcion=" . $dato["id_inscripcion_conferencia"] . "&id_conferencia=" . $_GET["id_conferencia"] . "' title='" . htmlspecialchars($dato["observaciones"]) . "'><img src='images/comment.png' /></a>";
    } else {
        $vectorConferencia["COMMENT"] = "";
    }//end else
    $vectorConferencia["COMMENT_EXCEL"] = $dato["observaciones"];

    $vectorConferencia["PAID_NUMERIC"] = $dato["pagado"];
    $vectorConferencia["ATTENDED_NUMERIC"] = $dato["asistido"];
  
    if ($dato["es_attendee_list"] == 1) {
        $vectorConferencia["ATTENDEE_LIST"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["es_attendee_list"] == 0) {
        $vectorConferencia["ATTENDEE_LIST"] = STATIC_GLOBAL_BUTTON_NO;
    }
	
	if ($dato["first_timer_or_council"] == 1) {
        $vectorConferencia["FIRST_TIMER"] = STATIC_GLOBAL_BUTTON_YES;
    } else {
        $vectorConferencia["FIRST_TIMER"] = STATIC_GLOBAL_BUTTON_NO;
    }
  
	// $vectorConferencia["PASSWORD"] = $dato["conference_password"];
	// $vectorConferencia["FIRST_TIMER"] = $dato["first_timer_or_council"];
    $vectorConferencia["CONTACT_PHONE"] = $dato["telefono"];

    $badgeLines = explode("\n", $dato["conference_badge"]);
    if (isset($badgeLines[0])) {
        $line1 = strip_tags($badgeLines[0]);
    } else {
        $line1 = "";
    }
    if (isset($badgeLines[1])) {
        $line2 = strip_tags($badgeLines[1]);
    } else {
        $line2 = "";
    }
    if (isset($badgeLines[2])) {
        $line3 = strip_tags($badgeLines[2]);
    } else {
        $line3 = "";
    }
    $vectorConferencia["BADGE"] = $line1 . "\n" . $line2 . "\n" . $line3;;

    $vectorConferencia["SISTER_ASSOCIATION"] = $dato["asociacion_hermana"];

    if ($dato["speaker"] == 1) {
        $vectorConferencia["SPEAKER"] = STATIC_GLOBAL_BUTTON_YES;
    } else if ($dato["speaker"] == 0) {
        $vectorConferencia["SPEAKER"] = STATIC_GLOBAL_BUTTON_NO;
    }

    if ($dato["es_dinner"] == 1) {
        $vectorConferencia["CLOSING_DINNER"] = STATIC_GLOBAL_BUTTON_NO;
    } else if ($dato["es_dinner"] == 0) {
        $vectorConferencia["CLOSING_DINNER"] = STATIC_GLOBAL_BUTTON_YES;
    }

    if ($dato["email_permiso"] == 0) {
        $vectorConferencia["EMAIL_PERMISSION"] = STATIC_GLOBAL_BUTTON_NO;
    } else if ($dato["email_permiso"] == 1) {
        $vectorConferencia["EMAIL_PERMISSION"] = STATIC_GLOBAL_BUTTON_YES;
    }

    $vectorConferencia["PAYMENT_METHOD"] = $dato["tipo_pago"];

    $resultDinnerGuests = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_guests_dinner_obtener(" . $dato["id_inscripcion_conferencia"] . ")");
    $resultReceptionGuests = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_guests_reception_obtener(" . $dato["id_inscripcion_conferencia"] . ")");
    $dinnerGuests = $db->getData($resultDinnerGuests);
    $receptionGuests = $db->getData($resultReceptionGuests);
    $vectorConferencia["DINNER_GUESTS"] = $dinnerGuests["valor"];
    $vectorConferencia["RECEPTION_GUESTS"] = $receptionGuests["valor"];

    $vectorConferencia["WORKSHOP_TITLE"] = "";
    $vectorConferencia["BADGE_NAME"] = "";
    $vectorConferencia["BADGE_AFFILIATION"] = "";

    //If not Excel, output data to EasyGestor, otherwise output to Excel
    if (!isset($_GET["excel"]) || (isset($_GET["excel"]) && $_GET["excel"] == 0)) {
        $subPlantilla->assign("CONFERENCE_REGISTERED", $vectorConferencia);
        if ($dato["activo"] == 1) {
            $subPlantilla->assign("ID_CONFERENCIA", $_GET["id_conferencia"]);
            $subPlantilla->parse("contenido_principal.item_conferencia.ir_detalle");
            $subPlantilla->parse("contenido_principal.item_conferencia");
        }//end if not Excel
    } else {
        if (isset($_GET["excel"]) && $_GET["excel"] == 1) {
            $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["REGISTRATION_NUMBER"]);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["EMAIL"]);
            $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorConferencia["FULL_NAME"]);
            $spreadsheet->getActiveSheet()->setCellValue("D" . $filaInicial, $vectorConferencia["FIRSTNAME"]);
            $spreadsheet->getActiveSheet()->setCellValue("E" . $filaInicial, $vectorConferencia["LASTNAME"]);
            $spreadsheet->getActiveSheet()->setCellValue("F" . $filaInicial, $vectorConferencia["COUNTRY"]);
            $spreadsheet->getActiveSheet()->setCellValue("G" . $filaInicial, $vectorConferencia["CONTACT_PHONE"]);
            $spreadsheet->getActiveSheet()->setCellValue("H" . $filaInicial, $vectorConferencia["MEMBER"]);
            $spreadsheet->getActiveSheet()->setCellValue("I" . $filaInicial, $vectorConferencia["SISTER_ASSOCIATION"]);
            $spreadsheet->getActiveSheet()->setCellValue("J" . $filaInicial, $vectorConferencia["SPEAKER"]);
            $spreadsheet->getActiveSheet()->setCellValue("K" . $filaInicial, $vectorConferencia["BADGE"]);
            $spreadsheet->getActiveSheet()->setCellValue("L" . $filaInicial, $vectorConferencia["CLOSING_DINNER"]);
            $spreadsheet->getActiveSheet()->setCellValue("M" . $filaInicial, $vectorConferencia["EMAIL_PERMISSION"]);
            $spreadsheet->getActiveSheet()->setCellValue("N" . $filaInicial, $vectorConferencia["PAYMENT_METHOD"]);
            $spreadsheet->getActiveSheet()->setCellValue("O" . $filaInicial, $vectorConferencia["DINNER_GUESTS"]);
            $spreadsheet->getActiveSheet()->setCellValue("P" . $filaInicial, $vectorConferencia["RECEPTION_GUESTS"]);
            $spreadsheet->getActiveSheet()->setCellValue("Q" . $filaInicial, $vectorConferencia["AMOUNT"]);
            $spreadsheet->getActiveSheet()->setCellValue("R" . $filaInicial, $vectorConferencia["PAID"]);
            $spreadsheet->getActiveSheet()->setCellValue("S" . $filaInicial, $vectorConferencia["ATTENDED"]);
            $spreadsheet->getActiveSheet()->setCellValue("T" . $filaInicial, $vectorConferencia["COMMENT_EXCEL"]);
			$spreadsheet->getActiveSheet()->setCellValue("U" . $filaInicial, $vectorConferencia["ATTENDEE_LIST"]);
            $spreadsheet->getActiveSheet()->setCellValue("V" . $filaInicial, $vectorConferencia["PASSWORD"]);
			$spreadsheet->getActiveSheet()->setCellValue("W" . $filaInicial, $vectorConferencia["FIRST_TIMER"]);
            $filaInicial++;

        } elseif (isset($_GET["excel"]) && $_GET["excel"] == 2) {
            $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["REGISTRATION_NUMBER"]);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["NAME_CERTIFICATE"]);
            $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorConferencia["SPEAKER"]);
            $spreadsheet->getActiveSheet()->setCellValue("D" . $filaInicial, $vectorConferencia["PAID"]);
            $spreadsheet->getActiveSheet()->setCellValue("E" . $filaInicial, $vectorConferencia["COMMENT_EXCEL"]);
            $vectorConferencia["WORKSHOP_TITLE"] = "";

            $resultWorkshops = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_taller_attended_obtener(" . $dato["id_inscripcion_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");
            while ($oneWorkshop = $db->getData($resultWorkshops)) {
                $vectorConferencia["WORKSHOP_TITLE"] .= $oneWorkshop["nombre_largo"] . " \r\n";
            }
            $spreadsheet->getActiveSheet()->setCellValue("F" . $filaInicial, $vectorConferencia["WORKSHOP_TITLE"]);
            $filaInicial++;


        } elseif (isset($_GET["excel"]) && $_GET["excel"] == 5) {
            $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["EMAIL"]);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["FULL_NAME"]);
            $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorConferencia["FIRSTNAME"]);

            $resultWorkshops = $db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_taller_obtener(" . $dato["id_inscripcion_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");

            $vectorConferencia["WORKSHOPS"] = '';

            while ($workshopDay = $db->getData($resultWorkshops)) {
                $numericDate = $workshopDay["fecha"];
                //Create an array of year, month and day
                $yearMonthDay = explode("-", $numericDate);
                //Create a timestamp from date
                $workshopDateTimeStamp = mktime(0, 0, 0, $yearMonthDay[1], $yearMonthDay[2], $yearMonthDay[0]);
                //Generate formatted date from timestamp
                $workshopDate = date('l, j F', $workshopDateTimeStamp);
                //Store date in workshops variable
                $vectorConferencia["WORKSHOPS"] .= $workshopDate . "\n";

                $workshopTitle = $workshopDay["nombre_largo"];
                //Add workshop title to workshops variable
                //Included space between linefeeds to force line break in Word
                $vectorConferencia["WORKSHOPS"] .= $workshopTitle . "\n \n";

                $spreadsheet->getActiveSheet()->setCellValue("D" . $filaInicial, $vectorConferencia["WORKSHOPS"]);
            }
            $filaInicial++;
        }// end Excel-5
    }// end Excel
    $i++;  // next signup
}// end while

$i = 0;

//Badges
if (isset($_GET["excel"]) && $_GET["excel"] == 3) {
    $fichero = "Badges.xls";

    //Set header row
    $spreadsheet->getActiveSheet()->setCellValue("A1", "Name");
    $spreadsheet->getActiveSheet()->setCellValue("B1", "Affiliation");

    //Format header row and columns (width, wrap text, vertical alignment)
    $spreadsheet->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(60);
    $spreadsheet->getActiveSheet()->getStyle('A1:B500')->getAlignment()->setWrapText(true);
    $spreadsheet->getActiveSheet()->getStyle('A1:B500')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

    //Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);

    //Rename worksheet
    $spreadsheet->getActiveSheet()->setTitle('Badges');

    $filaInicial = 2;

    $resultBadges = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_pr_get_conference_badges(" . $_GET["id_conferencia"] . ")");

    while ($oneBadge = $db->getData($resultBadges)) {
        $badgeLines = explode("\n", $oneBadge['conference_badge']);
        if (isset($badgeLines[0])) {
            $line1 = strip_tags($badgeLines[0]);
        } else {
            $line1 = "";
        }
        if (isset($badgeLines[1])) {
            $line2 = strip_tags($badgeLines[1]);
        } else {
            $line2 = "";
        }
        if (isset($badgeLines[2])) {
            $line3 = strip_tags($badgeLines[2]);
        } else {
            $line3 = "";
        }

        $vectorConferencia["BADGE_NAME"] = $line1;
        $vectorConferencia["BADGE_AFFILIATION"] = $line2 . "\n" . $line3;

        $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["BADGE_NAME"]);
        $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["BADGE_AFFILIATION"]);
        $filaInicial++;
    }//end while
}//end Excel-3

//Conference workshops
if (isset($_GET["excel"]) && $_GET["excel"] == 4) {
    $fichero = "Workshops.xls";

    //Get list of conference workshop IDs
    $conferenceWorkshops = $db->callProcedure("CALL ed_pr_get_conference_workshops(" . $_GET["id_conferencia"] . ")");

    //Rename worksheet
    $spreadsheet->getActiveSheet()->setTitle('All workshops');

    //Insert column headers and set column widths
    $spreadsheet->getActiveSheet()->setCellValue("A1", "Workshop");
    $spreadsheet->getActiveSheet()->setCellValue("B1", "Name");
    $spreadsheet->getActiveSheet()->setCellValue("C1", "Email");
    $spreadsheet->getActiveSheet()->setCellValue("D1", "Paid");

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(70);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);

    //Format header column
    $spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold(true);

    //Start filling data at row 2
    $filaInicial = 2;

    //Loop through array of workshop IDs, all in the same worksheet
    while ($allWorkshops = $db->getData($conferenceWorkshops)) {

        //Get array of signups for current workshop
        $conferenceWorkshopSignups = $db->callProcedure("CALL ed_pr_get_conference_workshop_signups(" . $_GET["id_conferencia"] . "," . $allWorkshops["id_taller_fecha"] . ")");

        //Loop through signups (rows) for current workshop
        while ($oneSignup = $db->getData($conferenceWorkshopSignups)) {

            //Convert lowercase names to first letter uppercase
            $oneSignup["apellidos"] = ucfirst($oneSignup["apellidos"]);
            $oneSignup["nombre"] = ucfirst($oneSignup["nombre"]);

            //Store data for current row in an array
            $vectorConferencia["WORKSHOP_TITLE"] = $oneSignup["nombre_largo"];
            $vectorConferencia["WORKSHOP_PARTICIPANT"] = $oneSignup["apellidos"] . ", " . $oneSignup["nombre"];
            $vectorConferencia["WORKSHOP_EMAIL"] = $oneSignup["correo_electronico"];
            if ($oneSignup["pagado"] == 1) {
                $vectorConferencia["WORKSHOP_PAID"] = "Yes";
            } else {
                $vectorConferencia["WORKSHOP_PAID"] = "No";
            }

            //Assign array elements to cells in spreadsheet at row number $filaInicial
            $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["WORKSHOP_TITLE"]);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["WORKSHOP_PARTICIPANT"]);
            $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorConferencia["WORKSHOP_EMAIL"]);
            $spreadsheet->getActiveSheet()->setCellValue("D" . $filaInicial, $vectorConferencia["WORKSHOP_PAID"]);
            $filaInicial++;

        }//end while

    }//end while

    //Create new worksheet
    $worksheet = $spreadsheet->createSheet();
    $worksheet->setTitle('Workshop-1');
    $spreadsheet->setActiveSheetIndexByName('Workshop-1');

    //Next new worksheet is 'Workshop-2';
    $a = 2;

    //Get list of conference workshop IDs (repeat DB call)
    $separateWorkshops = $db->callProcedure("CALL ed_pr_get_conference_workshops(" . $_GET["id_conferencia"] . ")");

    //Get array of workshop IDs and go through workshops one by one, each in a new worksheet
    while ($oneWorkshop = $db->getData($separateWorkshops)) {

        //Insert workshop title in current worksheet
        $conferenceWorkshopTitle = $oneWorkshop["nombre_largo"];
        $spreadsheet->getActiveSheet()->setCellValue("A1", $conferenceWorkshopTitle);
        //Format title
        $spreadsheet->getActiveSheet()->getStyle("A1:C3")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("A1")->getFont()->setSize(14);

        //Insert column headers and set column widths
        $spreadsheet->getActiveSheet()->setCellValue("A3", "Name");
        $spreadsheet->getActiveSheet()->setCellValue("B3", "Email");
        $spreadsheet->getActiveSheet()->setCellValue("C3", "Paid");

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);

        //Get array of signups for the current workshop
        $conferenceWorkshopSignups = $db->callProcedure("CALL ed_pr_get_conference_workshop_signups(" . $_GET["id_conferencia"] . "," . $oneWorkshop["id_taller_fecha"] . ")");

        //Start filling data at row 5
        $filaInicial = 5;

        //Get rows (signups) one by one
        while ($oneSignup = $db->getData($conferenceWorkshopSignups)) {

            //Convert all lowercase names to first letter uppercase
            $oneSignup["apellidos"] = ucfirst($oneSignup["apellidos"]);
            $oneSignup["nombre"] = ucfirst($oneSignup["nombre"]);

            //Store data for each row in an array
            $vectorConferencia["WORKSHOP_PARTICIPANT"] = $oneSignup["apellidos"] . ", " . $oneSignup["nombre"];
            $vectorConferencia["WORKSHOP_EMAIL"] = $oneSignup["correo_electronico"];
            if ($oneSignup["pagado"] == 1) {
                $vectorConferencia["WORKSHOP_PAID"] = "Yes";
            } else {
                $vectorConferencia["WORKSHOP_PAID"] = "No";
            }

            //Assign row to spreadsheet at row number $filaInicial
            $spreadsheet->getActiveSheet()->setCellValue("A" . $filaInicial, $vectorConferencia["WORKSHOP_PARTICIPANT"]);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $filaInicial, $vectorConferencia["WORKSHOP_EMAIL"]);
            $spreadsheet->getActiveSheet()->setCellValue("C" . $filaInicial, $vectorConferencia["WORKSHOP_PAID"]);
            $filaInicial++;

        }//end while

        //Create new worksheet
        $worksheet = $spreadsheet->createSheet();
        $worksheet->setTitle('Workshop-' . $a);
        $spreadsheet->setActiveSheetIndexByName('Workshop-' . $a);
        $a++;

    }//end while

}//end Excel-4

//Output Excel
if (isset($_GET["excel"]) && $_GET["excel"] > 0) {
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Rename worksheet
    //$spreadsheet->getActiveSheet()->setTitle('Members');
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


if (!isset($_GET["excel"]) || (isset($_GET["excel"]) && $_GET["excel"] == 0)) {

    $matriz[1]["descripcion"] = STATIC_GLOBAL_BUTTON_YES;
    $matriz[0]["descripcion"] = STATIC_GLOBAL_BUTTON_NO;

    //Get details of specified conference
    $resultadoConferencia = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_conferencia_obtener_concreta(" . $_GET["id_conferencia"] . "," . $_SESSION["user"]["language_id"] . ")");
    $datoConferencia = $db->getData($resultadoConferencia);

    //Conference ID (for link to edit workshop signups)
    $subPlantilla->assign("ID_CONFERENCIA", $_GET["id_conferencia"]);

    //Registration status dropdown
    $subPlantilla->assign("COMBO_ESTADO_INSCRIPCION", generalUtils::construirCombo($db, "CALL " . OBJECT_DB_ACRONYM . "_sp_estado_inscripcion_conferencia_buscador_obtener_combo()", "cmbEstadoInscripcion", "cmbEstadoInscripcion", $idEstadoInscripcion, "descripcion", "id_estado_inscripcion", "", 0, ""));

    //User type dropdown (member, non-member, sister association member)
    $matriz[3]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
    $matriz[2]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
    $matriz[1]["descripcion"] = STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
    $matriz[0]["descripcion"] = STATIC_GLOBAL_COMBO_DEFAULT;

    $subPlantilla->assign("COMBO_USER_TYPE", generalUtils::construirComboMatriz($matriz, "cmbTipoUsuario", "cmbTipoUsuario", $idTipoUsuario, "", -1, ""));

    //Breadcrumbs
    $vectorMigas[0]["url"] = STATIC_BREADCUMB_INICIO_LINK;
    $vectorMigas[0]["texto"] = STATIC_BREADCUMB_INICIO_TEXT;
    $vectorMigas[1]["url"] = STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK;
    $vectorMigas[1]["texto"] = STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_TEXT;
    $vectorMigas[2]["url"] = STATIC_BREADCUMB_CONFERENCE_EDIT_CONFERENCE_LINK . "&id_conferencia=" . $_GET["id_conferencia"];
    $vectorMigas[2]["texto"] = $datoConferencia["nombre"];
    $vectorMigas[3]["url"] = STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_LINK . "&id_conferencia=" . $_GET["id_conferencia"];
    $vectorMigas[3]["texto"] = STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_TEXT;

    require "includes/load_breadcumb.inc.php";

    //Informacion del usuario
    require "includes/load_information_user.inc.php";

    if ($datoConferencia["activo"] == 1) {
        $subPlantilla->parse("contenido_principal.boton_edit_inscripcion");
        if ($idEstadoInscripcion == 2) {
            $subPlantilla->parse("contenido_principal.boton_cancel_inscripcion");
        } else {
            $subPlantilla->parse("contenido_principal.boton_reactivate_inscripcion");
        }
    }//end if

    //Contruimos plantilla secundaria
    $subPlantilla->parse("contenido_principal");

    //Exportamos plantilla secundaria a la plantilla principal
    $plantilla->assign("CONTENIDO", $subPlantilla->text("contenido_principal"));

    //Construimos plantilla principal
    $plantilla->parse("contenido_principal");

    //Mostramos plantilla principal por pantalla
    $plantilla->out("contenido_principal");

}