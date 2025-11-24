<?php
/*
Delete lapsed member images 
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_lapsed_member_ids()";
$resultado=$db->callProcedure($codeProcedure);

while($dato=$db->getData($resultado)){
$imageFile = "/home/metmeetings/www/www/files/members/" . $dato["id_usuario_web"] . ".*";

  foreach (glob($imageFile) as $filename) {
    echo $filename;
    
    if(unlink($filename)) {
       echo " - Success!<br>";
    } else {
       echo " - Failed!<br>";
    }
    
  }
  
  $imageFile = "/home/metmeetings/www/www/files/members/thumb/" . $dato["id_usuario_web"] . ".*";

  foreach (glob($imageFile) as $filename) {
    echo $filename;
    
    if(unlink($filename)) {
       echo " - Success!<br>";
    } else {
       echo " - Failed!<br>";
    }
    
  }
 
}

echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>