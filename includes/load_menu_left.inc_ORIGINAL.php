<?php
	/**
	 * 
	 * Presentamos por pantalla el menu lateral con los hijos
	 * @author Edata S.L.
	 * @copyright Edata S.L.
	 * @version 1.0
	 */



	$resultMenuLeft = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[0].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
	$totalMenuPadre = $db->getNumberRows($resultMenuLeft);
	if($totalMenuPadre > 0) {
		//Instanciamos las plantilla del menu
		$plantillaMenuLeft = new XTemplate("html/includes/menu_left.inc.html");
		while($dataMenuLeft = $db->getData($resultMenuLeft)) {
			$esMostrableSubMenu=true;
			
			if(isset($_SESSION["met_user"])){
				if($dataMenuLeft["id_formulario"]==3 && ($_SESSION["met_user"]["tipoUsuario"]!=1 || $_SESSION["met_user"]["institution_id"]!="")){
					$esMostrableSubMenu=false;
				}else if($dataMenuLeft["id_modulo"]==12 && $_SESSION["met_user"]["tipoUsuario"]==4){
					$esMostrableSubMenu=false;
				}
			}
	
			$plantillaMenuLeft->assign("ITEM_MENU_LEFT_NOMBRE", $dataMenuLeft["nombre"]);
			//$plantillaMenuLeft->assign("ITEM_MENU_LEFT_URL", $dataMenuLeft["url"]."?menu=".$dataMenuLeft["id_menu"]);
			
			$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
			$vectorAtributosMenu["id_menu"]=$dataMenuLeft["id_menu"];
			$vectorAtributosMenu["seo_url"]=$dataMenuLeft["seo_url"];

			$vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeft["id_modulo"],$dataMenuLeft["id_menu"],$dataMenuLeft["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);
				
	
			$plantillaMenuLeft->assign("ITEM_MENU_LEFT_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
			//$plantillaMenuLeft->assign("ITEM_MENU_LEFT_URL", $vectorAtributosMenu["seo_url"]."?menu=".$vectorAtributosMenu["id_menu"]);
			
			$plantillaMenuLeft->assign("ITEM_MENU_LEFT_ACTIVO", ($dataMenuLeft["id_menu"] == $idMenu) ? "current" : "");
			
			//EL menu hijo que este activo, comprobamos si tiene hijos
			if(isset($explodeMenuId[1]) && $explodeMenuId[1] == $dataMenuLeft["id_menu"]) {
				$resultMenuLeftHijo = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[1].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
				if($db->getNumberRows($resultMenuLeftHijo) > 0) {
					while($dataMenuLeftHijo = $db->getData($resultMenuLeftHijo)) {
						
						$plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_NOMBRE", $dataMenuLeftHijo["nombre"]);

						$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
						$vectorAtributosMenu["id_menu"]=$dataMenuLeftHijo["id_menu"];
						$vectorAtributosMenu["seo_url"]=$dataMenuLeftHijo["seo_url"];
			
						$vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftHijo["id_modulo"],$dataMenuLeftHijo["id_menu"],$dataMenuLeftHijo["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);
							
				
						$plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
						
						//$plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_URL", $dataMenuLeftHijo["url"]."?menu=".$dataMenuLeftHijo["id_menu"]);
						
						$plantillaMenuLeft->assign("ITEM_MENU_LEFT_HIJO_ACTIVO", ($dataMenuLeftHijo["id_menu"] == $idMenu) ? "current" : "");
						
						if(isset($explodeMenuId[2]) && $explodeMenuId[2] == $dataMenuLeftHijo["id_menu"]) {
							$resultMenuLeftSubHijo = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[2].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
							if($db->getNumberRows($resultMenuLeftSubHijo) > 0) {
								while($dataMenuLeftSubHijo = $db->getData($resultMenuLeftSubHijo)) {
									$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_NOMBRE", $dataMenuLeftSubHijo["nombre"]);

									$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
									$vectorAtributosMenu["id_menu"]=$dataMenuLeftSubHijo["id_menu"];
									$vectorAtributosMenu["seo_url"]=$dataMenuLeftSubHijo["seo_url"];
			
									$vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftSubHijo["id_modulo"],$dataMenuLeftSubHijo["id_menu"],$dataMenuLeftSubHijo["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);
							
				
									$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
				
									
									//$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_URL", $dataMenuLeftSubHijo["url"]."?menu=".$dataMenuLeftSubHijo["id_menu"]);
									
									$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_ACTIVO", ($dataMenuLeftSubHijo["id_menu"] == $idMenu) ? "current" : "");
		
									if(isset($explodeMenuId[3]) && $explodeMenuId[3] == $dataMenuLeftSubHijo["id_menu"]) {
										$resultMenuLeftNieto = $db->callProcedure("CALL ed_sp_web_menu_obtener_listado(".$explodeMenuId[3].", ".$idMenuTipo.",".$_SESSION["id_idioma"].", 1)");
										if($db->getNumberRows($resultMenuLeftNieto) > 0) {
											while($dataMenuLeftNieto = $db->getData($resultMenuLeftNieto)) {
												$plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_NOMBRE", $dataMenuLeftNieto["nombre"]);
												
												$vectorAtributosMenu["idioma"]=$_SESSION["siglas"];
												$vectorAtributosMenu["id_menu"]=$dataMenuLeftNieto["id_menu"];
												$vectorAtributosMenu["seo_url"]=$dataMenuLeftNieto["seo_url"];
						
												$vectorAtributosMenu=generalUtils::generarUrlMenuContenido($db,$dataMenuLeftNieto["id_modulo"],$dataMenuLeftNieto["id_menu"],$dataMenuLeftNieto["descripcion"],$_SESSION["id_idioma"],$vectorAtributosMenu);
										
							
												$plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_URL",generalUtils::generarUrlAmigableMenu($vectorAtributosMenu));
												
												
												//$plantillaMenuLeft->assign("ITEM_MENU_LEFT_SUBHIJO_URL", $dataMenuLeftSubHijo["url"]."?menu=".$dataMenuLeftSubHijo["id_menu"]);
												
												$plantillaMenuLeft->assign("ITEM_MENU_LEFT_NIETO_ACTIVO", ($dataMenuLeftNieto["id_menu"] == $idMenu) ? "current" : "");
												
												$plantillaMenuLeft->assign("ESTILO_NIETO","style='padding-left:8px;'");
												$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo.menu_left_nieto.item_menu_left_nieto");
												
											}
											$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo.menu_left_nieto");
										}
									}
									$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo.item_menu_left_subhijo");
								}

								$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo.menu_left_subhijo");
							}
						}
						
						
						$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo.item_menu_left_hijo");
					}
					$plantillaMenuLeft->parse("contenido_principal.item_menu_left.menu_left_hijo");
				}
			}
			
			if($esMostrableSubMenu){
				$plantillaMenuLeft->parse("contenido_principal.item_menu_left");
			}
		}
		
		$plantillaMenuLeft->parse("contenido_principal");
		
		$plantilla->assign("MENU_LEFT", $plantillaMenuLeft->text("contenido_principal"));
	}
?>