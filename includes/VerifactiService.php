<?php
/**
 * VerifactiService - Handles communication with Verifacti API for Verifactu compliance
 *
 * This service sends invoice data to Verifacti and stores the response
 * (UUID, QR code, huella, etc.) in the database.
 *
 * @author Edata S.L.
 * @version 2.0
 */

class VerifactiService
{
    private $apiKey;
    private $apiUrl;
    private $db;
    private $lastError;
    private $logFile;

    // VIES countries (EU member states for VAT purposes, excluding Spain)
    // These require country code prefix on tax ID and operacion_exenta for intra-EU supplies
    // Note: XI (Northern Ireland) excluded as it only applies to goods, not services
    private static $viesCountries = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'EL', 'FI', 'FR',
        'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT',
        'RO', 'SE', 'SI', 'SK'
    ];

    /**
     * Constructor
     *
     * @param object $db Database connection object
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->lastError = null;

        // Load API keys from configuration
        if (defined('MET_ENV') && constant('MET_ENV') == 'LOCAL') {
            $verifactiKeys = require dirname(__FILE__) . "/../private/verifacti_keys.php";
        } else {
            $verifactiKeys = require "/home/metmeetings/private/verifacti_keys.php";
        }
        $this->apiKey = $verifactiKeys["api_key"];
        $this->apiUrl = $verifactiKeys["api_url"];

        // Initialize logging
        $logDir = dirname(__FILE__) . "/../logs";
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . "/verifacti_" . date("Y-m") . ".log";
    }

    /**
     * Write entry to log file
     *
     * @param string $level Log level (INFO, ERROR, DEBUG)
     * @param string $message Log message
     * @param array $context Additional context data
     */
    private function log($level, $message, $context = [])
    {
        $timestamp = date("Y-m-d H:i:s");
        $contextStr = !empty($context) ? " " . json_encode($context, JSON_UNESCAPED_UNICODE) : "";
        $logEntry = "[$timestamp] [$level] $message$contextStr\n";
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Submit an invoice to Verifacti for registration
     *
     * @param int $idFactura The invoice ID
     * @return bool True on success, false on failure
     */
    public function submitInvoice($idFactura)
    {
        $this->log("INFO", "Submitting invoice to Verifacti", ["id_factura" => $idFactura]);

        // Get invoice data from database
        $invoiceData = $this->getInvoiceData($idFactura);

        if (!$invoiceData) {
            $this->lastError = "Invoice not found: " . $idFactura;
            $this->log("ERROR", "Invoice not found", ["id_factura" => $idFactura]);
            return false;
        }

        // Get invoice lines for total calculation
        $invoiceLines = $this->getInvoiceLines($idFactura);

        // Build the API request payload
        $payload = $this->buildPayload($invoiceData, $invoiceLines);

        $this->log("DEBUG", "API request payload", [
            "id_factura" => $idFactura,
            "numero_factura" => $invoiceData["numero_factura"] ?? "",
            "tipo_factura" => $payload["tipo_factura"] ?? ""
        ]);

        // Send to Verifacti API
        $response = $this->sendToApi("/verifactu/create", $payload);

        if ($response === false) {
            // Store error in database
            $this->storeVerifactuError($idFactura, $this->lastError);
            $this->log("ERROR", "Submission failed", [
                "id_factura" => $idFactura,
                "error" => $this->lastError
            ]);
            return false;
        }

        // Store the response in database
        $this->log("INFO", "Submission successful", [
            "id_factura" => $idFactura,
            "uuid" => $response["uuid"] ?? "",
            "estado" => $response["estado"] ?? ""
        ]);
        return $this->storeVerifactuResponse($idFactura, $response);
    }

    /**
     * Cancel an invoice in Verifacti (Anular factura)
     *
     * This terminates a previously issued invoice in the AEAT system.
     * Once cancelled, no new invoice with identical series, number, and date can be created.
     *
     * @param int $idFactura The invoice ID
     * @param bool $rechazoPrevio Set to true if cancelling after previous rejection
     * @param bool $sinRegistroPrevio Set to true if invoice was not registered in AEAT
     * @return bool True on success, false on failure
     */
    public function cancelInvoice($idFactura, $rechazoPrevio = false, $sinRegistroPrevio = false)
    {
        $this->log("INFO", "Cancelling invoice in Verifacti", ["id_factura" => $idFactura]);

        // Get invoice data from database
        $invoiceData = $this->getInvoiceData($idFactura);

        if (!$invoiceData) {
            $this->lastError = "Invoice not found: " . $idFactura;
            $this->log("ERROR", "Invoice not found for cancellation", ["id_factura" => $idFactura]);
            return false;
        }

        // Check if invoice was submitted to Verifacti
        if (empty($invoiceData["verifactu_uuid"])) {
            $this->lastError = "Invoice has not been submitted to Verifacti";
            $this->log("ERROR", "Cannot cancel - no UUID", ["id_factura" => $idFactura]);
            return false;
        }

        // Parse invoice number into serie and numero
        $invoiceNumber = $invoiceData["numero_factura"] ?? "";
        $serie = "";
        $numero = $invoiceNumber;

        if (preg_match('/^([A-Za-z]+)-(.+)$/', $invoiceNumber, $matches)) {
            $serie = $matches[1];
            $numero = $matches[2];
        } elseif (preg_match('/^(\d{4})-(\d+)$/', $invoiceNumber, $matches)) {
            $serie = $matches[1];
            $numero = $matches[2];
        }

        // Format date from YYYY-MM-DD to DD-MM-YYYY
        $fechaFactura = $invoiceData["fecha_factura"] ?? date("Y-m-d");
        $fechaExpedicion = date("d-m-Y", strtotime($fechaFactura));

        // Build the cancellation payload
        $payload = [
            "serie" => $serie,
            "numero" => $numero,
            "fecha_expedicion" => $fechaExpedicion,
            "rechazo_previo" => $rechazoPrevio ? "S" : "N",
            "sin_registro_previo" => $sinRegistroPrevio ? "S" : "N"
        ];

        // Send to Verifacti API
        $response = $this->sendToApi("/verifactu/cancel", $payload);

        if ($response === false) {
            $this->storeVerifactuError($idFactura, $this->lastError);
            $this->log("ERROR", "Cancellation failed", [
                "id_factura" => $idFactura,
                "error" => $this->lastError
            ]);
            return false;
        }

        // Update the invoice status to "anulada"
        $this->db->callProcedure(
            "CALL ed_sp_factura_verifactu_actualizar(" .
            intval($idFactura) . ",'" .
            generalUtils::escaparCadena($invoiceData["verifactu_uuid"]) . "','" .
            generalUtils::escaparCadena($invoiceData["verifactu_qr"] ?? "") . "','" .
            generalUtils::escaparCadena($invoiceData["verifactu_huella"] ?? "") . "'," .
            "'anulada','" .
            generalUtils::escaparCadena($invoiceData["verifactu_url"] ?? "") . "','')"
        );

        $this->log("INFO", "Cancellation successful", [
            "id_factura" => $idFactura,
            "numero_factura" => $invoiceData["numero_factura"] ?? ""
        ]);
        return true;
    }

    /**
     * Get invoice data from database
     *
     * @param int $idFactura The invoice ID
     * @return array|false Invoice data or false if not found
     */
    private function getInvoiceData($idFactura)
    {
        $resultado = $this->db->callProcedure("CALL ed_sp_factura_obtener_concreto_pdf(" . intval($idFactura) . ")");
        $data = $this->db->getData($resultado);

        if (!$data) {
            return false;
        }

        // Fetch rectificativa fields which may not be in the stored procedure
        $rectQuery = "SELECT es_rectificativa, tipo_rectificativa, id_factura_rectificada,
                             importe_rectificativa, cuota_rectificativa
                      FROM ed_tb_factura WHERE id_factura = " . intval($idFactura);
        $rectResult = $this->db->callProcedure($rectQuery);
        $rectData = $this->db->getData($rectResult);

        if ($rectData) {
            $data = array_merge($data, $rectData);
        }

        return $data;
    }

    /**
     * Get invoice lines from database
     *
     * @param int $idFactura The invoice ID
     * @return array Invoice lines
     */
    private function getInvoiceLines($idFactura)
    {
        // Get language ID (default to English = 3)
        $langId = isset($_SESSION["user"]["language_id"]) ? $_SESSION["user"]["language_id"] : 3;

        $resultado = $this->db->callProcedure("CALL ed_sp_linea_factura_obtener_concreto_pdf(" . intval($idFactura) . "," . $langId . ")");

        $lines = [];
        while ($row = $this->db->getData($resultado)) {
            $lines[] = $row;
        }

        return $lines;
    }

    /**
     * Build the API request payload according to Verifacti API specification
     *
     * @param array $invoiceData Invoice data from database
     * @param array $invoiceLines Invoice line items
     * @return array Payload for Verifacti API
     */
    private function buildPayload($invoiceData, $invoiceLines)
    {
        $invoiceType = $invoiceData["tipo_factura_verifactu"] ?? "F1";
        $isSimplified = ($invoiceType === "F2");

        // Parse invoice number into serie and numero
        // Format expected: "2025-001" or "A-2025-001"
        $invoiceNumber = $invoiceData["numero_factura"] ?? "";
        $serie = "";
        $numero = $invoiceNumber;

        // Try to extract serie if format is "SERIE-NUMBER"
        if (preg_match('/^([A-Za-z]+)-(.+)$/', $invoiceNumber, $matches)) {
            $serie = $matches[1];
            $numero = $matches[2];
        } elseif (preg_match('/^(\d{4})-(\d+)$/', $invoiceNumber, $matches)) {
            // Format "2025-001" - use year as serie
            $serie = $matches[1];
            $numero = $matches[2];
        }

        // Format date from YYYY-MM-DD to DD-MM-YYYY
        $fechaFactura = $invoiceData["fecha_factura"] ?? date("Y-m-d");
        $fechaExpedicion = date("d-m-Y", strtotime($fechaFactura));

        // Calculate totals from invoice lines
        $totalAmount = 0;
        $apiLines = [];

        foreach ($invoiceLines as $line) {
            $precio = floatval($line["precio"] ?? 0);
            $totalAmount += $precio;

            // MET is VAT exempt (Article 20 Spanish VAT law - educational/cultural non-profit)
            // So base_imponible = full amount, tipo_impositivo = 0, cuota_repercutida = 0
            $apiLines[] = [
                "descripcion" => $line["concepto_personalizado"] ?? $line["concepto"] ?? "Services",
                "base_imponible" => number_format($precio, 2, ".", ""),
                "tipo_impositivo" => "0",
                "cuota_repercutida" => "0"
            ];
        }

        // If no lines found, create a single line with total
        if (empty($apiLines)) {
            $totalAmount = floatval($invoiceData["precio"] ?? 0);
            $apiLines[] = [
                "descripcion" => "Membership / Services",
                "base_imponible" => number_format($totalAmount, 2, ".", ""),
                "tipo_impositivo" => "0",
                "cuota_repercutida" => "0"
            ];
        }

        // Build base payload
        $payload = [
            "serie" => $serie,
            "numero" => $numero,
            "fecha_expedicion" => $fechaExpedicion,
            "tipo_factura" => $invoiceType,
            "descripcion" => "MET Membership and Services",
            "importe_total" => number_format($totalAmount, 2, ".", ""),
            "lineas" => $apiLines
        ];

        // Add recipient details (not required for F2 simplified invoices)
        if (!$isSimplified) {
            // Determine if Spanish or foreign customer
            $countryCode = strtoupper($invoiceData["tax_id_country"] ?? "");
            $isSpanish = empty($countryCode) || $countryCode === "ES";
            $isVies = !$isSpanish && $this->isViesCountry($countryCode);

            // Customer name (required for F1)
            $customerName = !empty($invoiceData["nombre_empresa_factura"])
                ? $invoiceData["nombre_empresa_factura"]
                : $invoiceData["nombre_cliente_factura"];

            $payload["nombre"] = $customerName;

            if ($isSpanish) {
                // Spanish customer - use NIF
                // Prefer tax_id_number, fallback to nif_cliente_factura for old invoices
                $payload["nif"] = !empty($invoiceData["tax_id_number"])
                    ? $invoiceData["tax_id_number"]
                    : ($invoiceData["nif_cliente_factura"] ?? "");
            } else {
                // Foreign customer - use id_otro structure
                // API expects: id_type, id, codigo_pais
                $taxIdNumber = $invoiceData["tax_id_number"] ?? "";

                // For VIES countries, prefix country code to tax ID for VIES validation
                // e.g., "0404621642" becomes "BE0404621642" for Belgium
                if ($isVies && !empty($countryCode) && !empty($taxIdNumber)) {
                    // Only add prefix if not already present
                    if (strpos(strtoupper($taxIdNumber), $countryCode) !== 0) {
                        $taxIdNumber = $countryCode . $taxIdNumber;
                    }
                }

                $payload["id_otro"] = [
                    "id_type" => $this->mapTaxIdType($invoiceData["tax_id_type"] ?? "04"),
                    "id" => $taxIdNumber,
                    "codigo_pais" => $countryCode
                ];
            }

            // For VIES countries (intra-EU B2B), add operacion_exenta to lines
            // E5 = Exempt under Article 25 LIVA (intra-EU supply of services)
            if ($isVies) {
                foreach ($payload["lineas"] as &$linea) {
                    $linea["operacion_exenta"] = "E5";
                    // Remove VAT fields as they're not applicable for exempt operations
                    unset($linea["tipo_impositivo"]);
                    unset($linea["cuota_repercutida"]);
                }
                unset($linea); // Break reference
            }
        }

        // Add rectificativa (corrective invoice) data if applicable
        $isRectificativa = in_array($invoiceType, ['R1', 'R2', 'R3', 'R4', 'R5']);
        if ($isRectificativa && !empty($invoiceData["tipo_rectificativa"])) {
            $payload["tipo_rectificativa"] = $invoiceData["tipo_rectificativa"];

            if ($invoiceData["tipo_rectificativa"] === 'S') {
                // Sustitución - need details of the invoice being replaced
                $originalInvoice = $this->getOriginalInvoiceData($invoiceData["id_factura_rectificada"] ?? null);
                if ($originalInvoice) {
                    // Parse original invoice number into serie and numero
                    $origSerie = "";
                    $origNumero = $originalInvoice["numero_factura"];
                    if (preg_match('/^([A-Za-z]*-?)(\d{4})-(\d+)$/', $origNumero, $matches)) {
                        $origSerie = ($matches[1] ?? '') . $matches[2];
                        $origNumero = $matches[3];
                    } elseif (preg_match('/^(\d{4})-(\d+)$/', $origNumero, $matches)) {
                        $origSerie = $matches[1];
                        $origNumero = $matches[2];
                    }

                    // Format date
                    $origFecha = date("d-m-Y", strtotime($originalInvoice["fecha_factura"]));

                    $payload["facturas_sustituidas"] = [
                        [
                            "serie" => $origSerie,
                            "numero" => $origNumero,
                            "fecha_expedicion" => $origFecha
                        ]
                    ];
                }
            } elseif ($invoiceData["tipo_rectificativa"] === 'I') {
                // Por diferencia - include correction amounts
                $payload["importe_rectificativa"] = number_format(floatval($invoiceData["importe_rectificativa"] ?? 0), 2, ".", "");
                $payload["base_rectificada"] = number_format(floatval($invoiceData["importe_rectificativa"] ?? 0), 2, ".", "");
                $payload["cuota_rectificada"] = number_format(floatval($invoiceData["cuota_rectificativa"] ?? 0), 2, ".", "");
                $payload["cuota_recargo_rectificada"] = "0.00"; // MET doesn't use recargo
            }
        }

        return $payload;
    }

    /**
     * Get original invoice data for rectificativa de sustitución
     *
     * @param int|null $idFactura The original invoice ID
     * @return array|false Invoice data or false if not found
     */
    private function getOriginalInvoiceData($idFactura)
    {
        if (empty($idFactura)) {
            return false;
        }

        $query = "SELECT numero_factura, fecha_factura, verifactu_uuid
                  FROM ed_tb_factura
                  WHERE id_factura = " . intval($idFactura);
        $resultado = $this->db->callProcedure($query);
        $data = $this->db->getData($resultado);

        return $data ? $data : false;
    }

    /**
     * Check if a country code is a VIES country (EU member state for VAT purposes)
     *
     * @param string $countryCode Two-letter country code
     * @return bool True if VIES country
     */
    private function isViesCountry($countryCode)
    {
        return in_array(strtoupper($countryCode), self::$viesCountries);
    }

    /**
     * Map tax ID type to Verifacti codes
     *
     * @param string $type Tax ID type from form
     * @return string Verifacti type code
     */
    private function mapTaxIdType($type)
    {
        // Verifacti id_otro tipo codes:
        // 02 = NIF-IVA (EU VAT)
        // 03 = Passport
        // 04 = Official ID from country of residence
        // 05 = Residence certificate
        // 06 = Other supporting document
        // 07 = Not registered

        // If already a valid numeric code, return it directly
        $validCodes = ["02", "03", "04", "05", "06", "07"];
        if (in_array($type, $validCodes)) {
            return $type;
        }

        // Otherwise map text values to codes (for backwards compatibility)
        $typeMap = [
            "VAT" => "02",
            "NIF-IVA" => "02",
            "PASSPORT" => "03",
            "ID" => "04",
            "OTHER" => "06"
        ];

        return $typeMap[strtoupper($type)] ?? "04";
    }

    /**
     * Send request to Verifacti API
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array|false API response or false on failure
     */
    private function sendToApi($endpoint, $data)
    {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init($url);

        $jsonData = json_encode($data);

        // Disable SSL verification for local development (Laragon certificate issue)
        $sslVerify = (defined('MET_ENV') && MET_ENV === 'LOCAL') ? false : true;

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->apiKey,
                "Accept: application/json"
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            $this->lastError = "cURL error: " . $curlError;
            return false;
        }

        $responseData = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $responseData;
        } else {
            $errorMsg = "";
            if (isset($responseData["message"])) {
                $errorMsg = $responseData["message"];
            } elseif (isset($responseData["error"])) {
                $errorMsg = $responseData["error"];
            } elseif (isset($responseData["errors"]) && is_array($responseData["errors"])) {
                $errorMsg = implode("; ", $responseData["errors"]);
            } else {
                $errorMsg = $response;
            }
            $this->lastError = "API error (HTTP $httpCode): " . $errorMsg;
            return false;
        }
    }

    /**
     * Store Verifactu response in database
     *
     * @param int $idFactura Invoice ID
     * @param array $response API response
     * @return bool True on success, false on failure
     */
    private function storeVerifactuResponse($idFactura, $response)
    {
        // Verifacti response fields
        $uuid = isset($response["uuid"]) ? generalUtils::escaparCadena($response["uuid"]) : "";
        $qrCode = isset($response["qr"]) ? generalUtils::escaparCadena($response["qr"]) : "";
        $huella = isset($response["huella"]) ? generalUtils::escaparCadena($response["huella"]) : "";
        $status = isset($response["estado"]) ? generalUtils::escaparCadena($response["estado"]) : "pending";
        $url = isset($response["url"]) ? generalUtils::escaparCadena($response["url"]) : "";

        $this->db->callProcedure(
            "CALL ed_sp_factura_verifactu_actualizar(" .
            intval($idFactura) . ",'" .
            $uuid . "','" .
            $qrCode . "','" .
            $huella . "','" .
            $status . "','" .
            $url . "','')"
        );

        return true;
    }

    /**
     * Store error in database when submission fails
     *
     * @param int $idFactura Invoice ID
     * @param string $error Error message
     */
    private function storeVerifactuError($idFactura, $error)
    {
        $this->db->callProcedure(
            "CALL ed_sp_factura_verifactu_actualizar(" .
            intval($idFactura) . ",'','','','error','','" .
            generalUtils::escaparCadena($error) . "')"
        );
    }

    /**
     * Get the last error message
     *
     * @return string|null Last error message or null if no error
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Check if Verifacti integration is enabled
     *
     * @return bool True if API key is configured
     */
    public function isEnabled()
    {
        return !empty($this->apiKey) &&
               $this->apiKey !== "YOUR_API_KEY_HERE" &&
               $this->apiKey !== "YOUR_TEST_API_KEY_HERE" &&
               $this->apiKey !== "YOUR_LIVE_API_KEY_HERE";
    }

    /**
     * Get QR code for an invoice (retrieves from database)
     *
     * @param int $idFactura Invoice ID
     * @return string|null QR code data or null if not available
     */
    public function getQrCode($idFactura)
    {
        $resultado = $this->db->callProcedure("CALL ed_sp_factura_obtener_concreto(" . intval($idFactura) . ")");
        $data = $this->db->getData($resultado);

        if ($data && !empty($data["verifactu_qr"])) {
            return $data["verifactu_qr"];
        }

        return null;
    }

    /**
     * Check if an invoice has been submitted to Verifactu
     *
     * @param int $idFactura Invoice ID
     * @return bool True if already submitted
     */
    public function isSubmitted($idFactura)
    {
        $resultado = $this->db->callProcedure("CALL ed_sp_factura_obtener_concreto(" . intval($idFactura) . ")");
        $data = $this->db->getData($resultado);

        return $data && !empty($data["verifactu_uuid"]);
    }

    /**
     * Query the status of a submitted invoice from Verifacti API
     *
     * Uses the UUID stored from initial submission to check current status.
     *
     * @param int $idFactura Invoice ID
     * @return array|false Status data or false on failure
     */
    public function getInvoiceStatus($idFactura)
    {
        $this->log("INFO", "Checking invoice status", ["id_factura" => $idFactura]);

        // Get invoice data to retrieve UUID
        $resultado = $this->db->callProcedure("CALL ed_sp_factura_obtener_concreto(" . intval($idFactura) . ")");
        $invoiceData = $this->db->getData($resultado);

        if (!$invoiceData || empty($invoiceData["verifactu_uuid"])) {
            $this->lastError = "Invoice has no Verifactu UUID";
            $this->log("ERROR", "Cannot check status - no UUID", ["id_factura" => $idFactura]);
            return false;
        }

        $uuid = $invoiceData["verifactu_uuid"];

        // Query the status endpoint
        $response = $this->sendGetRequest("/verifactu/status?uuid=" . urlencode($uuid));

        if ($response === false) {
            $this->log("ERROR", "Status check failed", [
                "id_factura" => $idFactura,
                "error" => $this->lastError
            ]);
            return false;
        }

        // Update the status in database if we got a valid response
        if (isset($response["estado"])) {
            $newStatus = $this->mapApiStatus($response["estado"]);
            $qrCode = isset($response["qr"]) ? generalUtils::escaparCadena($response["qr"]) : ($invoiceData["verifactu_qr"] ?? "");
            $huella = isset($response["huella"]) ? generalUtils::escaparCadena($response["huella"]) : ($invoiceData["verifactu_huella"] ?? "");
            $url = isset($response["url"]) ? generalUtils::escaparCadena($response["url"]) : ($invoiceData["verifactu_url"] ?? "");
            $error = isset($response["error"]) ? generalUtils::escaparCadena($response["error"]) : "";

            $this->db->callProcedure(
                "CALL ed_sp_factura_verifactu_actualizar(" .
                intval($idFactura) . ",'" .
                generalUtils::escaparCadena($uuid) . "','" .
                $qrCode . "','" .
                $huella . "','" .
                $newStatus . "','" .
                $url . "','" .
                $error . "')"
            );

            $this->log("INFO", "Status check successful", [
                "id_factura" => $idFactura,
                "estado" => $response["estado"],
                "internal_status" => $newStatus
            ]);
        }

        return $response;
    }

    /**
     * Map Verifacti API status to our internal status values
     *
     * @param string $apiStatus Status from API
     * @return string Internal status value
     */
    private function mapApiStatus($apiStatus)
    {
        // API returns Spanish status values
        $statusMap = [
            "Pendiente" => "pending",
            "Correcto" => "confirmed",
            "Aceptado con errores" => "confirmed",
            "Incorrecto" => "error",
            "Duplicado" => "error",
            "Anulado" => "anulada",
            "Factura inexistente" => "error",
            "No registrado" => "error",
            "Error servidor AEAT" => "pending"
        ];

        return $statusMap[$apiStatus] ?? "pending";
    }

    /**
     * Send GET request to Verifacti API
     *
     * @param string $endpoint API endpoint with query string
     * @return array|false API response or false on failure
     */
    private function sendGetRequest($endpoint)
    {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init($url);

        $sslVerify = (defined('MET_ENV') && MET_ENV === 'LOCAL') ? false : true;

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->apiKey,
                "Accept: application/json"
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($curlError) {
            $this->lastError = "cURL error: " . $curlError;
            return false;
        }

        $responseData = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $responseData;
        } else {
            $errorMsg = "";
            if (isset($responseData["message"])) {
                $errorMsg = $responseData["message"];
            } elseif (isset($responseData["error"])) {
                $errorMsg = $responseData["error"];
            } else {
                $errorMsg = $response;
            }
            $this->lastError = "API error (HTTP $httpCode): " . $errorMsg;
            return false;
        }
    }
}
