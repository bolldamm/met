<?php

	require "classes/databaseConnection.php";
	require "classes/generalUtils.php";
	require "database/connection.php";
	require "includes/load_template.inc.php";

$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_agenda_test()";
$resultado=$db->callProcedure($codeProcedure);

//Export to csv
// header("Content-Disposition: attachment; filename=current_workshops.txt");
// header("Content-type: text/csv; charset=UTF-8");

//Output header row
// echo "ID\tURL\r\n";
$agregate = "[";
//Output names
while($dato=$db->getData($resultado)){
$fecha = $dato['fecha'];
$titulo = $dato['titulo'];
$titulo = str_replace("'","",$titulo);
$time = $dato['descripcion_previa'];
$link = $dato['descripcion_completa'];
  
  if ($link) {
    $summary = "', summary: '<a href=\"".$link."\">".$titulo."<br><br><small><i>Click for details</i></small><a>'},";
} else {
    $summary = "', summary: '".$titulo."'},";
}
  
  $agregate = $agregate."{ startDate: '".$fecha." ".$time."', endDate: '".$fecha.$summary;
}
$agregate = $agregate."]";
// echo json_encode($dato);
 echo $agregate;

    $phpArray = array(
        0 => "Mon","Fred", 
        1 => "Tue", 
        2 => "Wed", 
        3 => "Thu",
        4 => "Fri", 
        5 => "Sat",
        6 => "Sun",
    )
      
?>

<!-- <script type="text/javascript">

    var jArray = <?php echo json_encode($resultEventos); ?>;
    
    alert(jArray);

    for(var i=0; i<jArray.length; i++){
        alert(jArray[i]);
    } 

 </script>

<html>
  <body>
    <?php echo json_encode($banana); ?>
  </body>
</html>  -->