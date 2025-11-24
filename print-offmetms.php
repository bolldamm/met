<?php
require "includes/load_main_components.inc.php";

//Obtenemos la conferencia actual
$conferenciaactual = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");
$datoConferencia = $db->getData($conferenciaactual);
$numeroConferencia = $datoConferencia["id_conferencia"];
  
header('Content-Type: text/html; charset=utf-8');
// Build the search pattern
$pattern = $_SERVER['DOCUMENT_ROOT'] . "/documentacion/conference_data/sessions." . $datoConferencia["id_conferencia"] . ".*.txt";
// Get the first matching file
$filename = glob($pattern)[0] ?? null; 
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$allSchedules = [];
$day = "";

// ---- Parse input ----
foreach ($lines as $line) {
    $parts = explode("|", $line);
    $type = $parts[0] ?? "";
    $time = $parts[1] ?? "";
    $room = trim($parts[2] ?? "");
    $title = trim($parts[3] ?? "");

    if ($type === "Day") {
        $day = strtok($parts[2], ","); 
        $allSchedules[$day] = [];
    } elseif ($type === "Off-METM" && $day !== "") {
        if ($room !== "") {
            $title .= ' [' . $room . ']';
        }
        $allSchedules[$day][] = [
            'time' => $time,
            'title' => $title
        ];
    }
}

// ---- Build tables with "height" estimate (number of rows) ----
$tablesWithHeight = [];
foreach ($allSchedules as $day => $events) {
    if (empty($events)) continue;
    $table = '<table>';
    $table .= '<thead><tr><th>' . htmlspecialchars($day) . '</th><th>Event</th></tr></thead><tbody>';
    foreach ($events as $event) {
        $table .= '<tr>';
        $table .= '<td>' . htmlspecialchars($event['time']) . '</td>';
        $table .= '<td>' . htmlspecialchars($event['title']) . '</td>';
        $table .= '</tr>';
    }
    $table .= '</tbody></table>';

    $tablesWithHeight[] = [
        'html' => $table,
        'height' => count($events) + 1 // +1 for header row
    ];
}

// ---- Distribute tables to balance column heights ----
$leftTables = [];
$rightTables = [];
$leftHeight = 0;
$rightHeight = 0;

foreach ($tablesWithHeight as $t) {
    if ($leftHeight <= $rightHeight) {
        $leftTables[] = $t['html'];
        $leftHeight += $t['height'];
    } else {
        $rightTables[] = $t['html'];
        $rightHeight += $t['height'];
    }
}

// ---- Footer ----
$pagefooter = '<div class="pfooter">';
$pagefooter .= 'Booking may be required for some off-METM events. Check <a href="https://www.metmeetings.org/en/my-metm:1415" target="_blank" style="text-decoration: underline; color: #0066cc;">My METM</a> for your sign-ups.<br>';
$pagefooter .= 'Version date: <strong>' . date('j F Y') . '</strong>. Check the “Last Minute” section at the top of <a href="https://www.metmeetings.org/en/my-metm:1415" target="_blank" style="text-decoration: underline; color: #0066cc;">My METM</a> for any changes.<br>';
$pagefooter .= '🍃 Think before you print. 🍃';
$pagefooter .= '</div>';

// ---- Build HTML content ----
$content = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Off-METM Programme</title>';
$content .= '<style>
@page { size: portrait; }
body { font-family: Arial, sans-serif; margin: 20px; }
.header { display: flex; align-items: center; margin-bottom: 30px; }
.header img { width: 160px; height: 160px; margin-right: 20px; }
.header h1 { margin: 0; font-size: 2em; text-align: center; }
.container { display: flex; justify-content: space-between; align-items: flex-start; }
.column { width: 48%; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 20px; }
th, td { border: 1px solid #333; padding: 8px; text-align: left; word-wrap: break-word; }
th:first-child, td:first-child { width: 25%; }
th { background-color: #f2f2f2; }
.pfooter { text-align: left; margin-top: 40px; font-size: 0.9em; line-height: 150%;}
</style></head><body><script>window.onload = function() { window.print(); };</script>';

// ---- Header ----
$content .= '<div class="header">';
$content .= '<img src="https://www.metmeetings.org/images/pictures/logos/MET_logo_color_SQUARE_256x256.png" alt="MET Logo">';
$content .= '<h1>OFF-METM PROGRAMME</h1>';
$content .= '</div>';

// ---- Two-column container ----
$content .= '<div class="container">';
$content .= '<div class="column">'; 
foreach ($leftTables as $t) { $content .= $t; }
$content .= '</div>';

$content .= '<div class="column">'; 
foreach ($rightTables as $t) { $content .= $t; }
$content .= '</div>';
$content .= '</div>'; // close container

// ---- Footer ----
$content .= $pagefooter;

$content .= '</body></html>';

// ---- Display the content ----
echo $content;
?>