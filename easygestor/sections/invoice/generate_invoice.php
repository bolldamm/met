<?php
/*
 * Create invoice using HTML template,
 * convert HTML to PDF, then
 * output PDF to screen and to file
 */

// Suppress deprecation and warning messages from Html2Pdf/TCPDF libraries (PHP 8.2+ compatibility)
error_reporting(0);

require_once('../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../../includes/VerifactiService.php');

use \Spipu\Html2Pdf\Html2Pdf;
use \Spipu\Html2Pdf\Exception\Html2PdfException;
use \Spipu\Html2Pdf\Exception\ExceptionFormatter;

//Get HTML invoice template
$plantillaPdf = new XTemplate("html/sections/invoice/generate_invoice.html");

//Initialize VerifactiService for Verifactu compliance
$verifactiService = new VerifactiService($db);

//Get invoice details
$resultadoFactura = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_obtener_concreto_pdf(" . $_GET["id_factura"] . ")");
$datoFactura = $db->getData($resultadoFactura);

//Check if invoice already has Verifactu registration (previously submitted)
//Note: We do NOT submit to Verifacti here - that happens at send time for compliance
$verifactuEnabled = false;
$verifactuShowPlaceholder = false;
if ($verifactiService->isEnabled() && $datoFactura["proforma"] != 1) {
    if (!empty($datoFactura["verifactu_uuid"])) {
        //Already registered - show QR
        $verifactuEnabled = true;
    } else {
        //Not yet registered - show placeholder
        $verifactuShowPlaceholder = true;
    }
}

//Assign invoice details to placeholders in template
$plantillaPdf->assign("INVOICE_PDF_DATE_VALUE", generalUtils::conversionFechaFormato($datoFactura["fecha_factura"], "-", "/"));
if ($datoFactura["proforma"] == 1) {
    $plantillaPdf->assign("INVOICE_PROFORMA", "PRO FORMA INVOICE");
} elseif ($datoFactura["proforma"] == 2) {
    $plantillaPdf->assign("INVOICE_PROFORMA", "CREDIT NOTE");
} else {
    $plantillaPdf->assign("INVOICE_PROFORMA", "INVOICE");
}
$plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE", $datoFactura["numero_factura"]);

//Display tax ID with appropriate country prefix
//Spanish/EU: show with country prefix (ES-12345678A, BE-0404621642)
//Non-EU: show without prefix
//F2 simplified invoices: show N/A (no tax ID required)
$customerTaxId = "";

//For F2 simplified invoices, just show N/A - no tax ID processing needed
if (!empty($datoFactura["tipo_factura_verifactu"]) && $datoFactura["tipo_factura_verifactu"] === "F2") {
    $customerTaxId = "N/A";
} else {
    $taxIdNumber = !empty($datoFactura["tax_id_number"])
        ? $datoFactura["tax_id_number"]
        : ($datoFactura["nif_cliente_factura"] ?? "");
    $taxIdCountry = strtoupper($datoFactura["tax_id_country"] ?? "");

    //EU VIES countries (excluding Spain which uses ES)
    $viesCountries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'FI', 'FR',
        'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT',
        'RO', 'SE', 'SI', 'SK'];

    if (!empty($taxIdNumber)) {
        $isSpanish = empty($taxIdCountry) || $taxIdCountry === "ES";
        $isEu = in_array($taxIdCountry, $viesCountries);

        if ($isSpanish) {
            //Spanish NIF: always show with ES- prefix
            $customerTaxId = "ES-" . $taxIdNumber;
        } elseif ($isEu) {
            //EU VAT: show with country prefix, avoiding duplication
            //Strip country code from number if already present
            if (strpos(strtoupper($taxIdNumber), $taxIdCountry) === 0) {
                $taxIdNumber = substr($taxIdNumber, strlen($taxIdCountry));
            }
            $customerTaxId = $taxIdCountry . "-" . $taxIdNumber;
        } else {
            //Non-EU: show without prefix
            $customerTaxId = $taxIdNumber;
        }
    } else {
        $customerTaxId = "N/A";
    }
}

$plantillaPdf->assign("INVOICE_PDF_CUSTOMER_CIF_VALUE", $customerTaxId);
if ($datoFactura["fecha_pago_factura"] != "") {
    $plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE", generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"], "-", "/"));
}

//Get billing address and assign to placeholder
$direccion = "";
if ($datoFactura["visible_nombre_cliente_factura"] == 1) {
    $direccion .= $datoFactura["nombre_cliente_factura"] . "<br>";
}
if ($datoFactura["visible_nombre_empresa_factura"] == 1) {
    $direccion .= $datoFactura["nombre_empresa_factura"] . "<br>";
}
if ($datoFactura["provincia_factura"] != "") {
    $provincia = " (" . $datoFactura["provincia_factura"] . ")";
} else {
    $provincia = "";
}
$direccion .= $datoFactura["direccion_factura"] . "<br>";
$direccion .= $datoFactura["codigo_postal_factura"] . "<br>";
// Get country name from tax_id_country (ISO-2 code) - the authoritative source
$displayCountry = "";
if (!empty($datoFactura["tax_id_country"])) {
    // Convert ISO-2 code to full country name
    $countryResult = $db->callProcedure("CALL ed_sp_web_pais_get_name_from_iso('" . $datoFactura["tax_id_country"] . "')");
    if ($countryRow = $db->getData($countryResult)) {
        $displayCountry = $countryRow["nombre_original"];
    }
}
// Fallback to pais_factura for legacy invoices that haven't been migrated
if (empty($displayCountry) && !empty($datoFactura["pais_factura"])) {
    $displayCountry = $datoFactura["pais_factura"];
}
$direccion .= $datoFactura["ciudad_factura"] . $provincia . " <br>" . $displayCountry . "<br>";

$plantillaPdf->assign("INVOICE_PDF_BILLING_ADDRESS_VALUE", $direccion);

//Get invoice items (workshop = 55, custom = 46, or conference) and assign to placeholders
$resultadoFactura = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_linea_factura_obtener_concreto_pdf(" . $_GET["id_factura"] . "," . $_SESSION["user"]["language_id"] . ")");
$total = 0;
while ($datoLineaFactura = $db->getData($resultadoFactura)) {
    if ($datoLineaFactura["id_concepto_movimiento"] == 55) {
        //Workshops heading
        $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", "<strong>" . STATIC_INVOICE_PDF_WORKSHOP_ROW_TITLE . "</strong>");
        $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "");
        $plantillaPdf->assign("ESTILO_CONCRETO", "");

        $plantillaPdf->parse("contenido_principal.item_linea_factura");

        $resultadoListadoTaller = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_inscripcion_taller_obtener_concreto_pdf(" . $datoLineaFactura["id_movimiento"] . "," . $_SESSION["user"]["language_id"] . ")");
        while ($datoListadoTaller = $db->getData($resultadoListadoTaller)) {

            $total += $datoListadoTaller["importe"];
            $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", $datoListadoTaller["nombre"]);
            $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . $datoListadoTaller["importe"]);
            $plantillaPdf->assign("ESTILO_CONCRETO", "padding-left:30px;");


            $plantillaPdf->parse("contenido_principal.item_linea_factura");
        }
    } else if ($datoLineaFactura["id_concepto_movimiento"] == 46) {
        $total += $datoLineaFactura["precio"];
        if ($datoLineaFactura["concepto_personalizado"] != "") {
            $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", $datoLineaFactura["concepto_personalizado"]);
        } else {
            //$plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE",STATIC_INVOICE_METM_CURRENT);
            $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", "METM" . date(y) . " conference fee");
        }
        $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . $datoLineaFactura["precio"]);

        $plantillaPdf->parse("contenido_principal.item_linea_factura");
    } else {
        $total += $datoLineaFactura["precio"];
        if ($datoLineaFactura["concepto_personalizado"] != "") {
            $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", $datoLineaFactura["concepto_personalizado"]);
        } else {
            $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", $datoLineaFactura["concepto"]);
        }
        $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . $datoLineaFactura["precio"]);

        $plantillaPdf->parse("contenido_principal.item_linea_factura");
    }
}

$plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . sprintf("%.2f", $total));

//Add Verifactu QR code section if already registered (only for real invoices, not proformas)
if ($verifactuEnabled && !empty($datoFactura["verifactu_qr"])) {
    $plantillaPdf->assign("VERIFACTU_UUID", $datoFactura["verifactu_uuid"]);
    $plantillaPdf->assign("VERIFACTU_HUELLA", $datoFactura["verifactu_huella"]);

    //QR code can be base64 data or URL
    $qrCode = $datoFactura["verifactu_qr"];
    if (strpos($qrCode, "data:image") === 0 || strpos($qrCode, "http") === 0) {
        $plantillaPdf->assign("VERIFACTU_QR_IMAGE", $qrCode);
    } else {
        //Assume it's base64 data without the data URI prefix
        $plantillaPdf->assign("VERIFACTU_QR_IMAGE", "data:image/png;base64," . $qrCode);
    }

    $plantillaPdf->parse("contenido_principal.verifactu_qr_section");
} elseif ($verifactuShowPlaceholder) {
    //Show placeholder for invoices not yet registered with Verifactu
    $plantillaPdf->parse("contenido_principal.verifactu_placeholder_section");
}

$plantillaPdf->parse("contenido_principal");

//Convert HTML to PDF, else output error message
try {
    $html2pdf = new Html2Pdf('P', 'A4', 'en');
    $html2pdf->setDefaultFont('freesans');
    $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"));

    //Only generate new hash if one doesn't exist (preserve hash for QR regeneration)
    if (empty($datoFactura["hash_generado"])) {
        $hashGenerado = md5(uniqid(time()) . $datoFactura["numero_factura"]);
        $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_actualizar_hash(" . $_GET["id_factura"] . ",'" . $hashGenerado . "')");
    }

    //Output PDF to file (F) and screen (I), then move to /easygestor/files/customers/invoice/pdf folder

    if (isset($_GET["ioutput"]) && $_GET["ioutput"]) {
        $invoiceOutput = "F";
    } else {
        $invoiceOutput = "FI";
    }

    $html2pdf->Output(__DIR__ . $datoFactura["numero_factura"] . ".pdf", $invoiceOutput);
    rename(__DIR__ . $datoFactura["numero_factura"] . ".pdf", "../files/customers/invoice/pdf/" . $datoFactura["numero_factura"] . ".pdf");

} catch (Html2PdfException $e) {
    $html2pdf->clean();

    $formatter = new ExceptionFormatter($e);
    echo $formatter->getHtmlMessage();
    exit;
}
