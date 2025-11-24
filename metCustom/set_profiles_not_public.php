<?php
/*
Delete lapsed member records 
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_suspended_member_ids()";
$resultado=$db->callProcedure($codeProcedure);

while($dato=$db->getData($resultado)){

$idMember = $dato["id_usuario_web"];
echo $idMember;
echo " - ";

  $result = $db->callProcedure("CALL ed_pr_set_profiles_not_public($idMember)");
  
  if ($result!=""){
	echo "Success!";
  } else {
	echo "It didn't work.";
  }

echo "<br>";
  
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>