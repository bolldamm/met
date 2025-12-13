<?php
header('Content-Type: text/html; charset=utf-8');

// ---- ADJUSTABLE CUTOFF ----
$cutoffDay  = "Friday";  // Day name where cutoff applies
$cutoffHour = 13;        // Hour (24h format) – exclude everything before this

// ---- Weekday ordering
$dayOrder = [
    "Monday"    => 1,
    "Tuesday"   => 2,
    "Wednesday" => 3,
    "Thursday"  => 4,
    "Friday"    => 5,
    "Saturday"  => 6,
    "Sunday"    => 7
];

require "includes/load_main_components.inc.php";

//Obtenemos la conferencia actual
$conferenciaactual = $db->callProcedure("CALL ed_sp_web_conferencia_actual()");
$datoConferencia = $db->getData($conferenciaactual);
$numeroConferencia = $datoConferencia["id_conferencia"];
  
// Build the search pattern
$pattern = $_SERVER['DOCUMENT_ROOT'] . "/documentacion/conference_data/sessions." . $datoConferencia["id_conferencia"] . ".*.txt";
// Get the first matching file
$filename = glob($pattern)[0] ?? null;

// $filename = "schedule.txt";
$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$pagefooter = "";
$pagefooter .= '<page_footer><div class="pfooter">';
$pagefooter .= 'The sessions marked with an asterisk have an interactive component.<br>';
$pagefooter .= 'Version date: <strong>' . date('j F Y') . '</strong>. Check the “Last Minute” section at the top of <a href="https://www.metmeetings.org/en/my-metm:1415" target="_blank" style="text-decoration: underline; color: #0066cc;">My METM</a> for any changes.<br>';
$pagefooter .= '🍃 Think before you print. 🍃';
$pagefooter .= '</div></page_footer>';

$content = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>METM Conference Programme</title>';

$content .= <<<HTML
<style>
@page { size: landscape; }
body { font-family: Arial, sans-serif; } 
h1 { text-align: center; margin: 30px 0; font-size: 2em; } 
table { width: 100%; border-collapse: collapse; margin: 0 auto 30px auto; table-layout: fixed; }
th, td { border: 1px solid #333; padding: 8px; text-align: left; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; hyphens: auto; }
th { background-color: #f2f2f2; }
.pfooter { text-align: left; line-height: 150%;}
</style></head><body>
<script>
window.onload = function() {
window.print();
};
</script>
<page>
HTML;

$day = "";
$rooms = [];
$schedule = [];
$allSchedules = [];
$tbcCounters = [];
$metmYear = ""; // last two digits of year

// ---- PARSE INPUT FILE ----
foreach ($lines as $lineIndex => $line) {
    $parts = explode("|", $line);

    $type = $parts[0] ?? "";
    $time = $parts[1] ?? "";
    $room = stripslashes(trim($parts[2] ?? ""));
    $title = stripslashes(trim($parts[3] ?? ""));
    $url = stripslashes(trim($parts[4] ?? ""));
    $presenter = stripslashes(trim($parts[5] ?? ""));
    $extra = stripslashes(trim($parts[6] ?? ""));

    // Append digit if room contains "TBC"
    if (stripos($room, 'TBC') !== false) {
        if (!isset($tbcCounters[$room])) $tbcCounters[$room] = 0;
        $tbcCounters[$room]++;
        $room .= $tbcCounters[$room];
    }

    if ($title === "Desk open") continue;

    if ($type === "Day") {
        if ($day !== "" && !empty($schedule)) {
            $allSchedules[$day] = ['rooms' => $rooms, 'schedule' => $schedule];
        }
        $day = strtok($parts[2], ","); // weekday name
        $rooms = [];
        $schedule = [];
        if (!empty($parts[3]) && preg_match('/^\d{4}$/', $parts[3])) {
            $metmYear = substr($parts[3], -2); // last two digits
        }
    }
    elseif (in_array($type, ["Keynote-AG","Session","Catering-Desk"])) {
        $assignedRoom = "";
        if ($type === "Catering-Desk") {
            $assignedRoom = "";
        } elseif ($room) {
            $assignedRoom = $room;
        } else {
            for ($i = $lineIndex + 1; $i < count($lines); $i++) {
                $nextParts = explode("|", $lines[$i]);
                if (($nextParts[0] ?? "") === "Talk" && trim($nextParts[2] ?? "") !== "") {
                    $assignedRoom = trim($nextParts[2]);
                    if (stripos($assignedRoom, 'TBC') !== false) {
                        if (!isset($tbcCounters[$assignedRoom])) $tbcCounters[$assignedRoom] = 0;
                        $tbcCounters[$assignedRoom]++;
                        $assignedRoom .= $tbcCounters[$assignedRoom];
                    }
                    break;
                }
            }
        }

        $schedule[] = [
            'time' => $time,
            'type' => $type,
            'title' => $title,
            'extra' => $extra,
            'room' => $assignedRoom,
            'talks' => []
        ];
    }
    elseif ($type === "Talk") {
        $lastIndex = count($schedule) - 1;
        if ($lastIndex >= 0) {
            $schedule[$lastIndex]['talks'][] = [
                'room' => $room,
                'title' => $title,
                'url' => $url,
                'presenter' => $presenter
            ];
            if ($room && !in_array($room, $rooms)) {
                $rooms[] = $room;
            }
        }
    }
}
if ($day !== "" && !empty($schedule)) {
    $allSchedules[$day] = ['rooms' => $rooms, 'schedule' => $schedule];
}

// ---- Title using extracted year ----
$confTitle = "METM" . $metmYear . " PROGRAMME";

// ---- HEADER ----
$content .= '<div style="display: flex; align-items: center; margin-bottom: 20px;">
        <img src="https://www.metmeetings.org/images/pictures/logos/MET_logo_color_SQUARE_256x256.png" alt="MET Logo" style="width: 160px; height: 160px; margin-right: 20px;">
        <h1 style="margin: 0;">' . htmlspecialchars($confTitle) . '</h1>
      </div>';

// ---- OUTPUT SCHEDULE (main conference only) ----
foreach ($allSchedules as $day => $data) {
    $schedule = $data['schedule'];

    if ($day === $cutoffDay) {
        $filtered = [];
        foreach ($schedule as $slot) {
            $hour = (int)substr($slot['time'], 0, 2);
            if ($hour >= $cutoffHour) $filtered[] = $slot;
        }
        if (!empty($filtered)) {
            $roomsFiltered = getRoomsForSchedule($filtered);
            $content .= renderTable($day, $roomsFiltered, $filtered);
        }
    } elseif (
        isset($dayOrder[$day], $dayOrder[$cutoffDay]) &&
        $dayOrder[$day] > $dayOrder[$cutoffDay]
    ) {
        $roomsAll = getRoomsForSchedule($schedule);
        $content .= renderTable($day, $roomsAll, $schedule);
    }
}

// ---- Helper: get only rooms used in a schedule ----
function getRoomsForSchedule($schedule) {
    $rooms = [];
    foreach ($schedule as $slot) {
        foreach ($slot['talks'] as $t) {
            if ($t['room'] && !in_array($t['room'], $rooms)) $rooms[] = $t['room'];
        }
        if ($slot['talks'] === [] && $slot['room'] && !in_array($slot['room'], $rooms)) {
            $rooms[] = $slot['room'];
        }
    }
    return $rooms;
}

// ---- Table rendering function ----
function renderTable($day, $rooms, $schedule) {
    $out = '<table>';
    $out .= '<thead><tr>';
    $out .= '<th width="10%">' . htmlspecialchars($day) . '</th>';
    $roomCount = max(1, count($rooms));
    foreach ($rooms as $r) {
        $out .= '<th width="' . intval(90 / $roomCount) . '%">' . htmlspecialchars($r) . '</th>';
    }
    $out .= '</tr></thead><tbody>';

    foreach ($schedule as $slot) {
        $out .= '<tr>';
        $out .= '<td>' . htmlspecialchars($slot['time']) . '</td>';

        $talkCount = count($slot['talks']);

        if ($talkCount === 0) {
            $colspan = max(1, count($rooms));
            $txt = htmlspecialchars($slot['title']);
            if ($slot['extra']) $txt .= ' <small>(' . htmlspecialchars($slot['extra']) . ')</small>';
            if (!empty($slot['room'])) $txt .= '&nbsp;[' . htmlspecialchars($slot['room']) . ']';
            $out .= '<td colspan="' . $colspan . '">' . $txt . '</td>';
        } elseif ($talkCount === 1) {
            $colspan = max(1, count($rooms));
            $talk = $slot['talks'][0];
            $title = htmlspecialchars($talk['title']);
            $presenter = htmlspecialchars($talk['presenter']);
            $out .= '<td colspan="' . $colspan . '">' . $title . '<br/><em>' . $presenter . '</em>';
            if (!empty($talk['room'])) $out .= ' [' . htmlspecialchars($talk['room']) . ']';
            $out .= '</td>';
        } else {
            foreach ($rooms as $r) {
                $talk = null;
                foreach ($slot['talks'] as $t) {
                    if ($t['room'] === $r) { $talk = $t; break; }
                }
                if ($talk) {
                    $out .= '<td>' . htmlspecialchars($talk['title']) . '<br/><em>' . htmlspecialchars($talk['presenter']) . '</em></td>';
                } else {
                    $out .= '<td></td>';
                }
            }
        }

        $out .= '</tr>';
    }

    $out .= '</tbody></table>';
    return $out;
}

// ---- CLEANUP AND ECHO ----
$content = preg_replace('/TBC\d+/', 'TBC', $content);
$content = str_replace('qwerty', '', $content);
$content = str_replace('qwertu', '', $content);
$content .= $pagefooter;
$content .= "</page></body></html>";
echo $content;
?>