<?php
	/**
	 * 
	 * Listamos todos los menus existentes en el sistema
	 * @Author eData
	 * 
	 */

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/conference/conference_registered/view_conference_registered.html");
	
	$mostrarPaginador=true;
	$valorDefecto=0; // default sort is by name (name = 0, RegID = 2, see matrizOrden below)
	$campoOrden="nombre_completo";
	$direccionOrden="DESC";
	//$numeroRegistrosPagina=DEFAULT_FILTER_NUMBER;
	$numeroRegistrosPagina=200;

	/**
	 * 
	 * $matrizOrden, la primera dimension de la matriz nos es el indice numerico, que es el que se usara como value del combobox.
	 * la parte asociativa "descripcion" nos indica la palabra que vera el usuario del gestor y la parte valor es el campo de la base de datos
	 * 
	 */
	$matrizOrden[0]["descripcion"]=STATIC_ORDER_CONFERENCE_REGISTERED_NAME_FIELD;
	$matrizOrden[0]["valor"]="nombre_completo";
	$matrizOrden[1]["descripcion"]=STATIC_ORDER_CONFERENCE_REGISTERED_EMAIL_FIELD;
	$matrizOrden[1]["valor"]="correo_electronico";
	$matrizOrden[2]["descripcion"]=STATIC_ORDER_CONFERENCE_REGISTERED_INSCRIPTION_NUMBER_FIELD;
	$matrizOrden[2]["valor"]="numero_inscripcion";
	$matrizOrden[3]["descripcion"]=STATIC_ORDER_CONFERENCE_REGISTERED_AMOUNT_FIELD;
	$matrizOrden[3]["valor"]="importe_total";
	$matrizOrden[4]["descripcion"]=STATIC_ORDER_CONFERENCE_REGISTERED_PAID_FIELD;
	$matrizOrden[4]["valor"]="pagado";
    $matrizOrden[5]["descripcion"]=STATIC_ORDER_WORKSHOP_DATE_USER_TYPE_FIELD;
    $matrizOrden[5]["valor"]="tipo_usuario";


	
	
	//Gestion del campo de orden y filtro de numero de registros
	$campoOrdenDefecto="";
	require "includes/load_filter_list.inc.php";
	
	//Por defecto ponemos las inscripciones aceptadas
	$idEstadoInscripcion=2;
	$nombre="";
    $idTipoUsuario=0;

    $filtroPaginador="";

    //registration status filter
    if(isset($_GET["cmbEstadoInscripcion"])) {
        $idEstadoInscripcion = $_GET["cmbEstadoInscripcion"];
        $filtroPaginador = "&cmbEstadoInscripcion=" . $idEstadoInscripcion;
    }

    //name filter
    if($nombre!=$_GET["txtNombre"]) {
        $nombre = $_GET["txtNombre"];
        $filtroPaginador .= "&txtNombre=" . $nombre;
        $subPlantilla->assign("VIEW_CONFERENCE_DATE_NAME_SEARCH_VALUE",$nombre);
    }

    //user type filter
    if(isset($_GET["cmbTipoUsuario"])){
        $idTipoUsuario=$_GET["cmbTipoUsuario"];
        $filtroPaginador.="&cmbTipoUsuario=".$idTipoUsuario;
    }

    if($campoOrden!="numero_inscripcion" && $campoOrden!="pagado"){
		$direccionOrden="ASC";
	}

	/**
	 * 
	 * El total de paginas que mostraremos por pantalla
	 * @var int
	 * 
	 */
	$totalPaginasMostrar=4;
	
	/**
	 * 
	 * Almacenamos la cadena que representa la llamada al store procedure
	 * @var string
	 * 
	 */

	
		
	$codeProcedure="CALL ".OBJECT_DB_ACRONYM."_sp_inscripcion_conferencia_listar(".$_GET["id_conferencia"].",".$idEstadoInscripcion.",'".$nombre."','".$idTipoUsuario."',".$_SESSION["user"]["language_id"].",'".$campoOrden."','".$direccionOrden."',";
	$urlActual="main_app.php?section=conference_registered&action=view&id_conferencia=".$_GET["id_conferencia"]."&hdnOrden=".$valorDefecto."&hdnRegistros=".$numeroRegistrosPagina."&".$filtroPaginador."&";


	//Paginador
	//Paginador
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$numeroRegistrosPagina=-1;
	}
	require "includes/load_paginator.inc.php";


	//Pagina actual
	$subPlantilla->assign("PAGINA_ACTUAL",$paginaActual);
	
	$esExcel=false;
	
	if(isset($_GET["excel"]) && $_GET["excel"]==1){
		$esExcel=true;
		$plantillaExcel=new XTemplate("html/excel/index.html");
		switch($_GET["hdnTipoExcel"]){
			case 1:
				$subPlantillaExcel=new XTemplate("html/excel/view_conference_registered.html");
				break;
			case 2:
				$subPlantillaExcel=new XTemplate("html/excel/view_conference_registered_certification.html");
				break;
		}//end switch
		
	}//end if
	
	$resultado=$db->callProcedure($codeProcedure);
	$i=0;
	$mostrarHeadersRealizado=false;
	while($dato=$db->getData($resultado)){
		if($i%2==0){
			$subPlantilla->assign("TR_STYLE","class='dark'");
		}else{
			$subPlantilla->assign("TR_STYLE","class='light'");
		}

		if($esConferencia==1){
			//$vectorTaller["ID"]=$dato["id_inscripcion_conferencia_linea"];
			$subPlantilla->assign("ID_DESTINO","2");
			$subPlantilla->assign("ID_DESTINO_PAGADO","5");
		}else{
			//$vectorTaller["ID"]=$dato["id_inscripcion_taller_linea"];
			$subPlantilla->assign("ID_DESTINO","1");
			$subPlantilla->assign("ID_DESTINO_PAGADO","4");
		}
		$vectorTaller["INSCRIPCION_NUMBER"]=$dato["numero_inscripcion"];
		$vectorTaller["NAME"]=$dato["nombre_completo"];
        $vectorTaller["FIRSTNAME"]=$dato["nombre_inscrito"];
        $vectorTaller["LASTNAME"]=$dato["apellidos_inscrito"];
		$nombreCertificado=$dato["nombre_inscrito"]." ".$dato["apellidos_inscrito"];
		
		if($dato["tratamiento"]=="Prof." || $dato["tratamiento"]=="Dr."){
			$nombreCertificado=$dato["tratamiento"]." ".$nombreCertificado;
		}//end if
		
		$vectorTaller["NAME_CERTIFICATE"]=$nombreCertificado;
		$vectorTaller["COUNTRY"]=$dato["pais"];
		$vectorTaller["EMAIL"]=$dato["correo_electronico"];

		
		if($dato["pagado"]==1){
			$vectorTaller["PAID"]=STATIC_GLOBAL_BUTTON_YES;
		}else if($dato["pagado"]==0){
			$vectorTaller["PAID"]=STATIC_GLOBAL_BUTTON_NO;
		}
		$subPlantilla->assign("ID_DESTINO","3");
		$subPlantilla->assign("ID_DESTINO_PAGADO","6");
		
		$vectorTaller["ID"]=$dato["id_inscripcion_conferencia"];
        $vectorTaller["AMOUNT"]=$dato["importe_total"];

        if($dato["id_usuario_web"]!=""){
            $tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
            $vectorTaller["MEMBER"]="M";
        }else{
            if($dato["id_asociacion_hermana"]==""){
                $tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
                $vectorTaller["MEMBER"]="-";
            }else{
                $tipoUsuario=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
                $vectorTaller["MEMBER"]="S";
            }
        }

		if($dato["asistido"]==1){
			$vectorTaller["ASSISTED"]=STATIC_GLOBAL_BUTTON_YES;
		}else if($dato["asistido"]==0){
			$vectorTaller["ASSISTED"]=STATIC_GLOBAL_BUTTON_NO;
			
		}
		
		
		if($dato["observaciones"]!=""){
			$vectorTaller["COMMENT"]="<a href='main_app.php?section=conference_registered&action=edit&id_inscripcion=".$dato["id_inscripcion_conferencia"]."&id_conferencia=".$_GET["id_conferencia"]."' title='".htmlspecialchars($dato["observaciones"])."'><img src='images/comment.png' /></a>";
		}else{
			$vectorTaller["COMMENT"]="";
		}//end else
		
		$vectorTaller["PAID_NUMERIC"]=$dato["pagado"];
		$vectorTaller["ASSISTED_NUMERIC"]=$dato["asistido"];
		
		if($esExcel){
			$esCertificado=false;
			$vectorTaller["COMMENT_EXCEL"]=$dato["observaciones"];

			$vectorTaller["CONTACT_PHONE"]=$dato["telefono"];
			$vectorTaller["BADGE"]=$dato["conference_badge"];
			$vectorTaller["TITLE"]=$dato["tratamiento"];
			$vectorTaller["SISTER_ASSOCIATION"]=$dato["asociacion_hermana"];
			if($dato["speaker"]==1){
				$vectorTaller["SPEAKER"]=STATIC_GLOBAL_BUTTON_YES;
			}else if($dato["speaker"]==0){
				$vectorTaller["SPEAKER"]=STATIC_GLOBAL_BUTTON_NO;
			}
					
			if($dato["es_dinner"]==1){
				$vectorTaller["DINNERS"]=STATIC_GLOBAL_BUTTON_NO;
			}else if($dato["es_dinner"]==0){
				$vectorTaller["DINNERS"]=STATIC_GLOBAL_BUTTON_YES;
			}
			
			if($dato["email_permiso"]==1){
				$vectorTaller["EMAIL_PERMISSION"]=STATIC_GLOBAL_BUTTON_NO;
			}else if($dato["email_permiso"]==0){
				$vectorTaller["EMAIL_PERMISSION"]=STATIC_GLOBAL_BUTTON_YES;
			}
			
			if($dato["es_certificado"]==1){
				$esCertificado=true;
				$vectorTaller["CERTIFICATE"]=STATIC_GLOBAL_BUTTON_YES;
			}else if($dato["es_certificado"]==0){
				$vectorTaller["CERTIFICATE"]=STATIC_GLOBAL_BUTTON_NO;
			}

			$vectorTaller["INVOICE"]=STATIC_GLOBAL_BUTTON_YES;

			$vectorTaller["PAYMENT_METHOD"]=$dato["tipo_pago"];
			
			
			//Miramos los extras
			$resultadoConferenciaExtra=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_extra_obtener(".$dato["id_inscripcion_conferencia"].")");
			$totalConferenciaExtra=$db->getNumberRows($resultadoConferenciaExtra);
			while($datoConferenciaExtra=$db->getData($resultadoConferenciaExtra)){
				if(!$mostrarHeadersRealizado){
					$subPlantillaExcel->assign("CONFERENCIA_EXTRA_HEADER",$datoConferenciaExtra["descripcion_excel"]);
					$subPlantillaExcel->parse("contenido_principal.bloque_header");
				}//end if
				
				if($datoConferenciaExtra["es_boolean"]==1){
					if($datoConferenciaExtra["valor"]==1){
						$subPlantillaExcel->assign("CONFERENCIA_EXTRA_VALUE",STATIC_GLOBAL_BUTTON_YES);
					}else{
						$subPlantillaExcel->assign("CONFERENCIA_EXTRA_VALUE",STATIC_GLOBAL_BUTTON_NO);
					}//end else
				}else{
					$subPlantillaExcel->assign("CONFERENCIA_EXTRA_VALUE",$datoConferenciaExtra["valor"]);
				}//end else
				
				
				$subPlantillaExcel->parse("contenido_principal.item_conferencia.bloque_extra");
			}//end while
			
			$mostrarHeadersRealizado=true;
			
			$subPlantillaExcel->assign("CONFERENCE_REGISTERED",$vectorTaller);
			
			
			//talleres...
			if($_GET["hdnTipoExcel"]==2){
				$resultadoTaller=$db->callProcedure("CALL ed_sp_inscripcion_conferencia_linea_taller_obtener(".$dato["id_inscripcion_conferencia"].",".$_SESSION["user"]["language_id"].")");
				$i=0;
				$totalTaller=$db->getNumberRows($resultadoTaller);
				while($datoTaller=$db->getData($resultadoTaller)){
					$subPlantillaExcel->assign("TALLER_NOMBRE",$datoTaller["nombre_largo"]);
					
					if($i!=$totalTaller){
						$subPlantillaExcel->parse("contenido_principal.item_conferencia.item_conferencia_taller.item_conferencia_taller_separador");
					}//end if

					$subPlantillaExcel->parse("contenido_principal.item_conferencia.item_conferencia_taller");

				}//end while
				$subPlantillaExcel->parse("contenido_principal.item_conferencia");

			}else{
				$subPlantillaExcel->parse("contenido_principal.item_conferencia");
			}//end if
			
			
			
			
		}else{
			$subPlantilla->assign("CONFERENCE_REGISTERED",$vectorTaller);
			if($dato["activo"]==1){
				$subPlantilla->assign("ID_CONFERENCIA",$_GET["id_conferencia"]);
				$subPlantilla->parse("contenido_principal.item_conferencia.ir_detalle");
			}//end if
			

	
			
			$subPlantilla->parse("contenido_principal.item_conferencia");
		}//end else
		
		
		
		$i++;
	}//end while

	$i=0;

	
	
	$matriz[1]["descripcion"]=STATIC_GLOBAL_BUTTON_YES;
	$matriz[0]["descripcion"]=STATIC_GLOBAL_BUTTON_NO;
	
	
	$resultadoConferencia=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_conferencia_obtener_concreta(".$_GET["id_conferencia"].",".$_SESSION["user"]["language_id"].")");
	$datoConferencia=$db->getData($resultadoConferencia);

    //Id conferencia
    $subPlantilla->assign("ID_CONFERENCIA",$_GET["id_conferencia"]);

    //Combo estado inscripción
    $subPlantilla->assign("COMBO_ESTADO_INSCRIPCION",generalUtils::construirCombo($db,"CALL ".OBJECT_DB_ACRONYM."_sp_estado_inscripcion_conferencia_buscador_obtener_combo()","cmbEstadoInscripcion","cmbEstadoInscripcion",$idEstadoInscripcion,"descripcion","id_estado_inscripcion","",0,""));

    //Combo tipo usuario (member, non-member, sister association member)
    $matriz[3]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_NON_MEMBER;
    $matriz[2]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_SISTER_ASSOCIATION;
    $matriz[1]["descripcion"]=STATIC_VIEW_WORKSHOP_DATE_USER_TYPE_MEMBER;
    $matriz[0]["descripcion"]=STATIC_GLOBAL_COMBO_DEFAULT;

    $subPlantilla->assign("COMBO_USER_TYPE",generalUtils::construirComboMatriz($matriz,"cmbTipoUsuario","cmbTipoUsuario",$idTipoUsuario,"",-1,""));


//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_CONFERENCE_VIEW_CONFERENCE_TEXT;
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_CONFERENCE_EDIT_CONFERENCE_LINK."&id_conferencia=".$_GET["id_conferencia"];
	$vectorMigas[2]["texto"]=$datoConferencia["nombre"];
	$vectorMigas[3]["url"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_LINK."&id_conferencia=".$_GET["id_conferencia"];
	$vectorMigas[3]["texto"]=STATIC_BREADCUMB_CONFERENCE_REGISTERED_VIEW_CONFERENCE_REGISTERED_TEXT;


require "includes/load_breadcumb.inc.php";
	
	//Informacion del usuario
	require "includes/load_information_user.inc.php";
	
	if(!$esExcel){
		if($datoConferencia["activo"]==1){
			$subPlantilla->parse("contenido_principal.boton_edit_inscripcion");
			if($idEstadoInscripcion==2){
				$subPlantilla->parse("contenido_principal.boton_cancel_inscripcion");
			}else{
				$subPlantilla->parse("contenido_principal.boton_reactivate_inscripcion");
			}
		}//end if
	
		//Contruimos plantilla secundaria
		$subPlantilla->parse("contenido_principal");
		
		//Exportamos plantilla secundaria a la plantilla principal
		$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
		
		//Construimos plantilla principal
		$plantilla->parse("contenido_principal");
		
		//Mostramos plantilla principal por pantalla
		$plantilla->out("contenido_principal");
	}else{
		$fichero=$datoConferencia["nombre"]."_inscriptions.xls";
		if($_GET["hdnTipoExcel"]==2){
			$fichero=$datoConferencia["nombre"]."_certificates.xls";
		}//end if
		header("Content-type: application/vnd.ms-excel");
		//header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=$fichero");
		header("Content-Transfer-Encoding: binary");
		
		$subPlantillaExcel->assign("CONFERENCE_ACTUAL",$datoConferencia["nombre"]);
		
		//Mostramos excel
		$subPlantillaExcel->parse("contenido_principal");
		$plantillaExcel->assign("CONTENIDO",$subPlantillaExcel->text("contenido_principal"));
		$plantillaExcel->parse("contenido_principal");
		$plantillaExcel->out("contenido_principal");
	}
?>