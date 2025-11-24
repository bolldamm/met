<?php
// $idMovement = $_GET["movement"];
// $idRegno = $_GET["regno"];

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

if ($_GET["regno"])
{
//  echo "regno:";
//  echo $_GET["regno"];
//    echo "<br>movement:";
//  echo $_GET["movement"];
  
  $codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_link_to_reg_id('".$_GET["movement"]."','".$_GET["regno"]."')";
$resultado=$db->callProcedure($codeProcedure);
  
  echo "DONE!";
  
  echo "<br><input type=button onClick='self.close();' value='Close this window'>";
  
    } else {
        
?>
<!DOCTYPE html>
<html>
<body>

<h1>Link Reg ID number</h1>

<p><strong>WARNING:</strong> Reg ID = Membership Reg ID only. This is the number you find in the Reg. ID column of the Movements page when the movement type is Membership | Membership current year.</p>
  
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="get">
     <input type="hidden" id="movement" name="movement" value="<?php echo $_GET['movement'] ?>">
    <label for="registration_id">Reg ID</label>&nbsp;&nbsp;<input style="width:50px" name="regno" type="number" value="" />&nbsp;&nbsp;
    <button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover"><span class="ui-button-text">Link</span></button>
  </form> 

</body>
  <?php
}
?>