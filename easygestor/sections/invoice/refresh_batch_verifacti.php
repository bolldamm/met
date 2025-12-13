<?php
/**
 * Script to refresh Verifacti status for all selected invoices
 * @Author Claude
 */

require "../../includes/load_main_components.inc.php";
require "../../includes/load_validate_user.inc.php";
require "../../config/constants.php";
require "../../config/dictionary/" . $_SESSION["user"]["language_dictio"];
require "../../../includes/VerifactiService.php";

// Parse the comma-separated invoice IDs
$vectorItem = array_filter(explode(",", $_POST["hdnId"]));
$totalItem = count($vectorItem);

if ($totalItem == 0) {
    echo "<p>No invoices selected.</p>";
    echo "<p><a href='main_app.php?section=invoice&action=view'>Back to invoice list</a></p>";
    exit;
}

// Initialize Verifacti service
$verifactiService = new VerifactiService($db);

if (!$verifactiService->isEnabled()) {
    echo "<p>Verifacti service is not enabled.</p>";
    echo "<p><a href='main_app.php?section=invoice&action=view'>Back to invoice list</a></p>";
    exit;
}

// HTML output with progress
echo "<!DOCTYPE html><html><head><title>Refresh Verifacti Status</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .skipped { color: #6c757d; }
    .progress-container { width: 100%; background-color: #ddd; margin: 10px 0; }
    .progress-bar { width: 0%; height: 30px; background-color: #4CAF50; text-align: center; line-height: 30px; color: white; }
    table { border-collapse: collapse; margin-top: 20px; }
    td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f4f4f4; }
</style></head><body>";

echo "<h2>Refreshing Verifacti Status</h2>";
echo "<p>Processing " . $totalItem . " invoice(s)...</p>";
echo "<div class='progress-container'><div class='progress-bar' id='progressBar'>0%</div></div>";
echo "<table><tr><th>Invoice #</th><th>Status</th><th>Result</th></tr>";

// Flush output
ob_flush();
flush();

$successCount = 0;
$errorCount = 0;
$skippedCount = 0;

// Process each invoice
for ($i = 0; $i < $totalItem; $i++) {
    $idFactura = intval($vectorItem[$i]);

    // Get invoice data
    $resultadoFactura = $db->callProcedure("CALL " . OBJECT_DB_ACRONYM . "_sp_factura_obtener_concreto(" . $idFactura . ")");
    $datoFactura = $db->getData($resultadoFactura);

    $invoiceNumber = $datoFactura["numero_factura"] ?? "Unknown";

    // Check if invoice has Verifactu UUID
    if (empty($datoFactura["verifactu_uuid"])) {
        echo "<tr><td>" . htmlspecialchars($invoiceNumber) . "</td><td class='skipped'>No UUID</td><td>Skipped - not submitted to Verifacti</td></tr>";
        $skippedCount++;
    } else {
        // Refresh the status
        $result = $verifactiService->getInvoiceStatus($idFactura);

        if ($result !== false) {
            $newStatus = $result["estado"] ?? "Unknown";
            echo "<tr><td>" . htmlspecialchars($invoiceNumber) . "</td><td class='success'>" . htmlspecialchars($newStatus) . "</td><td>Updated successfully</td></tr>";
            $successCount++;
        } else {
            $error = $verifactiService->getLastError();
            echo "<tr><td>" . htmlspecialchars($invoiceNumber) . "</td><td class='error'>Error</td><td>" . htmlspecialchars($error) . "</td></tr>";
            $errorCount++;
        }
    }

    // Update progress bar
    $progress = round((($i + 1) / $totalItem) * 100);
    echo "<script>document.getElementById('progressBar').style.width = '" . $progress . "%'; document.getElementById('progressBar').innerHTML = '" . $progress . "%';</script>";
    ob_flush();
    flush();
}

echo "</table>";

// Summary
echo "<h3>Summary</h3>";
echo "<p><span class='success'>Success: " . $successCount . "</span> | ";
echo "<span class='error'>Errors: " . $errorCount . "</span> | ";
echo "<span class='skipped'>Skipped: " . $skippedCount . "</span></p>";

// Redirect back after a delay
echo "<p>Redirecting back to invoice list in 3 seconds...</p>";
echo "<script>setTimeout(function(){ window.location = '../../main_app.php?section=invoice&action=view&reload=" . rand() . "'; }, 3000);</script>";
echo "<p><a href='../../main_app.php?section=invoice&action=view'>Click here if not redirected</a></p>";

echo "</body></html>";
