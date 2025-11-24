<?php
	require "includes/load_main_components.inc.php";
	require "includes/load_validate_user.inc.php";
	require "config/constants.php";
	require "config/dictionary/".$_SESSION["user"]["language_dictio"];
	require "config/sections.php";
	require "classes/excel/PHPExcel.php";
	
	//Si hemos enviado form por post
	if(count(GET)>0){
		$parametroAdicional="";
		switch($_GET["section"]){
			case SECTION_MOVEMENT:
				
				//Declaramos excel
				$objPHPExcel = new PHPExcel();

				//Accounts met type
				if($_GET["mode"]==1){
				
					//Encabezados
					$objPHPExcel->getActiveSheet()->setCellValue("A3", STATIC_VIEW_MOVEMENT_ACCOUNT_TYPE);
					$objPHPExcel->getActiveSheet()->setCellValue("B3", STATIC_VIEW_MOVEMENT_ACCOUNT);
					$objPHPExcel->getActiveSheet()->setCellValue("C3", STATIC_VIEW_MOVEMENT_OUTGOING);
					$objPHPExcel->getActiveSheet()->setCellValue("D3", STATIC_VIEW_MOVEMENT_INCOMING);
					$objPHPExcel->getActiveSheet()->setCellValue("E3", STATIC_VIEW_MOVEMENT_BALANCE);
					
					//Poner en negrita
					$objPHPExcel->getActiveSheet()->getStyle("A3:E3")->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
					
					
					//Titulo primero
					$objPHPExcel->getActiveSheet()->setCellValue("A1", STATIC_VIEW_MOVEMENT_MET_ACCOUNTS_TYPE);
					$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(24);
					$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
					
					//Segundo titulo
					$objPHPExcel->getActiveSheet()->setCellValue("A2", STATIC_VIEW_MOVEMENT_BETWEEN." {$_GET["from"]} ".STATIC_VIEW_MOVEMENT_AND." {$_GET["to"]}");
					$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(14);
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:C2');
				
					$resultadoConceptoPadre=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_padre_relacionado('".generalUtils::conversionFechaFormato($_GET["from"])."','".generalUtils::conversionFechaFormato($_GET["to"])."',".$_SESSION["user"]["language_id"].")");
	
					$filaInicial=4;
					$vectorCeldaSalida=Array();
					$vectorCeldaEntrada=Array();
					$vectorCeldaBalance=Array();
					while($datoConceptoPadre=$db->getData($resultadoConceptoPadre)){
						$objPHPExcel->getActiveSheet()->setCellValue("A".$filaInicial, $datoConceptoPadre["concepto"]);
						$filaInicial++;
						
						
						$resultadoConceptoHijo=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_hijo_relacionado(".$datoConceptoPadre["id_padre"].",'".generalUtils::conversionFechaFormato($_GET["from"])."','".generalUtils::conversionFechaFormato($_GET["to"])."',".$_SESSION["user"]["language_id"].")");
						while($datoConceptoHijo=$db->getData($resultadoConceptoHijo)){
							$filaInicialAuxiliar=$filaInicial;
							//Introducir datos
							$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, $datoConceptoHijo["concepto"]);
							$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, $datoConceptoHijo["salida"]);
							$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, $datoConceptoHijo["entrada"]);
							$objPHPExcel->getActiveSheet()->setCellValue("E".$filaInicial, "=SUM(D".$filaInicial."-C".$filaInicial.")");
							$filaInicial++;
						}
						
						//Subtotal literal
						$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, STATIC_VIEW_MOVEMENT_SUBTOTAL);
						$objPHPExcel->getActiveSheet()->getStyle("B".$filaInicial)->getFont()->setBold(true);
			
						
						
						
						
						//1.Salida
						$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, "=SUM(C".($filaInicialAuxiliar-1).":C".($filaInicial-1).")");
						array_push($vectorCeldaSalida,"C".$filaInicial);
						
						//2.Entrada
						$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, "=SUM(D".($filaInicialAuxiliar-1).":D".($filaInicial-1).")");
						array_push($vectorCeldaEntrada,"D".$filaInicial);
						
						//3.Balance
						$objPHPExcel->getActiveSheet()->setCellValue("E".$filaInicial, "=SUM(E".($filaInicialAuxiliar-1).":E".($filaInicial-1).")");
						array_push($vectorCeldaBalance,"E".$filaInicial);
						$filaInicial++;
						
					}
					
					if($filaInicial>4){
						//Total literal
						$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, STATIC_VIEW_MOVEMENT_TOTAL);
						$objPHPExcel->getActiveSheet()->getStyle("B".$filaInicial)->getFont()->setBold(true);
						
						//Total salida
						$celdasSalida=implode("+",$vectorCeldaSalida);
						$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, "=SUM(".$celdasSalida.")");
						
						//Total entrada
						$celdasSalida=implode("+",$vectorCeldaEntrada);
						$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, "=SUM(".$celdasSalida.")");
						
						//Total balance
						$celdasSalida=implode("+",$vectorCeldaBalance);
						$objPHPExcel->getActiveSheet()->setCellValue("E".$filaInicial, "=SUM(".$celdasSalida.")");	
					}
					
					$fichero="met_accounts_by_type.xls";
					
				}else if($_GET["mode"]==2){
					//Accounts summary
					
					//Encabezados
					$objPHPExcel->getActiveSheet()->setCellValue("A3", STATIC_VIEW_MOVEMENT_ACCOUNT);
					$objPHPExcel->getActiveSheet()->setCellValue("B3", STATIC_VIEW_MOVEMENT_TOTAL_OUTGOING);
					$objPHPExcel->getActiveSheet()->setCellValue("C3", STATIC_VIEW_MOVEMENT_TOTAL_INCOMING);
					$objPHPExcel->getActiveSheet()->setCellValue("D3", STATIC_VIEW_MOVEMENT_BALANCE);
					
					//Poner en negrita
					$objPHPExcel->getActiveSheet()->getStyle("A3:D3")->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
					$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
					
					
					//Titulo primero
					$objPHPExcel->getActiveSheet()->setCellValue("A1", STATIC_VIEW_MOVEMENT_MET_ACCOUNTS_SUMMARY);
					$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(24);
					$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:C1');
					
					//Segundo titulo
					$objPHPExcel->getActiveSheet()->setCellValue("A2", STATIC_VIEW_MOVEMENT_BETWEEN." {$_GET["from"]} ".STATIC_VIEW_MOVEMENT_AND." {$_GET["to"]}");
					$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(14);
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A2:C2');
					
					
					//Fila inicial
					$filaInicial=4;
					$filaInicialAuxiliar=$filaInicial;
					$resultadoConceptoHijo=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_concepto_movimiento_hijo_resumen('".generalUtils::conversionFechaFormato($_GET["from"])."','".generalUtils::conversionFechaFormato($_GET["to"])."',".$_SESSION["user"]["language_id"].")");
					while($datoConceptoHijo=$db->getData($resultadoConceptoHijo)){
						//Introducir datos
						$objPHPExcel->getActiveSheet()->setCellValue("A".$filaInicial, $datoConceptoHijo["concepto"]);
						$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, $datoConceptoHijo["salida"]);
						$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, $datoConceptoHijo["entrada"]);
						$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, "=SUM(C".$filaInicial."-B".$filaInicial.")");
						$filaInicial++;
					}
					
					
					if($filaInicial>4){
						//Total literal
						$objPHPExcel->getActiveSheet()->setCellValue("A".$filaInicial, STATIC_VIEW_MOVEMENT_TOTAL);
						$objPHPExcel->getActiveSheet()->getStyle("A".$filaInicial)->getFont()->setBold(true);
						
						//Total salida
						$objPHPExcel->getActiveSheet()->setCellValue("B".$filaInicial, "=SUM(B".$filaInicialAuxiliar.":B".($filaInicial-1).")");
						
						//Total entrada
						$objPHPExcel->getActiveSheet()->setCellValue("C".$filaInicial, "=SUM(C".$filaInicialAuxiliar.":C".($filaInicial-1).")");
						
						//Total balance
						$objPHPExcel->getActiveSheet()->setCellValue("D".$filaInicial, "=SUM(D".$filaInicialAuxiliar.":D".($filaInicial-1).")");
					}
					
					
					
					$fichero="met_accounts_summary.xls";
					
				}
				
				$objPHPExcel->setActiveSheetIndex(0);
					
				
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment;filename=".$fichero);
				header("Cache-Control: max-age=0");
								
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');



				break;
			default:
				generalUtils::redirigir("index.php");
		}
	}
?>