<?php
//conexio bdd


header("Content-Type: text/html; charset=utf-8");
/**
 *
 * Indicamos la ruta absoluta en donde esta alojado el fichero.
 * @var string
 */


require "../../../classes/databaseConnection.php";
require "../../../classes/generalUtils.php";
require "../../classes/gestorGeneralUtils.php";
require "../../../database/connection.php";
require "../../../includes/load_template.inc.php";


//Get dictionary
require "../../config/dictionary/en_EN.php";

//Load HTMl to PDF converter
//require "../../classes/html2pdf/html2pdf.class.php"; //Old Html2Pdf class, now obsolete
require_once('../vendor/autoload.php');

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

//$members='1561,1264';
//$importe=30;
//$id_mov=2;
$members = $_GET["members"];
$importe = $_GET["importe"];
$id_mov = $_GET["mov"];

//echo $members ."<br>";
//echo $importe ."<br>";
//echo $id_mov ."<br>";
//die();

//Start loop to generate invoices
$facturasPDF = $db->callProcedure("CALL ed_sp_auto_renew_member_institution('" . $members . "'," . $importe . "," . $id_mov . ")");
$numFactura = $db->getNumberRows($facturasPDF);
while ($numFactura = $db->getData($facturasPDF)) {
    //Plantilla pdf
    $plantillaPdf = new XTemplate("../../html/sections/invoice/generate_invoice_massive.html");

    $id_factura = $numFactura["id_factura"];
    //.$_GET["id_factura"]

    $id_user_language_id = "1";
    //.$_SESSION["user"]["language_id"].

    //echo $id_factura;
    //Get invoice details
    $resultadoFactura = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_obtener_concreto_pdf(" . $id_factura . ")");
    $datoFactura = $db->getData($resultadoFactura);

    $plantillaPdf->assign("INVOICE_PDF_DATE_VALUE", generalUtils::conversionFechaFormato($datoFactura["fecha_factura"], "-", "/"));
    $plantillaPdf->assign("INVOICE_PDF_INVOICE_NUMBER_VALUE", $datoFactura["numero_factura"]);
    //echo $datoFactura["numero_factura"];

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
    $plantillaPdf->assign("INVOICE_PDF_PAYMENT_RECEIVED_VALUE", generalUtils::conversionFechaFormato($datoFactura["fecha_pago_factura"], "-", "/"));

    //Get billing details
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
    // Use pais_factura if available, otherwise try to get country name from tax_id_country
    $displayCountry = $datoFactura["pais_factura"];
    if (empty($displayCountry) && !empty($datoFactura["tax_id_country"])) {
        $countryResult = $db->callProcedure("CALL ed_sp_web_pais_get_name_from_iso('" . $datoFactura["tax_id_country"] . "')");
        if ($countryRow = $db->getData($countryResult)) {
            $displayCountry = $countryRow["nombre_original"];
        }
    }
    $direccion .= $datoFactura["ciudad_factura"] . $provincia . " <br>" . $displayCountry . "<br>";

    //echo $direccion;
    //die();
    $plantillaPdf->assign("INVOICE_PDF_BILLING_ADDRESS_VALUE", $direccion);

    $resultadoFactura = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_linea_factura_obtener_concreto_pdf(" . $id_factura . "," . $id_user_language_id . ")");
    $total = 0;

    while ($datoLineaFactura = $db->getData($resultadoFactura)) {
        $plantillaPdf->assign("INVOICE_PDF_ITEMS_TITLE_VALUE", $datoLineaFactura["mas_informacion"]);
        $total += $datoLineaFactura["precio"];
        $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . $datoLineaFactura["precio"]);
        $plantillaPdf->parse("contenido_principal.item_linea_factura");
    }

    $plantillaPdf->assign("INVOICE_PDF_AMOUNT_TITLE_VALUE", "&euro; " . sprintf("%.2f", $total));
    $plantillaPdf->parse("contenido_principal");


    //Convert HTML to PDF or output error message if conversion fails
    try {
        $html2pdf = new Html2Pdf('P', 'A4', 'en');
        $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"));
        $hashGenerado = md5(uniqid(time()) . $datoFactura["numero_factura"]);

        //Update invoice
        $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_actualizar_hash(" . $_GET["id_factura"] . ",'" . $hashGenerado . "')");

        //Output PDF to file (F), then move file to /easygestor/files/customers/invoice/pdf folder
        $html2pdf->Output(__DIR__ . $datoFactura["numero_factura"] . ".pdf", "F");
        rename(__DIR__ . $datoFactura["numero_factura"] . ".pdf", "../files/customers/invoice/pdf/" . $datoFactura["numero_factura"] . ".pdf");

    } catch (Html2PdfException $e) {
        $html2pdf->clean();

        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
        exit;
    }

    /*    try {
            $html2pdf = new HTML2PDF("P", "A4", "es");
            $html2pdf->writeHTML($plantillaPdf->text("contenido_principal"), false);
            // $html2pdf->Output("../files/customers/invoice/pdf/".$datoFactura["numero_factura"].".pdf","F");
            //Calculamos hash
            $hashGenerado = md5(uniqid(time()) . $datoFactura["numero_factura"]);
            //Actualizar factura
            $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_actualizar_hash(" . $id_factura . ",'" . $hashGenerado . "')");


            //$html2pdf->Output($datoFactura["numero_factura"].".pdf","FI");
            //Comento la I perque no surti per pantalla
            $html2pdf->Output($datoFactura["numero_factura"] . ".pdf", "F");
            rename($datoFactura["numero_factura"] . ".pdf", "../../../files/customers/invoice/pdf/" . $datoFactura["numero_factura"] . ".pdf");
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }*/

    //End while loop for generating invoices
}