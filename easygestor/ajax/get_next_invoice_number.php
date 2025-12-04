<?php
/**
 * AJAX endpoint to get the next suggested invoice number
 * Returns the next available invoice number based on the selected type
 *
 * @param string type - 'invoice' (default), 'proforma', or 'creditnote'
 */

require "../includes/load_main_components.inc.php";
require "../includes/load_validate_user.inc.php";

header('Content-Type: application/json');

// Get the invoice type from request
$type = isset($_GET['type']) ? $_GET['type'] : 'invoice';

// Get the last invoice number from database
$resultadoFacturaLast = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_last()");
$datoFacturaLast = $db->getData($resultadoFacturaLast);

$maxNumero = $datoFacturaLast["max_numero_factura"] ?? "";

// Extract the numeric part (after last hyphen)
$nextNum = 1;
if (!empty($maxNumero)) {
    // Handle formats: "2025-001", "PF-2025-001", "CN-2025-001"
    if (preg_match('/(\d+)$/', $maxNumero, $matches)) {
        $nextNum = intval($matches[1]) + 1;
    }
}

// Build the suggested number based on type
$year = date("Y");
switch ($type) {
    case 'proforma':
        $suggestedNumber = "PF-" . $year . "-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        break;
    case 'creditnote':
        $suggestedNumber = "CN-" . $year . "-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        break;
    case 'invoice':
    default:
        $suggestedNumber = $year . "-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        break;
}

echo json_encode([
    'success' => true,
    'number' => $suggestedNumber,
    'lastNumber' => $maxNumero
]);
