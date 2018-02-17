<?php
	require "includes/load_main_components.inc.php";
	require "includes/load_validate_user.inc.php";
	require "config/constants.php";
	require "config/dictionary/".$_SESSION["user"]["language_dictio"];
	require "config/sections.php";
	
	//Si hemos enviado form por post
	if(count($_POST)>0){
		
		if($_SESSION["user"]["rol_global"]!=1){
			/**
			 * 
			 * Incluimos la informacion sobre las secciones,acciones y las rutas de acceso al script(Ahora podremos controlar permisos mas facilmente)
			 * 
			 */
			require "config/controller.php";
			$idSeccion=$controller[$_POST["section"]][ID_SECTION];
			
			//Si somos una seccion a controlar...
			if($idSeccion!=-1 && !gestorGeneralUtils::tienePermisoUsuarioSeccion($db, $idSeccion, 2)){
				die();
			}
		}
		
		/**
		 * 
		 * La cadena de los id separado por el caracter, se ha pasado a un array.
		 * @var array
		 * 
		 */
		$vectorItem=array_filter(explode(",",$_POST["hdnId"]));
		/**
		 * 
		 * Total items del array
		 * @var int
		 * 
		 */
		$totalItem=count($vectorItem);

		$parametroAdicional="";
		switch($_POST["section"]){
			case SECTION_CONFERENCE_REGISTERED:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_reactivar(".$vectorItem[$i].")");
				}
				$parametroAdicional="&id_conferencia=".$_POST["id_conferencia"]."&cmbEstadoInscripcion=".$_POST["cmbEstadoInscripcion"];	
				break;
			case SECTION_WORKSHOP_DATE:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$idCompleto=explode("-",$vectorItem[$i]);
					$idInscripcion=$idCompleto[0];
					$idLineaInscripcion=$idCompleto[1];
					if($_POST["id_destino"]==1){

						//Workshop perteneciente a un workshop inscrito desde el workshop form
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_total_obtener(".$idInscripcion.")");
						$totalRegistros=$db->getNumberRows($resultado);
			
						if($totalRegistros>1){
								$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_linea_eliminar(".$idLineaInscripcion.")");
						}else if($totalRegistros==1){
							//Si solo hay taller asociado, entonces a parte de poner a borrado la linea, pasaremos a cancelar la inscripcion global
							$db->startTransaction();
				
							//Borramos logicamente la inscripcion linea
							$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_linea_eliminar(".$idLineaInscripcion.")");
							
							//Cancelamos inscripcion global
							$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_taller_cancelar(".$idInscripcion.")");
							
							$db->endTransaction();
						}
					}else{
						//Workshop perteneciente a un workshop inscrito desde el conference form
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_total_obtener(".$idInscripcion.")");
						$totalRegistros=$db->getNumberRows($resultado);
			
						if($totalRegistros>1){
								$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_linea_eliminar(".$idLineaInscripcion.")");
						}else if($totalRegistros==1){
							//Si solo hay taller asociado, entonces a parte de poner a borrado la linea, pasaremos a cancelar la inscripcion global
							$db->startTransaction();
				
							//Borramos logicamente la inscripcion linea
							$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_linea_eliminar(".$idLineaInscripcion.")");
							
							//Cancelamos inscripcion global
							$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_cancelar(".$idInscripcion.")");
							
							$db->endTransaction();
						}
						
					}
					//$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_cancelar(".$vectorItem[$i].")");
				}
				$parametroAdicional="&id_taller=".$_POST["id_taller"]."&cmbFecha=".$_POST["cmbFecha"];	
				break;
		}
		
		generalUtils::redirigir("main_app.php?section=".$_POST["section"]."&action=view&pagina=".$_POST["pagina"]."&hdnOrden=".$_POST["hdnOrden"]."&hdnRegistros=".$_POST["hdnRegistros"].$parametroPadre.$parametroAdicional);
	}
?>