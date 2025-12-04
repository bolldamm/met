<?php
/**
 * AJAX endpoint to cancel an invoice in Verifacti (Anular factura)
 *
 * @param int id_factura - The invoice ID to cancel
 */

require "../includes/load_main_components.inc.php";
require "../includes/load_validate_user.inc.php";
require "../../includes/VerifactiService.php";

header('Content-Type: application/json');

// Validate invoice ID
if (!isset($_POST['id_factura']) || !is_numeric($_POST['id_factura'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid invoice ID'
    ]);
    exit;
}

$idFactura = intval($_POST['id_factura']);

// Initialize Verifacti service
$verifactiService = new VerifactiService($db);

// Check if service is enabled
if (!$verifactiService->isEnabled()) {
    echo json_encode([
        'success' => false,
        'error' => 'Verifacti service is not enabled'
    ]);
    exit;
}

// Attempt to cancel the invoice
$result = $verifactiService->cancelInvoice($idFactura);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Invoice cancelled successfully in Verifacti'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $verifactiService->getLastError()
    ]);
}
