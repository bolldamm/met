<?php
/**
 * AJAX endpoint to refresh Verifacti status for an invoice
 *
 * Queries the Verifacti API for the current status and updates the database
 *
 * @param int id_factura - The invoice ID to check
 */

require "../includes/load_main_components.inc.php";
require "../includes/load_validate_user.inc.php";
require "../../includes/VerifactiService.php";

header('Content-Type: application/json');

// Validate invoice ID
if (!isset($_GET['id_factura']) || !is_numeric($_GET['id_factura'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid invoice ID'
    ]);
    exit;
}

$idFactura = intval($_GET['id_factura']);

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

// Query the status
$result = $verifactiService->getInvoiceStatus($idFactura);

if ($result !== false) {
    // Map status for display
    $displayStatus = "Unknown";
    if (isset($result["estado"])) {
        $displayStatus = $result["estado"];
    }

    echo json_encode([
        'success' => true,
        'status' => $displayStatus,
        'data' => $result,
        'message' => 'Status refreshed successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $verifactiService->getLastError()
    ]);
}
