<?php
echo "<h1>This routine will print workshop badges for:</h1>";


$inputDate = $_GET["ws-date"]; // Expected format: dd-mm-yyyy

// Convert to yyyy-mm-dd using DateTime
$dateObj = DateTime::createFromFormat('d-m-Y', $inputDate);

if ($dateObj) {
    $wsdate = $dateObj->format('Y-m-d');

    // Now call the procedure with the properly formatted date
    $resultado = $db->callProcedure("CALL ed_pr_ws_badge('" . $wsdate . "')");

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            echo htmlspecialchars($row["nombre"]) . " " . htmlspecialchars($row["apellidos"]) . "<br>";
        }
    } else {
        echo "No results or query failed.";
    }
} else {
    echo "Invalid date format.";
}



?>