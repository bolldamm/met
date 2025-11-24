<?php
	/**
	 * 
	 * Script que muestra y realiza la edicion de una inscripcion conferencia
	 * @Author eData
	 * 
	 */

$idConferencia=$_GET["id_conferencia"];
$registrationId = $_GET["id_inscripcion"];

	//Si hemos enviado el formulario...
	if(count($_POST)){
		$idConferencia=$_POST["hdnIdConferencia"];


		//Obtenemos los talleres que habian elegidos
		$vectorTallerInscripcion=Array();
		$resultadoTallerInscripcion=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_taller_obtener(".$_POST["hdnIdInscripcion"].")");
		$i=0;
		while($datoTallerInscripcion=$db->getData($resultadoTallerInscripcion)){
			array_push($vectorTallerInscripcion,$datoTallerInscripcion["id_taller_fecha"]);
		}
		
		//Obtenemos los bloques de fechas de los talleres
		$resultadoTallerFecha=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_conferencia_fecha_bloque_obtener(".$idConferencia.",".$_SESSION["id_idioma"].")");
			
		$vectorTalleres=Array();
			
		//Fecha
		while($datoTallerFecha=$db->getData($resultadoTallerFecha)){
			if(isset($_POST["rdnFecha_".$datoTallerFecha["fecha"]])){
				array_push($vectorTalleres,$_POST["rdnFecha_".$datoTallerFecha["fecha"]]);
			}
		}
		
		
		//Obtener mini sessiones
		$resultadoTallerConferenciaMini=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_conferencia_obtener_concreto(".$registrationId.",".$idConferencia.",".$_SESSION["user"]["language_id"].",1)");
		if($db->getNumberRows($resultadoTallerConferenciaMini)>0){
			while($datoTallerConferenciaMini=$db->getData($resultadoTallerConferenciaMini)){
				if(isset($_POST["chkFechaMini_".$datoTallerConferenciaMini["fecha"]."_".$datoTallerConferenciaMini["id_taller_fecha"]])){
					array_push($vectorTalleres,$datoTallerConferenciaMini["id_taller_fecha"]);
				}//end if
			}//end if
		}//end if
		
		//Primero obtengo los talleres que se dejan de usar...
		$vectorTallerOut=Array();
		$totalTallerInscripcion=count($vectorTallerInscripcion);

		
		for($i=0;$i<$totalTallerInscripcion;$i++){
			if(!in_array($vectorTallerInscripcion[$i],$vectorTalleres)){
				array_push($vectorTallerOut,$vectorTallerInscripcion[$i]);
			}	
		}
		
		//Segundo obtengo los talleres que se ponen como nuevos
		$vectorTallerIn=Array();
		$totalTalleres=count($vectorTalleres);

		
		for($i=0;$i<$totalTalleres;$i++){
			if(!in_array($vectorTalleres[$i],$vectorTallerInscripcion)){
				array_push($vectorTallerIn,$vectorTalleres[$i]);
			}	
		}

		
		$totalTallerOut=count($vectorTallerOut);
		$totalTallerIn=count($vectorTallerIn);
		
		
		$vectorTalleresInsertar=Array();

		
		for($i=0;$i<$totalTallerIn;$i++){
			$resultadoTaller=$db->callProcedure("CALL ed_sp_taller_fecha_conferencia_obtener_verificaciones(".$_SESSION["id_idioma"].",".$vectorTallerIn[$i].")");
			$datoTaller=$db->getData($resultadoTaller);
			
			$vectorTalleresInsertar[$i]["id"]=$vectorTallerIn[$i];
			$vectorTalleresInsertar[$i]["precio"]=$datoTaller["precio"];

		}

		
		$db->startTransaction();
		
		
		//Borramos los que sobran
		for($i=0;$i<$totalTallerOut;$i++){
			$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_linea_modificacion_eliminar(".$_POST["hdnIdInscripcion"].",".$vectorTallerOut[$i].")");
		}
		
		
		for($i=0;$i<$totalTallerIn;$i++){
			$db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_insertar(".$_POST["hdnIdInscripcion"].",".$vectorTalleresInsertar[$i]["id"].",'".$vectorTalleresInsertar[$i]["precio"]."')");
		}
		

		
		//Link observation to conference registration ID
        $dinnerOptout = $_POST["optout"];
		$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_asignar_comentario(".$_POST["hdnIdInscripcion"].",'".generalUtils::escaparCadena($_POST["txtaComentario"])."','".$dinnerOptout."')");

    /************** INICIO: EXTRAS **************/
          $vectorExtras = Array();

    $vectorExtras[0]["id"] = 2;
    $vectorExtras[0]["valor"] = $_POST["dinner_guests"];

    $vectorExtras[1]["id"] = 7;
    $vectorExtras[1]["valor"] = $_POST["reception_guests"];

    //Insert extras (dinner guests and reception guests) in ed_sp_web_inscripcion_conferencia_extra_update
    //Changed "$i<=2" to "$i<count($vectorExtras)"
    for ($i = 0; $i < count($vectorExtras); $i++) {
        $db->callProcedure("CALL ed_sp_web_inscripcion_conferencia_extra_update(" . $_POST["hdnIdInscripcion"] . "," . $vectorExtras[$i]["id"] . "," . $vectorExtras[$i]["valor"] . ")");
    }
      
      if ($_POST["dinner_guests"] < $_POST["old_dinner_guests"])
      {
        $db->callProcedure("CALL ed_pr_delete_guest_details($registrationId,1)");
        $db->callProcedure("CALL ed_pr_delete_guest_dinner($registrationId)");
      }
      if ($_POST["reception_guests"] < $_POST["old_reception_guests"])
      {
        $db->callProcedure("CALL ed_pr_delete_guest_details($registrationId,2)");
      }     
      
    /************** FIN: EXTRAS **************/      
      

        //Update attendee photo
        //require "attendee_photo.php";

        $db->endTransaction();
		
		

		
		if($_POST["hdnVolver"]==0){
			generalUtils::redirigir("main_app.php?section=conference_registered&action=view&id_conferencia=".$_POST["hdnIdConferencia"]);
		}else{
			generalUtils::redirigir("main_app.php?section=conference_registered&action=edit&id_inscripcion=".$_POST["hdnIdInscripcion"]."&id_conferencia=".$_POST["hdnIdConferencia"]);
		}

	}

	
	if(!isset($registrationId) || !is_numeric($registrationId)){
		generalUtils::redirigir("main_app.php?section=conference&action=view");
	}
	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/conference/conference_registered/manage_conference_registered.html");
	
	
	require "../includes/load_format_date.inc.php";
	
	//Obtenemos workshops de conferencia actuales

	$resultadoTallerConferencia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_conferencia_obtener_concreto(".$registrationId.",".$idConferencia.",".$_SESSION["user"]["language_id"].",-1)");

	$fechaActual="";
	$esMini=false;
	while($datoTallerConferencia=$db->getData($resultadoTallerConferencia)){
		if($fechaActual!="" && $fechaActual!=$datoTallerConferencia["fecha"]){
			
			$fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
			$mesTaller = explode("/", $fechaTaller);
		
			//Proceso para obtener dia de la semana
			$fechaTrozeada=explode("-",$fechaActual);
			
			
			
			$fechaTimeStamp=mktime(0,0,0,$mesTaller[1],$mesTaller[0],$mesTaller[2]);
			$diaSemana=$vectorSemana[date("N",$fechaTimeStamp)];
			
			$subPlantilla->assign("CONFERENCIA_TALLER_FECHA_BLOQUE", $diaSemana.", ".intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]]);
			
			//Miramos si hay mini...
			if($esMini){
				$subPlantilla->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
			}//end if
			
			$subPlantilla->assign("ITEM_MINI_DISABLED","");
			//$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",$fechaActual);
			$subPlantilla->parse("contenido_principal.bloque_fecha_taller");
		}
		
		$literalFull="";		
		$subPlantilla->assign("CONFERENCIA_TALLER",$datoTallerConferencia["nombre"].$literalFull);
		$subPlantilla->assign("CONFERENCIA_FECHA",$datoTallerConferencia["fecha"]);
		$subPlantilla->assign("CONFERENCIA_ID_TALLER_FECHA",$datoTallerConferencia["id_taller_fecha"]);
		$subPlantilla->assign("CONFERENCIA_ID_TALLER",$datoTallerConferencia["id_taller"]);
		if($datoTallerConferencia["seleccionado"]==0){
			$subPlantilla->assign("CHECKED_TALLER","");
			
			if($datoTallerConferencia["total_inscritos"]>=$datoTallerConferencia["plazas"]){
				$literalFull="<span style='color:#8B1513'> FULL!</span>";
				$subPlantilla->assign("ITEM_TALLER_DISABLED","disabled='true'");
			}
			
		}else{
			$idConferencia=$datoTallerConferencia["seleccionado"];
			$subPlantilla->assign("CHECKED_TALLER","checked");
			$subPlantilla->assign("TALLER_FECHA_VALOR",$datoTallerConferencia["id_taller_fecha"]);
		}
		
		if($datoTallerConferencia["es_mini"]==1){
			$subPlantilla->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini.fila_conferencia_taller_mini");
			$esMini=true;
		}else{
			if($datoTallerConferencia["seleccionado"]>0){
				$subPlantilla->assign("ITEM_MINI_DISABLED","disabled='true'");
			}
			$subPlantilla->parse("contenido_principal.bloque_fecha_taller.fila_conferencia_taller");
		}


		
		$subPlantilla->assign("ITEM_TALLER_DISABLED","");
		

		
		$fechaActual=$datoTallerConferencia["fecha"];
	}
	
	if($fechaActual!=""){
		$fechaTaller = generalUtils::conversionFechaFormato($fechaActual, "-", "/");
		$mesTaller = explode("/", $fechaTaller);
	
		//Proceso para obtener dia de la semana
		$fechaTrozeada=explode("-",$fechaActual);
		$fechaTimeStamp=mktime(0,0,0,$mesTaller[1],$mesTaller[0],$mesTaller[2]);
		$diaSemana=$vectorSemana[date("N",$fechaTimeStamp)];
		
		$subPlantilla->assign("CONFERENCIA_TALLER_FECHA_BLOQUE", $diaSemana.", ".intval($fechaTrozeada[2])." ".$vectorMes[$fechaTrozeada[1]]);
		
		
		//Miramos si hay mini...
		if($esMini){
			$subPlantilla->parse("contenido_principal.bloque_fecha_taller.bloque_taller_mini");
		}//end if
		
		//$plantillaFormulario->assign("CONFERENCIA_TALLER_FECHA_BLOQUE",$fechaActual);
		$subPlantilla->parse("contenido_principal.bloque_fecha_taller");
	}
		

	
	$resultadoConferencia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_obtener_concreta(".$idConferencia.",".$_SESSION["user"]["language_id"].")");
	$datoConferencia=$db->getData($resultadoConferencia);

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONFERENCE_EDIT_CONFERENCE_LINK."&id_conferencia=".$idConferencia;
	$vectorMigas[2]["texto"]=$datoConferencia["nombre"];
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_LINK."&id_conferencia=".$idConferencia;
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_TEXT;
	$vectorMigas[4]["url"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_EDIT_CONFERENCE_REGISTERED_LINK."&id_inscripcion=".$registrationId."&id_conferencia=".$idConferencia;
	$vectorMigas[4]["texto"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_EDIT_CONFERENCE_REGISTERED_TEXT;


	
	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_INSCRIPCION",$registrationId);
	$subPlantilla->assign("ID_CONFERENCIA",$idConferencia);
	$subPlantilla->assign("ACTION","edit");
	
		$resultadoAttendee=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_pr_inscripcion_conferencia_get_options(".$registrationId.")");
	$datoAttendee=$db->getData($resultadoAttendee);

$subPlantilla->assign("ATTENDEE_NAME",$datoAttendee["nombre"] . " " . $datoAttendee["apellidos"]);

	//Comentario
	$resultadoComentario=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_obtener_comentario(".$registrationId.")");
	$datoComentario=$db->getData($resultadoComentario);
	
	$subPlantilla->assign("CONFERENCIA_REGISTRO_DESCRIPCION",htmlspecialchars($datoComentario["observaciones"]));
	$subPlantilla->assign("ACTION","edit");

	//Attendee list
    //Image to display when clicking on the "view image" icon
    $resultAttendeeList = $db->callProcedure("CALL ed_sp_get_specific_attendee_list_details(" . $registrationId . ")");
	$dataAttendeeList=$db->getData($resultAttendeeList);
      	$subPlantilla->assign("CONFERENCIA_PASSWORD",$dataAttendeeList["conference_password"]);
//        if ($dataAttendeeList["imagen"] != "") {
//            $subPlantilla->assign("ATTENDEE_PHOTO", $dataAttendeeList["imagen"]);
//        } else {
//            $subPlantilla->assign("ATTENDEE_PHOTO", "default.jpg");
//        }
      if ($dataAttendeeList["es_dinner"] == 1) {
        $subPlantilla->assign("DINNER_CHECK", "checked");
      } else {
        $subPlantilla->assign("DINNER_CHECK", "");
      }
      $result = $db->callProcedure("CALL ed_pr_get_guests($registrationId,7)");
      $row = $result->fetch_assoc();        
      $subPlantilla->assign("RECEPTION_GUESTS", $row["valor"]);
      $result = $db->callProcedure("CALL ed_pr_get_guests($registrationId,2)");
      $row = $result->fetch_assoc();
      $subPlantilla->assign("DINNER_GUESTS", $row["valor"]);
      
        // Prepare badge text
      
      // $txtBadge = str_replace("<br />", "\n", $dataAttendeeList["conference_badge"]);
      // $txtBadge = str_replace("&comma;", ",", $txtBadge);
      //  $subPlantilla->assign("CONFERENCIA_BADGE", $txtBadge);

        //Display current attendee list permission
      //  if ($dataAttendeeList["es_attendee_list"] == 1) {
      //      $subPlantilla->assign("LIST_PERMISSION", "checked");
      //  } else {
      //      $subPlantilla->assign("LIST_PERMISSION", "");
      //  }
    



//Informacion del usuario
require "includes/load_information_user.inc.php";

//Incluimos save & clsoe
$subPlantilla->parse("contenido_principal.item_button_close");


//Incluimos proceso onload
$plantilla->parse("contenido_principal.carga_inicial");



//Contruimos plantilla secundaria
$subPlantilla->parse("contenido_principal");

//Exportamos plantilla secundaria a la plantilla principal
$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));

//Construimos plantilla principal
$plantilla->parse("contenido_principal");

//Mostramos plantilla principal por pantalla
$plantilla->out("contenido_principal");
?>
}