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
require_once('../../../vendor/autoload.php');

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

//$members='1561,1264';
//$importe=30;
//$id_mov=2;

$members = $_GET["members"];
$importe = $_GET["importe"];
$id_mov = $_GET["mov"];

// echo $members ."<br>";
// echo $importe ."<br>";
// echo $id_mov ."<br>";
// die();

//Start loop to generate invoices

$facturasPDF = $db->callProcedure("CALL ed_sp_auto_renew_member('" . $members . "'," . $importe . "," . $id_mov . ")");
$numFactura = $db->getNumberRows($facturasPDF);
while ($numFactura = $db->getData($facturasPDF)) {
    //HTML template for conversion to PDF
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

    //Display Spanish NIF if available, otherwise show foreign tax ID
    $customerTaxId = "";
    if (!empty($datoFactura["nif_cliente_factura"])) {
        $customerTaxId = $datoFactura["nif_cliente_factura"];
    } elseif (!empty($datoFactura["tax_id_number"])) {
        $customerTaxId = $datoFactura["tax_id_number"];
        if (!empty($datoFactura["tax_id_country"])) {
            $customerTaxId = $datoFactura["tax_id_country"] . " - " . $customerTaxId;
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
    $direccion .= $datoFactura["ciudad_factura"] . $provincia . " <br>" . $datoFactura["pais_factura"] . "<br>";

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