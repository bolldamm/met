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
			case SECTION_MENU:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					if(gestorGeneralUtils::tienePermisoUsuarioMenu($db, $vectorItem[$i], 2)){								
						//Obtenemos las imagenes de cada menu
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_archivo_obtener(".$vectorItem[$i].",'')");
						while($dato=$db->getData($resultado)){
							unlink("../files/menu/".$dato["nombre"]);
						}
						$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_menu_eliminar(".$vectorItem[$i].")");
					}
					$parametroAdicional="&hdnIdPosicion=".$_POST["hdnIdPosicion"];
				}	
				break;
			case SECTION_NEW:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					//Obtenemos imagen de noticia
					/*
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_obtener_imagen(".$vectorItem[$i].")");
					$dato=$db->getData($resultado);
					*/
					//Logo
					/*
					if($dato["imagen"]!=""){
						unlink("../files/news/".$dato["imagen"]);
					}
					*/
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_noticia_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_THEMATIC:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tematica_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_JOB:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_oferta_trabajo_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_MOVEMENT:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_movimiento_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_CONCEPT:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_PAYMENT_TYPE:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_tipo_pago_movimiento_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_SUCCESS:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					//Obtenemos logo del proyecto
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_obtener_imagen(".$vectorItem[$i].")");
					$dato=$db->getData($resultado);
					if($dato["imagen"]!=""){
						unlink("../files/institutional/".$dato["imagen"]);
					}

					//Eliminamos caso de exito
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_caso_exito_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_ACCESS_BUTTON:
				for($i=0;$i<$totalItem;$i++){
					//Obtenemos logo del proyecto
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_obtener_imagen(".$vectorItem[$i].")");
					while($dato=$db->getData($resultado)){
						if($dato["imagen"]!=""){
							unlink("../files/sections/".$dato["imagen"]);
						}
					}
					//Eliminamos boton acceso
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_boton_acceso_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_DIARY:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					//Obtenemos las imagenes de cada noticia
					/*
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_archivo_listar(".$vectorItem[$i].")");
					while($dato=$db->getData($resultado)){
						unlink("../files/diary/".$dato["nombre"]);
					}
					*/
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_agenda_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_MEMBER:
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_eliminar(".$vectorItem[$i].")");
				}
				break;	
			case SECTION_CONFERENCE:
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_eliminar(".$vectorItem[$i].")");
				}
				break;		
			case SECTION_WORKSHOP:
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_taller_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_ELETTER:
				//Recorremos array
				for($i=0;$i<$totalItem;$i++){
					$resultado=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_novedad_eliminar(".$vectorItem[$i].")");
				}
				break;
			case SECTION_INVOICE:
				//Recorremos array
				$skippedInvoices = array();
				$deletedCount = 0;
				for($i=0;$i<$totalItem;$i++){
					$idFactura = intval($vectorItem[$i]);

					//Check if invoice is registered with Verifactu
					$resultadoCheck = $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_obtener_concreto(".$idFactura.")");
					$datoCheck = $db->getData($resultadoCheck);

					if (!empty($datoCheck["verifactu_uuid"])) {
						//Invoice is registered with Verifactu - cannot delete
						$skippedInvoices[] = $datoCheck["numero_factura"];
					} else {
						//Safe to delete
						$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_eliminar(".$idFactura.")");
						$deletedCount++;
					}
				}

				//If any invoices were skipped, store message in session
				if (count($skippedInvoices) > 0) {
					$_SESSION["verifactu_warning"] = "Cannot delete Verifactu-registered invoices: " . implode(", ", $skippedInvoices) . ". These invoices have been submitted to AEAT and must be preserved. Use 'Anular Verifacti' to cancel them instead. Deleted " . $deletedCount . " other invoice(s).";
				}
				break;					
		}
		
		if(isset($_POST["id_padre"]) && $_POST["id_padre"]!=""){
			$parametroPadre="&id_padre=".$_POST["id_padre"];
		}else{
			$parametroPadre="";
		}
		
		generalUtils::redirigir("main_app.php?section=".$_POST["section"]."&action=view&pagina=".$_POST["pagina"]."&hdnOrden=".$_POST["hdnOrden"]."&hdnRegistros=".$_POST["hdnRegistros"].$parametroPadre.$parametroAdicional);
	}
?>