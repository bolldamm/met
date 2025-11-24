<?php
// Deletes a particular registration record from a member's profile

require "../classes/databaseConnection.php";
require "../database/connection.php";

// Get the registration (id_inscripcion) from the URL
$regid=$_GET["regid"];

// Delete the database record of the registration
$result = $db->callProcedure("CALL ed_pr_delete_member_registration($regid)");

// On-screen feedback
if ($result!=""){
echo "Success! Check the member profile in the Easygestor.";
} else {
echo "It didn't work.";
}
echo "<br><br><button onclick='goBack()'>Go Back</button><script>function goBack() {window.history.back(); }</script>";

?>