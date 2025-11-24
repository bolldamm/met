<?php
/*
* Get a list of new members
* by ID
*/

	require "../classes/databaseConnection.php";
	require "../classes/generalUtils.php";
	require "../database/connection.php";
	require "../includes/load_template.inc.php";

$asOfId=$_GET["id_p"];

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_pr_get_list_of_new_members2('".$asOfId."')";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
//header("Content-Disposition: attachment; filename=new_members.txt");
//header("Content-type: text/csv; charset=UTF-8");

//Export to Excel (some encoding problems)
header("Content-Disposition: attachment; filename=new_members.xls");
header("Content-type: application/vnd.ms-excel, charset = utf-8"); 

//Output header row
echo "First name\tLast name\tEmail\tCountry\tDate\tNewsletter Permission\tQualifications\tHearAbout\n";

//Output names
while($dato=$db->getData($resultado)){
$firstName = $dato['nombre'];
$lastName = $dato['apellidos'];
$email = $dato['correo_electronico'];
$country = $dato['nombre_original'];
$date = $dato['fecha_inscripcion'];
$newsletter = $dato['newsletter_permission'];
$qualifications = $dato['cualificaciones'];
$hearAbout =  $dato['descripcion'];
  
echo iconv("UTF-8", "UTF-16LE//IGNORE", $firstName);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $lastName);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $email);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $country);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $date);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $newsletter);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $qualifications);
echo "\t";
echo iconv("UTF-8", "UTF-16LE//IGNORE", $hearAbout).PHP_EOL;
    
// echo $firstName."\t".$lastName."\t".$email."\t".$country."\t".$date."\t".$newsletter."\t".$qualifications."\t".$hearAbout."\r\n";
}

?>