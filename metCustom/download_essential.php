<?php
session_start();

if (!isset($_SESSION['attendees_export'])) {
    die("No data to export.");
}

$attendees = $_SESSION['attendees_export'];

// Set Excel headers
header("Content-Disposition: attachment; filename=must-be-theres.xls");
header("Content-Type: application/vnd.ms-excel; charset=utf-8");

// Excel-compatible HTML table output
echo "<table border='1'>
        <tr>
            <th>Attendee ID</th>
            <th>First Name</th>
            <th>Surname</th>
            <th>Email</th>
        </tr>";

foreach ($attendees as $attendee) {
    echo "<tr>
            <td>{$attendee['id']}</td>
            <td>{$attendee['nombre']}</td>
            <td>{$attendee['apellidos']}</td>
            <td>{$attendee['correo']}</td>
          </tr>";
}

echo "</table>";
exit;
?>