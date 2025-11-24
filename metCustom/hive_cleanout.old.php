<?php
/*
* Hive cleanout
*/

	require "../classes/databaseConnection.php";
	require "../database/connection.php";

$os = array("hive-team@metmeetings.org", "Met-Conversations@metmeetings.org");
$populate = $db->callProcedure("CALL metmeetings_wordpress.wordpress_get_emails()");

while($item=$db->getData($populate)){
  
  $memberEmail = $item['user_email'];
  
  $result = $db->callProcedure("CALL ed_pr_check_lapsed('$memberEmail')");
  $row = $result->fetch_assoc();
  
  if (!$row['id_usuario_web']) {

    if (!in_array($memberEmail, $os)) {
    echo $memberEmail;
    
    $resultado = $db->callProcedure("CALL metmeetings_wordpress.wordpress_cleanout('$memberEmail')");

    if ($resultado!=""){
        echo " - Success!";
    } else {
        echo " - It didn't work.";
    }
    
    echo "<br />";
    } 
  }
}
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>