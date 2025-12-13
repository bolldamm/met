<?php
/**
 * AJAX endpoint to search for original invoices when creating a rectificativa
 * Used for Factura Rectificativa de Sustitución (Type S)
 *
 * @author Claude
 */

// Suppress all error output and handle manually
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require "../includes/load_main_components.inc.php";
    require "../includes/load_validate_user.inc.php";
    require "../config/constants.php";

    // Get search parameters
    $searchText = isset($_GET["q"]) ? generalUtils::escaparCadena($_GET["q"]) : "";

    if (strlen($searchText) < 2) {
        echo json_encode(["success" => false, "error" => "Search term must be at least 2 characters"]);
        exit;
    }

    // Search query - include all customer details for auto-population
    $query = "SELECT
        f.id_factura,
        f.numero_factura,
        f.fecha_factura,
        f.nombre_cliente_factura,
        f.nombre_empresa_factura,
        f.nif_cliente_factura,
        f.direccion_factura,
        f.codigo_postal_factura,
        f.ciudad_factura,
        f.provincia_factura,
        f.pais_factura,
        f.tax_id_country,
        f.tax_id_type,
        f.tax_id_number,
        (SELECT SUM(lf.precio) FROM ed_tb_linea_factura lf WHERE lf.id_factura = f.id_factura) as total
    FROM ed_tb_factura f
    WHERE f.proforma = 0
      AND f.numero_factura IS NOT NULL
      AND f.numero_factura != ''
      AND (
        f.numero_factura LIKE '%" . $searchText . "%'
        OR f.nombre_cliente_factura LIKE '%" . $searchText . "%'
        OR f.nombre_empresa_factura LIKE '%" . $searchText . "%'
        OR f.nif_cliente_factura LIKE '%" . $searchText . "%'
        OR f.tax_id_number LIKE '%" . $searchText . "%'
      )
    ORDER BY f.id_factura DESC
    LIMIT 20";

    $result = $db->callProcedure($query);
    $invoices = [];

    while ($row = $db->getData($result)) {
        $customerName = !empty($row["nombre_empresa_factura"])
            ? $row["nombre_empresa_factura"]
            : $row["nombre_cliente_factura"];

        // Use tax_id_number with fallback to nif_cliente_factura
        $displayNif = !empty($row["tax_id_number"])
            ? $row["tax_id_number"]
            : ($row["nif_cliente_factura"] ?? "");

        $invoices[] = [
            "id" => $row["id_factura"],
            "numero" => $row["numero_factura"],
            "fecha" => $row["fecha_factura"] ?? "",
            "customer" => $customerName ?? "",
            "nif" => $displayNif,
            "total" => number_format(floatval($row["total"] ?? 0), 2, ".", ","),
            // Include all customer details for auto-population
            "nombre_cliente" => $row["nombre_cliente_factura"] ?? "",
            "nombre_empresa" => $row["nombre_empresa_factura"] ?? "",
            "direccion" => $row["direccion_factura"] ?? "",
            "codigo_postal" => $row["codigo_postal_factura"] ?? "",
            "ciudad" => $row["ciudad_factura"] ?? "",
            "provincia" => $row["provincia_factura"] ?? "",
            "pais" => $row["pais_factura"] ?? "",
            "tax_id_country" => $row["tax_id_country"] ?? "",
            "tax_id_type" => $row["tax_id_type"] ?? "",
            "tax_id_number" => $row["tax_id_number"] ?? ""
        ];
    }

    echo json_encode([
        "success" => true,
        "count" => count($invoices),
        "invoices" => $invoices
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Error: " . $e->getMessage()
    ]);
} catch (Error $e) {
    echo json_encode([
        "success" => false,
        "error" => "Fatal error: " . $e->getMessage()
    ]);
}
