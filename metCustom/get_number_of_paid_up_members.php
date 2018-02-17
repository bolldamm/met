#!/usr/local/bin/php.cli
<?php
/*
Write number of paid-up members at current time to a text file on the server 
*/

require "../classes/databaseConnection.php";
require "../database/connection.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_number_of_paid_up_members()";
$result=$db->callProcedure($codeProcedure);
$numMembers = mysqli_num_rows($result);
$date = date("Y/m/d h:i A");
$row = $date."\t".$numMembers.PHP_EOL;

$file = '/home/metmeetings/www/www/metCustom/number_of_members.txt';
//$file = 'number_of_members.txt';
$fp = fopen($file, 'a+');
fwrite($fp,$row);
fclose($fp);

?>