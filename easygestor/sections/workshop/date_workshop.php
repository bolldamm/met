<?php
	/**
	 * 
	 * Este fichero trata las personas y sus N areas asociadas
	 * 
	 */

	$vectorIdFecha=array_filter(explode(",",$_POST["hdnIdFecha"]));
	$totalVectorIdFecha=count($vectorIdFecha);

	//Recorremos todos los inputs de url
	for($i=0;$i<$totalVectorIdFecha;$i++){
		if(isset($_POST["hdnIdFechaConcreta_".$vectorIdFecha[$i]])){
			$idFechaConcreta=$_POST["hdnIdFechaConcreta_".$vectorIdFecha[$i]];
		}else{
			$idFechaConcreta=0;
		}

		$fecha=generalUtils::conversionFechaFormato($_POST["txtFecha_".$vectorIdFecha[$i]]);
		$precio=$_POST["txtPrecio_".$vectorIdFecha[$i]];
		$precioSister=$_POST["txtPrecioSister_".$vectorIdFecha[$i]];
		$precioNoSocio=$_POST["txtPrecioNoSocio_".$vectorIdFecha[$i]];
		$plaza=$_POST["txtPlaza_".$vectorIdFecha[$i]];
		
		//Miramos si es requerido ser miembro para inscribirse o no
		$esMiembro=0;
		if(isset($_POST["chkEsMiembro_".$vectorIdFecha[$i]])){
			$esMiembro=1;
		}
		
		//Miramos si es conferencia o no
		$esConferencia=0;
		if(isset($_POST["chkEsConferencia_".$vectorIdFecha[$i]])){
			$esConferencia=1;
		}

		if(isset($_POST["hdnEliminarFecha_".$vectorIdFecha[$i]]) && $_POST["hdnEliminarFecha_".$vectorIdFecha[$i]]==1){
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_fecha_eliminar_concreto(".$idFechaConcreta.")");
		}else{
			$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_fecha_guardar(".$idFechaConcreta.",".$idTaller.",'".$fecha."','".$precio."','".$precioSister."','".$precioNoSocio."',".$plaza.",".$esMiembro.",".$esConferencia.")");
		}
	}