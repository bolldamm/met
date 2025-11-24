<?php
	require "../includes/load_main_components.inc.php";
	$esAjax=true;
	require "../includes/load_validate_user.inc.php";
	require "../config/dictionary/".$_SESSION["user"]["language_dictio"];
	
	//Buscador
	$nombre="";
	$email="";

	if($_POST["txtNombre"]!=""){
		$nombre=$_POST["txtNombre"];
	}//end if
	if($_POST["txtEmail"]!=""){
		$email=$_POST["txtEmail"];
	}//end if
	
	$plantilla=new XTemplate("../html/ajax/search_invoice_member.html");
	

	$resultadoUsuario=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_usuario_web_factura_web_listar('".generalUtils::escaparCadena($nombre)."','".generalUtils::escaparCadena($email)."')");
	$totalUsuario=$db->getNumberRows($resultadoUsuario);
	$i=1;


	while($datoUsuario=$db->getData($resultadoUsuario)){
		if($datoUsuario["institucion"]==""){
			$nombreCompleto=$datoUsuario["nombre"]." ".$datoUsuario["apellidos"];
		}else{
			$nombreCompleto=$datoUsuario["institucion"];
		}
		
		if($datoUsuario["nombre_cliente_factura"]!=""){
			$vectorMiembro["NAME"]=$datoUsuario["nombre_cliente_factura"];
		}else{
			$vectorMiembro["NAME"]=$nombreCompleto;
		}
		
		$vectorMiembro["CONTADOR"]=$i;
		$vectorMiembro["EMAIL"]=$datoUsuario["correo_electronico"];
		$vectorMiembro["NIF"]=$datoUsuario["nif_cliente_factura"];
		$vectorMiembro["COMPANY"]=$datoUsuario["nombre_empresa_factura"];
		
		
		if($datoUsuario["direccion_factura"]==""){
			if($datoUsuario["direccion_individual"]!=""){
				$datoUsuario["direccion_factura"]=$datoUsuario["direccion_individual"];
			}else{
				$datoUsuario["direccion_factura"]=$datoUsuario["direccion_institucion"];
			}
		}//end if
		
		if($datoUsuario["codigo_postal_factura"]==""){
			if($datoUsuario["codigo_postal_individual"]!=""){
				$datoUsuario["codigo_postal_factura"]=$datoUsuario["codigo_postal_individual"];
			}else{
				$datoUsuario["codigo_postal_factura"]=$datoUsuario["codigo_postal_institucion"];
			}
		}//end if
		
		if($datoUsuario["ciudad_factura"]==""){
			if($datoUsuario["ciudad_individual"]!=""){
				$datoUsuario["ciudad_factura"]=$datoUsuario["ciudad_individual"];
			}else{
				$datoUsuario["ciudad_factura"]=$datoUsuario["ciudad_institucion"];
			}
		}//end if
		
		if($datoUsuario["ciudad_factura"]==""){
			if($datoUsuario["ciudad_individual"]!=""){
				$datoUsuario["ciudad_factura"]=$datoUsuario["ciudad_individual"];
			}else{
				$datoUsuario["ciudad_factura"]=$datoUsuario["ciudad_institucion"];
			}
		}//end if
		
		if($datoUsuario["provincia_factura"]==""){
			if($datoUsuario["provincia_individual"]!=""){
				$datoUsuario["provincia_factura"]=$datoUsuario["provincia_individual"];
			}else{
				$datoUsuario["provincia_factura"]=$datoUsuario["provincia_institucion"];
			}
		}//end if
		
		if($datoUsuario["pais_factura"]==""){
			if($datoUsuario["pais_individual"]!=""){
				$datoUsuario["pais_factura"]=$datoUsuario["pais_individual"];
			}else{
				$datoUsuario["pais_factura"]=$datoUsuario["pais_institucion"];
			}
		}//end if
		
		
		$vectorMiembro["ADDRESS"]=$datoUsuario["direccion_factura"];
		$vectorMiembro["ZIPCODE"]=$datoUsuario["codigo_postal_factura"];
		$vectorMiembro["CITY"]=$datoUsuario["ciudad_factura"];
		$vectorMiembro["PROVINCE"]=$datoUsuario["provincia_factura"];
		$vectorMiembro["COUNTRY"]=$datoUsuario["pais_factura"];

		
		$plantilla->assign("MEMBER",$vectorMiembro);
		$plantilla->parse("contenido_principal.item_miembro");
		
		
		$i++;
	}
	$plantilla->parse("contenido_principal");
	
	$plantilla->out("contenido_principal");
?>