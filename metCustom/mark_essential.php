<?php

	session_start();
	header('Content-Type: text/html; charset=utf-8');

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

	// Get currrent conference ID
	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$current_id_conferencia = $row["current_id_conferencia"];

	require "../easygestor/includes/load_remove_attending_essential_attendees.php";

// Call the function to clean up essential attendees already attending the conference
$essentialMember = removeAttendingEssentialAttendees($db, $current_id_conferencia);

if (isset($_GET['del'])) {
    $attendee_id_to_delete = intval($_GET['del']);  // sanitize input

    // Call procedure to delete the attendee from the DB
    $db->callProcedure("CALL ed_pr_essential_attendee_delete('" . $attendee_id_to_delete . "')");

    // Optional: Redirect to remove the GET param from URL (cleaner reload)
    // generalUtils::redirigir("www.metmeetings.org/metCustom/mark_essential.php");
    header("Location: mark_essential.php");
    exit;
}

echo '<!DOCTYPE html><html><head>    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style></head><body>';

if (isset($_POST['hdnId']) && !empty($_POST['hdnId'])) {
    $selectedIds = explode(',', $_POST['hdnId']);

    echo "<h2>Processing essential METM attendees:</h2>";
    echo "<ul>";

    foreach ($selectedIds as $id) {
        $attendee_id = trim($id);

        if ($attendee_id !== '' && ctype_digit($attendee_id)) {
            $alreadyEssential = false;
            $alreadyComing = false;

            // Check if already marked essential
            $resultado = $db->callProcedure("CALL ed_pr_essential_attendee_present('" . $attendee_id . "')");
            if ($db->getNumberRows($resultado) > 0) {
                $alreadyEssential = true;
            }

            // Check if already attending the current conference
            $resultado = $db->callProcedure("CALL ed_pr_get_conference_user('" . $attendee_id . "', '" . $current_id_conferencia . "')");
            if ($db->getNumberRows($resultado) > 0) {
                $alreadyComing = true;
            }

            // Fetch user details for displaying name
            $userDetails = $db->callProcedure("CALL ed_pr_get_user_details('" . $attendee_id . "')");
            $fullName = "User ID $attendee_id"; // Fallback

            if ($db->getNumberRows($userDetails) > 0) {
                $user = $db->getData($userDetails);
                $firstName = htmlspecialchars($user["nombre"]);
                $lastName = htmlspecialchars($user["apellidos"]);
                $fullName = "$firstName $lastName";
            }

            if ($alreadyEssential) {
                echo "<li><strong>$fullName</strong> is already marked essential. Skipped.</li>";
            } elseif ($alreadyComing) {
                echo "<li><strong>$fullName</strong> is already attending. Skipped.</li>";
            } else {
                // Add to essential list
                $db->callProcedure("CALL ed_pr_essential_attendee_add('" . $attendee_id . "')");
                echo "<li><strong>$fullName</strong> added as essential.</li>";
            }
        } else {
            echo "<li style='color:red;'>Invalid or empty ID skipped: " . htmlspecialchars($attendee_id) . "</li>";
        }
    }

    echo "</ul>";
} else {
    echo "<p>To add other names to the essential member METM attendee list,<br />go to the MEMBERS section of easyGestor, select some names<br />and press <em>Essential METM attendee</em>.</p>";
}

echo "<h2>Essential member METM attendees</h2>";

// Get all essential attendees (id_usuario is returned)
$resultado = $db->callProcedure("CALL ed_pr_essential_attendee_list()");

// Collect all rows first
$attendees = [];

while ($row = $db->getData($resultado)) {
    $attendee_id = isset($row["id_usuario_web"]) ? htmlspecialchars($row["id_usuario_web"]) : 'UNKNOWN';

    $nombre = "-";
    $apellidos = "-";
    $correo = "-";

    if ($attendee_id !== 'UNKNOWN') {
        $detalles = $db->callProcedure("CALL ed_pr_get_user_details('" . $attendee_id . "')");

        if ($db->getNumberRows($detalles) > 0) {
            $info = $db->getData($detalles);
            $nombre = htmlspecialchars($info["nombre"]);
            $apellidos = htmlspecialchars($info["apellidos"]);
            $correo = htmlspecialchars($info["correo_electronico"]);
        }
    }

    $attendees[] = [
        'id' => $attendee_id,
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'correo' => $correo
    ];
}

// Sort alphabetically by surname
usort($attendees, function ($a, $b) {
    return strcasecmp($a['apellidos'], $b['apellidos']);
});

// Save to session for download
$_SESSION['attendees_export'] = $attendees;

// Add CSV download button
echo "<form method='post' action='download_essential.php'>
        <button type='submit'>Download Excel with emails</button>
      </form><br />";

// Display HTML table (no email, no remove column)
echo "<table border='1' cellpadding='5' cellspacing='0'>
      <thead>
        <tr>
            <th>Attendee ID</th>
            <th>First Name</th>
            <th>Surname</th>
            <th>Remove</th>
        </tr>
      </thead>
      <tbody>";

foreach ($attendees as $attendee) {
    echo "<tr>
            <td>{$attendee['id']}</td>
            <td>{$attendee['nombre']}</td>
            <td>{$attendee['apellidos']}</td>
            <td style='text-align:center; cursor:pointer;'>
                <span style='color:red; font-size: 1.2em;' title='Remove' onclick=\"removeEssential('{$attendee['id']}')\">✖</span>
            </td>
          </tr>";
}

echo "</tbody></table>";

?>

<script>

// Close the popup window after 2 minutes (120,000 milliseconds)
setTimeout(function () {
    window.close();
}, 120000);
  
function removeEssential(attendeeId) {
    if (!attendeeId) return;

    // Redirect to the same page with a deletion parameter
    window.location.href = "mark_essential.php?del=" + encodeURIComponent(attendeeId);
}
</script>
</body>
</html>