<?php
/*
Output the information needed for direct debit renewals (user ID, Movement ID, name, surname, email) for all paid direct debits between two dates
*/
	require "../classes/databaseConnection.php";
	require "../database/connection.php";
$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_direct_debits";
$resultado=$db->callProcedure($codeProcedure);

header("Content-Disposition: attachment; filename=direct_debits.csv");
header("Content-type: text/csv; charset=UTF-8"); 

echo "Direct debits;;;;\n";
echo "UserID;Type;Name;Surname;Email\n";
while($dato=$db->getData($resultado)){
$rows = $dato['id_usuario_web'].";".$dato['id_concepto_movimiento'].";".$dato['nombre'].";".$dato['apellidos'].";".$dato['correo_electronico']."\n";
echo $rows;
}
?>