<?php
require "includes/load_main_components.inc.php";
require "includes/load_mailer.inc.php";

if (!$_POST["returnURL"]) {
    $_POST["returnURL"] = $_SERVER['HTTP_REFERER'];
} 
?>

<html>
<head>
<meta name="robots" content="noindex">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title>METM Raffle</title>
<link rel="icon" type="image/png" href="https://www.metmeetings.org/favicon.png">
<link rel="stylesheet" href="https://www.metmeetings.org/css/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="https://www.metmeetings.org/css/section/openbox.css" type="text/css" media="screen">
<style>
body {background-color: #d6f3f2;}
h2   {text-align: center;}
p    {color: #384a85; text-align: center;}
form    {text-align: center;}
input[type=submit]    {font-size: 200%;color: white;}
button {font-size: 200%;color: white;}
</style>
</head>
<body>

<?php
  
  	$result = $db->callProcedure("CALL ed_pr_get_current_id_conferencia()");
	$row = $result->fetch_assoc();
	$idConferencia = $row["current_id_conferencia"];
  
    $result = $db->callProcedure("CALL ed_sp_conferencia_obtener_concreta($idConferencia,3)");
  	$row = $result->fetch_assoc();
	$nombreConferencia = $row["nombre"];

	$result = $db->callProcedure("CALL ed_pr_get_max_raffle_ticket($idConferencia)");
	$row = $result->fetch_assoc();
	$raffleTicket = $row["max_raffle_ticket"];
  
$result = $db->callProcedure("CALL ed_pr_get_prizes()");
$last = $result->num_rows;
?>
<h2 class="titleSection"><?php echo $nombreConferencia;?> Raffle</h2>

<table style="width:100%">
  <tr>

<?php
if ($_POST["prizeno"]) {

	if (isset($_POST['draw'])) {	
		$current_prize = $_POST["prizeno"];
	} else {
		$current_prize = $_POST["prizeno"] - 1;
		$_POST["winnerList"] = $_POST["oldwinnerList"];
	}

} else {
  $current_prize = 1;
}
$result = $db->callProcedure("CALL ed_pr_get_prize_by_id($current_prize)");

$row = $result->fetch_assoc();
$nextprize = $current_prize + 1;

?>

   <td rowspan="<?php echo $last;?>" style="width:30%;text-align:center;vertical-align:top;"><br/><br/><br/><br/><br/><br/><br/><br/>

<?php
if ($last >= $current_prize) {
	
if(!isset($_POST['confirm_draw'])){
	echo "<img src='" . $row["url"]. "' style='width: 340px; height: 170px;' />";
	echo "<p style='font-size:200%;'>";
	echo $row["name"];
	$prizeName = $row["name"];
	$sponsorName = $row["sponsor"];
	echo "</p>";
	}

if ($_POST["afterfirsttime"]) {
  
if (strlen($_POST["drawn"]) == 4 * $raffleTicket) {
    echo "Error: There are no more raffle tickets.";
    exit(); // Break out of the routine
}

do {
$ndrawn = rand(1,$raffleTicket);

while (strlen($ndrawn) < 3 ) {
$ndrawn = "0" . $ndrawn;
}

} while (strpos($_POST["drawn"],$ndrawn));

$_POST["drawn"] = $_POST["drawn"] . " " . $ndrawn;

} else {
$ndrawn = "---";
}
  
if ($ndrawn != "---") {
   	$result = $db->callProcedure("CALL ed_pr_get_raffle_winner($idConferencia,'$ndrawn')");
	$row = $result->fetch_assoc();
	$raffleWinner = $row["nombre"]." ".$row["apellidos"];
	$deleteWinner = "Delete ".$raffleWinner;
	$_POST["oldwinnerList"] = $_POST["winnerList"];
    $_POST["winnerList"] = $_POST["winnerList"]."<br>".$raffleWinner." (".$row["correo_electronico"].") won ".$sponsorName." ".$prizeName;
}

if (($ndrawn == "---") && ($current_prize <= $last) && !isset($_POST['confirm_draw'])) {

?>

<form method="post" action="https://www.metmeetings.org/raffle.php">
<input type="hidden" name="prizeno" value="<?php echo $current_prize;?>">
<input type="hidden" name="drawn" value="<?php echo $_POST["drawn"];?>">
<input type="hidden" name="returnURL" value="<?php echo $_POST["returnURL"];?>">
<input type="hidden" name="winnerList" value="<?php echo $_POST["winnerList"];?>">
<input type="hidden" name="oldwinnerList" value="<?php echo $_POST["oldwinnerList"];?>">
<input type="hidden" name="afterfirsttime" value="1">
<input class="btn btn-primary" type="submit" name="draw" value="Draw">
</form>

<?php

}

if (($ndrawn != "---") && ($current_prize < $last)) {

?>
<br/><br/>
<form method="post" action="https://www.metmeetings.org/raffle.php">
<input type="hidden" name="prizeno" value="<?php echo $nextprize;?>">
<input type="hidden" name="drawn" value="<?php echo $_POST["drawn"];?>">
<input type="hidden" name="returnURL" value="<?php echo $_POST["returnURL"];?>">
<input type="hidden" name="winnerList" value="<?php echo $_POST["winnerList"];?>">
<input type="hidden" name="oldwinnerList" value="<?php echo $_POST["oldwinnerList"];?>">
<input type="hidden" name="afterfirsttime" value="0">
<input class="btn btn-primary" type="submit" name="draw" value="Next prize"><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<input class="btn btn-primary" type="submit" name="draw_again" value="<?php echo $deleteWinner;?>">
</form>

<?php
 }
 
 if (($ndrawn != "---") && ($current_prize == $last)) {
	 $_SESSION["mailBody"] = $_POST["winnerList"];

?>
<br/><br/>
<form method="post" action="https://www.metmeetings.org/raffle.php">
<input type="hidden" name="prizeno" value="<?php echo $nextprize;?>">
<input type="hidden" name="drawn" value="<?php echo $_POST["drawn"];?>">
<input type="hidden" name="returnURL" value="<?php echo $_POST["returnURL"];?>">
<input type="hidden" name="winnerList" value="<?php echo $_POST["winnerList"];?>">
<input type="hidden" name="oldwinnerList" value="<?php echo $_POST["oldwinnerList"];?>">
<input type="hidden" name="afterfirsttime" value="0">
<input class="btn btn-primary" type="submit" name="confirm_draw" value="Confirm prizes"><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<input class="btn btn-primary" type="submit" name="draw_again" value="<?php echo $deleteWinner;?>">
</form>

<?php
 }
 
 
  
  if (isset($_POST['confirm_draw'])) {
	  
//PHPMailer Object
$mail = new PHPMailer(true); //Argument true in constructor enables exceptions

//From email address and name
$mail->From = "webmaster@metmeetings.org";
$mail->FromName = "Michael Farrell";

//To address and name
$mail->addAddress("council@metmeetings.org");
    
//CC and BCC
$mail->addCC("sponsorship@metmeetings.org");

//Address to which recipient will reply
$mail->addReplyTo("webmaster@metmeetings.org");

//Send HTML or Plain Text email
$mail->isHTML(true);

$mail->Subject = "Raffle results";
$mail->Body = $_SESSION["mailBody"];
$mail->send();

unset ($_SESSION["mailBody"]);
 
}
  
?>

</td>
    <td rowspan="<?php echo $last;?>" style="font-size: 1200%;width:40%;text-align:center;">
<?php

echo $ndrawn;
  
echo "<p style='font-size:20%;'>";
echo $raffleWinner;
echo "</p>";
  
echo "</td>";

$result = $db->callProcedure("CALL ed_pr_get_prizes()");
$last = $result->num_rows;

while($row = $result->fetch_assoc()) {
        echo "<td style='text-align:center;'><img src='" . $row["url"] . "' style='width: 200px; height: 100px;' /></td></tr><tr>";
}
echo "</tr>";
?>

</table>

<p style="text-align:right;">
<form method="post" action="https://www.metmeetings.org/raffle.php">
<input type="submit" class="btn btn-primary" name="Reset" value="Reset">
</form>
</p>

<?php

}
// } 

?>
</body>
</html>