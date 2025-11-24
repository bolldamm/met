<?php
/* Clears zero-euro movements and invoices */

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$resultado = $db->callProcedure("CALL ed_sp_factura_zero_borrar()");

echo "Success!";
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>