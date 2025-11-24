<?php
	/**
	 * 
	 * Script que muestra y realiza un evio
	 * @Author eData
	 * 
	 */
	
	//Si hemos enviado el formulario...
	if(count($_POST)>0){
		$destinatarios=Array();
		//Introducimos todos los destinatarios existentes en el para
		$destinatarios=array_merge($destinatarios,array_filter(explode(";",$_POST["txtPara"])));
		$destinatarios=array_unique($destinatarios);
		$totalDestinatarios=count($destinatarios);
		

		require "../includes/load_mailer.inc.php";

		$plantilla=new XTemplate("../html/mail/mail_invoice.html");
		
		$_POST["txtaDescripcion"]=str_replace("/documentacion/","http://www.metmeetings.org/documentacion/",$_POST["txtaDescripcion"]);
		
		$plantilla->assign("CONTENIDO",$_POST["txtaDescripcion"]);
		
		$plantilla->parse("contenido_principal");
		
		$mail->From = STATIC_MAIL_FROM;
	  	$mail->FromName = "MET";
	  	$mail->Subject = $_POST["txtNombre"];
	  	$mail->Body = $plantilla->text("contenido_principal");

	  	$vectorDestinatario=Array();
	  	foreach($destinatarios as $valor){
			// Mail a donde enviar
	  		$mail->AddAddress($valor);


	  		if($mail->Send()){		
				//Destinatario
				
				array_push($vectorDestinatario,$valor);

				
	  		}
	  		//Limpiamos address
	  		$mail->ClearAllRecipients();
	  	}
	  	
	  	$idFactura=$_POST["hdnIdElemento"];

		/****** Guardamos el log del correo electronico ******/
		$idUsuarioWebCorreo="null";
	
		//Tipo correo electronico
		$idTipoCorreoElectronico=EMAIL_TYPE_INVOICE_SENT;
		
		
		//Asunto
		$asunto=$mail->Subject;
		$cuerpo=$mail->Body;

        //Update invoice
        $db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_actualizar_enviado(".$idFactura.")");
		
		require "../includes/load_log_email.inc.php";	
	  	
		generalUtils::redirigir("main_app.php?section=invoice&action=view");
	}

	if($_GET["send"]) {
      define("AUTOSEND_JAVASCRIPT","<script type='text/javascript'>document.getElementById('send-button').click();</script>");
	} else {
      define("AUTOSEND_JAVASCRIPT","");
    }

	//Plantilla principal
	$plantilla=new XTemplate("html/principal.html");
		
	//Plantilla secundaria
	$subPlantilla=new XTemplate("html/sections/invoice/send_invoice.html");
	


	//Editor descripcion completa
	$plantilla->assign("TEXTAREA_ID","txtaDescripcion");
	$plantilla->assign("TEXTAREA_TOOLBAR","Newsletter");
	$plantilla->parse("contenido_principal.carga_inicial.inicializar_ckeditor");
	
	
	$plantilla->parse("contenido_principal.carga_inicial.editor_finder");

	//Migas de pan
	$vectorMigas[0]["url"]=STATIC_BREADCUMB_INICIO_LINK;
	$vectorMigas[0]["texto"]=STATIC_BREADCUMB_INICIO_TEXT;
	$vectorMigas[1]["url"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_LINK;
	$vectorMigas[1]["texto"]=STATIC_BREADCUMB_INVOICE_VIEW_INVOICE_TEXT;	
	$vectorMigas[2]["url"]=STATIC_BREADCUMB_INVOICE_SEND_INVOICE_LINK."&id_factura=".$_GET["id_factura"];
	$vectorMigas[2]["texto"]=STATIC_BREADCUMB_INVOICE_SEND_INVOICE_TEXT;	

	

	require "includes/load_breadcumb.inc.php";
	
	
	//Parametros hidden formulario
	$subPlantilla->assign("ID_ELEMENTO",$_GET["id_factura"]);
	$subPlantilla->assign("ACTION","send");
	
	
	//Obtener factura concreta
	$resultadoFactura=$db->callProcedure("CALL ".OBJECT_DB_ACRONYM."_sp_factura_usuario_web_asociado_obtener(".$_GET["id_factura"].",".$_SESSION["user"]["language_id"].")");
	$datoFactura=$db->getData($resultadoFactura);

	if($datoFactura["hash_generado"]==""){
		exit;
	}else{
		$enlace=CURRENT_DOMAIN."get_invoice.php?hash=".$datoFactura["hash_generado"];
	}

        if($datoFactura["first_name"]!=""){
        $emailName=$datoFactura["first_name"];
        } else {
        $emailName=$datoFactura["nombre_cliente_factura"];
        }
     
        if($datoFactura["email_cliente_factura"]!=""){
        $emailAddress=$datoFactura["email_cliente_factura"];
        } else {
        $emailAddress="";
        }

//        $emailName="";
//        $emailAddress="";
	//If the user associated with the movement is an individual member
//	if($datoFactura["id_modalidad_usuario_web"]==1){
//		$emailName=$datoFactura["nombre"];
//		$emailAddress=$datoFactura["correo_electronico"];
	//If the user associated with the movement is an institutional member
//      }else if($datoFactura["id_modalidad_usuario_web"]==2){
//		$emailName=$datoFactura["nombre_representante"];
//		$emailAddress=$datoFactura["email_cliente_factura"];		
//	}else{
	//If the user associated with the movement is a non-member
//		$emailName=$datoFactura["nombre_cliente_factura"];
//		$emailAddress=$datoFactura["email_cliente_factura"];
//	}

	
	$subPlantilla->assign("INVOICE_PARA",$emailAddress);
	$subPlantilla->assign("INVOICE_DESCRIPCION",STATIC_VIEW_MOVEMENT_SEND_INVOICE_BODY_1." ".$emailName.", <br><br>".STATIC_VIEW_MOVEMENT_SEND_INVOICE_BODY_2." <br><br>".STATIC_VIEW_MOVEMENT_SEND_INVOICE_DOWNLOAD_INVOICE_1." <a href='".$enlace."'>".STATIC_VIEW_MOVEMENT_SEND_INVOICE_DOWNLOAD_INVOICE_2."</a>".STATIC_VIEW_MOVEMENT_SEND_INVOICE_DOWNLOAD_INVOICE_3.".<br><br>".STATIC_VIEW_MOVEMENT_SEND_INVOICE_BODY_3);


	//Informacion del usuario
	require "includes/load_information_user.inc.php";
			
	
	//Incluimos script editor
	$plantilla->parse("contenido_principal.editor_script");
	
	//Incluimos proceso onload
	$plantilla->parse("contenido_principal.carga_inicial");
	
	//Asuntos
	$subPlantilla->assign("INVOICE_ASUNTO",STATIC_VIEW_MOVEMENT_SEND_INVOICE_SUBJECT);
	
	
	//Contruimos plantilla secundaria
	$subPlantilla->parse("contenido_principal");
	
	//Exportamos plantilla secundaria a la plantilla principal
	$plantilla->assign("CONTENIDO",$subPlantilla->text("contenido_principal"));
	
	//Construimos plantilla principal
	$plantilla->parse("contenido_principal");
	
	//Mostramos plantilla principal por pantalla
	$plantilla->out("contenido_principal");
?>