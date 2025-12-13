

/** Funciones generales **/
/* carrousel_resume_initCallback */
function carrousel_resume_initCallback(carousel) {
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind('click', function() {
        carousel.startAuto(0);
    });

    carousel.buttonPrev.bind('click', function() {
        carousel.startAuto(0);
    });

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
        carousel.stopAuto();
    }, function() {
        carousel.startAuto();
    });
}

/* Creamos el objeto Ajax */
function nuevoAjax() {
	var xmlhttp=false;
	try {
		// CreaciÃ³n del objeto ajax para navegadores diferentes a Explorer
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");  
	} catch (e) {
		try {
			// o bien
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			// CreaciÃ³n del objeto ajax para Explorer
		} catch (E) {
			xmlhttp = false;
		}
	}
	if(!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

/* validar formato del correo electrónico */
function validarEmail(email){
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	return reg.test(email);
}

function ejecutarIntroLogin(evento) {
	if(evento.keyCode == 13) {
		document.frmLogin.btnLogin.click();
	}
}

/* recuperamos el valor seleccionado de un conjunto de radiobuttons */
function radioButtonValorSeleccionado(elemento) {
	var totalElementos = elemento.length;
	var i = 0;
	
	while(i < totalElementos){
		if(elemento[i].checked) {
			return elemento[i].value;
		}
		i++;
	}
	
	return -1;
}

/* Dado un elemento, si este esta activo activara el campo de texto */
function abrirTexto(elemento, campoTexto) {
	if(elemento.checked) {
		document.getElementById(campoTexto).style.display="block";
	}else{
		document.getElementById(campoTexto).style.display="none";
	}
}

/* validamos si el valor es numerico */
function IsNumeric(elemento)
{
	return (String(elemento).search(/^\d+$/) != -1);
}

/* destacamos el campo erroneo */
function destacarError(elemento) {
	var contenidoClases = elemento.getAttribute("class");
	elemento.setAttribute("class", contenidoClases + " errorInput");
}

/* reestablecemos el campo erroneo */
function limpiarErrores() {
	$(".errorInput").removeClass("errorInput");
}

/* comprueba si la cadena es vacia */
function trim(cadena){
	return cadena.replace(/^\s+|\s+$/gi, "");
}

/* placeholder vaciar input text */
function limpiarInputValue(elemento,fraseDefecto){
	if(elemento.value==fraseDefecto){
		elemento.value="";
	}
}

/* placeholder restaurar input text */
function restaurarInputValue(elemento,fraseDefecto){
	if(elemento.value==""){
		elemento.value=fraseDefecto;
	}
}

/* placeholder vaciar textarea */
function limpiarTextAreaValue(elemento,fraseDefecto){
	if(elemento.value==fraseDefecto){
		elemento.value = "";
	}
}

/* placeholder restaurar textarea */
function restaurarTextAreaValue(elemento,fraseDefecto){
	if(elemento.value==""){
		elemento.value = fraseDefecto;
	}
}

/* placeholder vaciar textarea */
function limpiarTextAreaEditorValue(editor,fraseDefecto){
	var instancia=CKEDITOR.instances[editor];
	if(instancia.getData()==fraseDefecto){
		instancia.document.getBody().setHtml("");
	}
}

/* placeholder restaurar textarea */
function restaurarTextAreaEditorValue(editor,fraseDefecto){
	var instancia=CKEDITOR.instances[editor];
	if(instancia.getData().replace("<br />","").trim()==""){
		instancia.document.getBody().setHtml(fraseDefecto);
	}
}

function limpiarInputPassword(elemento,destino){
	elemento.style.display="none";
	document.getElementById(destino).style.display="";
	document.getElementById(destino).focus();
}
function restaurarInputPassword(elemento,destino,fraseDefecto){
	if(elemento.value==""){
		elemento.style.display="none";
		var passAuxiliar=document.getElementById(destino);
		passAuxiliar.style.display="";
		passAuxiliar.value=fraseDefecto;
	}
}

/* Recargar de nuevo la imagen del captcha */
function refrescarCaptcha(){
	document.getElementById("imageCaptcha").src = "classes/secureimage/securimage_show.php?sid=" + Math.random();
	document.getElementById("txtCaptcha").value="";
}

/* Movemos la situación de la web arriba */
function moverWeb(elemento) {
	scroll(0,0);
}

/* centra el mapa de googleMaps segun la dirección que reciba por parametro */
function centrarMapa(address, zoom, map, geocoder){
	map.setZoom(zoom);
    if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
        	map.setCenter(results[0].geometry.location);
        } else {
        	map.setCenter(new google.maps.LatLng(0,0));
        }
      });
	}
}

/* Añade un mensaje a la posicion seleccionadaen en googleMaps */
function anyadirMarcador(address, titulo, centrar, zoom, imagenMarcador, map, geocoder){
    if (geocoder) {
    	if(zoom > 0) {
    		map.setZoom(zoom);
    	}
	    geocoder.geocode( {'address': address}, function(results, status) {
	    	if (status == google.maps.GeocoderStatus.OK) {
		        if(centrar == true) {
		        	map.setCenter(results[0].geometry.location);
		        }
		        if(trim(titulo) != "") {
					var marker = new google.maps.Marker({
						icon: imagenMarcador,
						map: map, 
						position: results[0].geometry.location
					});
					var infowindow = new google.maps.InfoWindow({ content: titulo });
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.open(map, marker);
					});
		        }
			}
		});
	}
}

function eliminarElementoGuardar(invocador,elementoOculto,elementoMostrar,valor){
	var input=document.getElementById(elementoOculto);
	input.value=valor;
	invocador.style.display="none";
	document.getElementById(elementoMostrar).style.display="";	
}


/* Capa de cargando */
function mensajeCargando(mensaje){
    $.blockUI({  message:mensaje,css: { 
        border: 'none', 
        padding: '15px', 
        fontFamily:'Arial',
        fontSize:'14px',
        fontWeight:"bold",
        border:'10px solid #348a88',
        backgroundColor: '#000', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: .5, 
        color: '#fff' 
    }});
}

/* anclar pantalla */
function anclarElemento(elemento){
	location.hash="#"+elemento;
}


function centrarPantallaElemento(elemento){
	$("html, body").animate({
		scrollTop: $(elemento).offset().top
	}, 1000);
}


function generarInscripcionWorkshop(){
	var formulario=$("#frmWorkshopRegister");
	var resultadoProceso;
	$.ajax({
        type: "POST",
        url: "ajax/save_inscription_workshop.php",
        data: formulario.serialize(),
        success: function(data){
        	//Si es correcto...
        	document.getElementById("resultadoInscripcion").innerHTML=data;
        	
        	//Si encontramos la variable
        	resultadoProceso=document.getElementById("hdnTipoResultado");
        	if(resultadoProceso.value==1){
        		window.location="inscripcion_finalizada.php?modo=2&tipo=1";
        	}else if(resultadoProceso.value==2){
        		document.frmPaypal.submit();
        	}else if(resultadoProceso.value==4){
            	window.location="inscripcion_finalizada.php?tipo=4";
        	}else{
        		var msgContenido = document.getElementById("msgAlert");
				msgContenido.innerHTML = resultadoProceso.value;
				msgContenido.setAttribute("class", "msgKO");
				msgContenido.style.display = "block";
				moverWeb();
				$.unblockUI();
        	}
      	}
	});
}

function generarInscripcionConferenc(){
	var formulario=$("#frmConferenceRegister");
	var resultadoProceso;
	

	$.ajax({
        type: "POST",
        url: "ajax/save_inscription_conference.php",
       data: formulario.serialize(),
		//data: formData,
		contentType: false,
		processData: false,
        success: function(data){
        	//Si es correcto...
        	document.getElementById("resultadoInscripcion").innerHTML=data;
        	
        	//Si encontramos la variable
        	resultadoProceso=document.getElementById("hdnTipoResultado");
        	if(resultadoProceso.value==1){
        		window.location="inscripcion_finalizada.php?modo=1&tipo=1";
        	}else if(resultadoProceso.value==2){
        		document.frmPaypal.submit();
        	}else if(resultadoProceso.value==4){
            	window.location="inscripcion_finalizada.php?tipo=4";
        	}else{
        		var msgContenido = document.getElementById("msgAlert");
				msgContenido.innerHTML = resultadoProceso.value;
				msgContenido.setAttribute("class", "msgKO");
				msgContenido.style.display = "block";
				moverWeb();
				$.unblockUI();
        	}
      	}
	});
}





function generarInscripcion(){
	var formulario=$("#frmMembership");
	var resultadoProceso;
	$.ajax({
        type: "POST",
        url: "ajax/save_inscription.php",
        data: formulario.serialize(),
        success: function(data){
        	//Si es correcto...
        	document.getElementById("resultadoInscripcion").innerHTML=data;
        	
        	//Si encontramos la variable
        	resultadoProceso=document.getElementById("hdnTipoResultado");
        	if(resultadoProceso.value==1){
        		window.location="inscripcion_finalizada.php?tipo=1";
        	}else if(resultadoProceso.value==2){
        		document.frmPaypal.submit();
        	}else if(resultadoProceso.value==4){
            	window.location="inscripcion_finalizada.php?tipo=4";
        	}else{
        		var msgContenido = document.getElementById("msgAlert");
				msgContenido.innerHTML = resultadoProceso.value;
				msgContenido.setAttribute("class", "msgKO");
				msgContenido.style.display = "block";
				moverWeb();
				$.unblockUI();
        	}
      	}
	});
}


function generarRenovacion(){
	var formulario=$("#frmMembership");
	var resultadoProceso;
	$.ajax({
        type: "POST",
        url: "ajax/save_renew_inscription.php",
        data: formulario.serialize(),
        success: function(data){
        	//Si es correcto...
        	document.getElementById("resultadoInscripcion").innerHTML=data;
        	
        	//Si encontramos la variable
        	resultadoProceso=document.getElementById("hdnTipoResultado");
        	if(resultadoProceso.value==1){
        		window.location="inscripcion_finalizada.php?tipo=1";
        	}else if(resultadoProceso.value==2){
        		document.frmPaypal.submit();
        	}else if(resultadoProceso.value==4){
            	window.location="inscripcion_finalizada.php?tipo=4";
        	}else{
        		var msgContenido = document.getElementById("msgAlert");
				msgContenido.innerHTML = resultadoProceso.value;
				msgContenido.setAttribute("class", "msgKO");
				msgContenido.style.display = "block";
				moverWeb();
				$.unblockUI();
        	}
      	}
	});
}



function calcularTotalTaller(invocador){
	var precioActual=parseFloat(document.getElementById("totalPayable").innerHTML);
	var idNumerico=invocador.name.split("_")[1];
	var asociacionHermana=document.frmWorkshopRegister.cmbAsociacionHermana;
	
	var idLogin=document.getElementById("hdnIdLogin");
	if(idLogin){
		precioTallerConcreto=parseFloat(document.getElementById("precioTaller_"+idNumerico).innerHTML);
	}else{
		if(asociacionHermana.value==-1){
			precioTallerConcreto=parseFloat(document.getElementById("precioTallerNoMiembro_"+idNumerico).innerHTML);
		}else{
			precioTallerConcreto=parseFloat(document.getElementById("precioTallerSister_"+idNumerico).innerHTML);
		}
	}

	if(invocador.checked){
		precioActual+=precioTallerConcreto;
	}else{
		precioActual-=precioTallerConcreto;
	}
	document.getElementById("totalPayable").innerHTML=precioActual;
}

function refrescarTotalTaller(invocador){
	var precioActual=0;
	var asociacionHermana=document.frmWorkshopRegister.cmbAsociacionHermana;
	var precioRelevante;
	var idNumerico;
	if(asociacionHermana.value==-1){
		precioRelevante="precioTallerNoMiembro_";
	}else{
		precioRelevante="precioTallerSister_";
	}
	
	$("#containerWorkshop input:checked").each(function(){
		idNumerico=$(this).attr("name").split("_")[1];
		precioActual+=parseFloat(document.getElementById(precioRelevante+idNumerico).innerHTML);
	});
	
	document.getElementById("totalPayable").innerHTML=precioActual;
	
}

function gestionarBloqueInvoice(invocador){
	if(invocador.checked){
		$(".bloqueInvoice").css("display","");
	}else{
		$(".bloqueInvoice").css("display","none");
	}
}

function mostrarMensajeError(mensaje){
	document.getElementById("messageError").innerHTML = mensaje;
	document.getElementById("messageError").style.display = "block";
}


function deseleccionarRadio(invocador){
	var seleccionadoActual=document.getElementById("hdnFecha_"+invocador.name.split("_")[1]);
	var seleccionadoActualAux=seleccionadoActual.value;
	var seleccionadoActualValor=seleccionadoActual.value;
	var totalCheckedActual=0;
	var fechaConcreta;
	var idActual;
	var idActualRef;
	var precio;
	var miniSesion=$("#containerWorkshop input.miniSession:checked");
	$("#containerWorkshop input.hiddenDate").each(function(index,valor){
		if(valor.value!=""){
			totalCheckedActual++;
		}
	});
	
	
	var totalCheckedPosterior=0;
	
	//Sobreescribimos
	seleccionadoActual.value=invocador.value;
	
	if(seleccionadoActualValor!=""){
		if(seleccionadoActualValor==seleccionadoActual.value){
			invocador.checked=false;
			seleccionadoActual.value="";
		}
	}//end if
	
	
	
	$("#containerWorkshop input.hiddenDate").each(function(index,valor){
		//A partir del bloque de fecha actual queremos averiguar si es necesario deshabilitar las minis
		fechaConcreta=valor.id.split("_")[1];

		if(valor.value!=""){
			totalCheckedPosterior++;
			
			$("#container_mini_session_"+fechaConcreta+" input[type='checkbox']").attr("disabled","true");
			$("#container_mini_session_"+fechaConcreta+" input[type='checkbox']").removeAttr("checked");

		}else{
			//Al no escoger workshop, puedes escoger mini
			$("#container_mini_session_"+fechaConcreta+" input[type='checkbox']").removeAttr("disabled");
		}//end if

	});
	/*
	//Ponemos precio por defecto
	var bloquePrecio=document.getElementById("totalPayable");
	bloquePrecio.innerHTML=document.getElementById("hdnPrecioMinimo").value
	
	

	//Miro si teniamos radio escogido
	$(miniSesion).each(function(index,valor){
		idActual=valor.name.split("_");
		idActualRef=idActual[1]+"_"+idActual[2];
		if(document.getElementById("hdnIdLogin")){
			precio=document.getElementById("hdnPrecioMiniMember_"+idActualRef).value;
		}else{
			if(document.frmConferenceRegister.cmbAsociacionHermana.value=="-1"){
				precio=document.getElementById("hdnPrecioMiniNonMember_"+idActualRef).value;
			}else{
				precio=document.getElementById("hdnPrecioMiniSister_"+idActualRef).value;
			}//end else
		}//end else

		//if(seleccionadoActualAux=="" && idActual[1]!=invocador.name.split("_")[1]){
			if(totalCheckedPosterior>0){
				if($("#"+valor.id+":checked").length>0){
					modificarTotalConferencia(parseFloat(precio),true);
				}else{
					
					//modificarTotalConferencia(parseFloat(precio)*(-1));
				}//end else
			}else{
				//modificarTotalConferencia(parseFloat(precio)*(-1));
			}//end else
		//}//end if
	})//end if
	
	aplicarDescuentos();
	
	//Miramos si se incrementa el precio al haber escogido mas de un workshop
	if(totalCheckedActual==2 && totalCheckedPosterior<2){
		modificarTotalConferencia(-30,true);
		;
	}else if(totalCheckedPosterior==2){
		modificarTotalConferencia(30,true);
	}//end else
	
*/
	reinicializarPrecioTotal();
	
}

function tratarMiniSession(invocador){
	/*var tallerSeleccionado=0;
	var tallerMiniSeleccionado=0;
	var idActual;
	var idActualRef;
	var precio;
	
	//Workshops activados
	$("#containerWorkshop input.hiddenDate").each(function(index,valor){
		if(valor.value!=""){
			tallerSeleccionado++;
		}//end if
	})//end workshop each
	
	
	//Minis escogidos
	tallerMiniSeleccionado=($("#containerWorkshop input.miniSession:checked").length);
	
	//Por cada taller mini que seleccionemos aumentamos precio
	idActual=invocador.name.split("_");
	idActualRef=idActual[1]+"_"+idActual[2];
	
	if(document.getElementById("hdnIdLogin")){
		precio=document.getElementById("hdnPrecioMiniMember_"+idActualRef).value;
	}else{
		if(document.frmConferenceRegister.cmbAsociacionHermana.value=="-1"){
			precio=document.getElementById("hdnPrecioMiniNonMember_"+idActualRef).value;
		}else{
			precio=document.getElementById("hdnPrecioMiniSister_"+idActualRef).value;
		}//end else
	}//end else
	


	if(tallerSeleccionado==1){

		if(invocador.checked){
			modificarTotalConferencia(parseFloat(precio),true);
		}else{
			modificarTotalConferencia(parseFloat(precio)*(-1),true);
		}//end else
	}else if(tallerMiniSeleccionado==4){
		modificarTotalConferencia(parseInt(precio,10),true);
	}else if(tallerMiniSeleccionado==3){
		modificarTotalConferencia(parseInt(precio,10)*(-1),true);
	}//end else
	*/
	reinicializarPrecioTotal();
}//end function




function tratamientoInvitados(invocador){
	if(invocador.checked){
		document.getElementById("containerGuest").style.display="";
	}else{
		document.getElementById("containerGuest").style.display="none";
	}
}

function descuentoTotalConferencia(invocador,valorAgregado){
	if(invocador.checked){
		modificarTotalConferencia(valorAgregado*(-1),false);
	}else{
		modificarTotalConferencia(valorAgregado,false);
	}
}

function descuentoDesayunoConferencia(invocador,valorAgregado){
	if(invocador.checked){
		modificarTotalConferencia(valorAgregado*(-1),false);
	}else{
		modificarTotalConferencia(valorAgregado,false);
	}
}

function modificarTotalConferencia(valorAgregado,controlarLimite){
	var bloquePrecio=document.getElementById("totalPayable");
	var precioActual=bloquePrecio.innerHTML;
	var precioMinimo=parseFloat(document.getElementById("hdnPrecioMinimo").value);
	
	
	var descuentoSpeaker=document.getElementById("hdnPrecioSpeakerAsociacion");
	if(document.getElementById("chkHelper").checked){
		precioMinimo-=parseFloat(descuentoSpeaker.value);
	}//end if
	

	//Miramos si esta desayuno seleccionado y en ese caso generamos descuento
	var descuentoDesayuno=document.getElementById("hdnPrecioDesayunoAsociacion");
	if(document.getElementById("chkDinner").checked){
		precioMinimo-=parseFloat(descuentoDesayuno.value);
	}//end if
	
	var resultadoOperacion=parseInt(precioActual,10)+valorAgregado;


	if(precioMinimo>resultadoOperacion && controlarLimite){
		bloquePrecio.innerHTML=precioMinimo;
	}else{
		bloquePrecio.innerHTML=precioMinimo;
	}//end else
}


function obtenerComboSubConcepto(invocador){
	var contenedor=document.getElementById("comboConcepto");
	
	//No se selecciona nada
	if(invocador.value==0){
		$(".contenedorSubConcepto").css("display","none");
		contenedor.innerHTML="";
	}else{
		var ajax=nuevoAjax();
		ajax.open("GET","ajax/load_combo.php?c=1&id="+invocador.value);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==4){
				$(".contenedorSubConcepto").css("display","");
				contenedor.innerHTML=ajax.responseText;
			}
		}
		ajax.send(null);
	}
}

function aplicarDescuentos(){
	
	//Miramos si esta speaker seleccionado y en ese caso generamos descuento
	var descuentoSpeaker=document.getElementById("hdnPrecioSpeaker");
	if(document.getElementById("chkHelper").checked){
		modificarTotalConferencia(-parseFloat(descuentoSpeaker.value),false);
	}//end if
	

	//Miramos si esta desayuno seleccionado y en ese caso generamos descuento
	var descuentoDesayuno=document.getElementById("hdnPrecioDesayuno");
	if(document.getElementById("chkDinner").checked){
		modificarTotalConferencia(-parseFloat(descuentoDesayuno.value),false);
	}//end if

}


function reinicializarPrecioTotal(){
	var precioBase;
	var descuentoSpeaker;
	var descuentoDesayuno;
	var tallerSeleccionado=0;
		
	if(!document.getElementById("hdnIdLogin")){
		if(document.getElementById("cmbAsociacionHermana").value!="-1"){
			document.getElementById("hdnPrecioMinimo").value=document.getElementById("hdnPrecioMinimoAsociacion").value;
			descuentoSpeaker=document.getElementById("hdnPrecioSpeakerAsociacion");
			descuentoDesayuno=document.getElementById("hdnPrecioDesayunoAsociacion");
		}else{
			document.getElementById("hdnPrecioMinimo").value=document.getElementById("hdnPrecioMinimoAux").value;
			//descuentoSpeaker=document.getElementById("hdnPrecioSpeaker");
			//descuentoDesayuno=document.getElementById("hdnPrecioDesayuno");
			descuentoSpeaker=document.getElementById("hdnPrecioSpeakerAsociacion");
			descuentoDesayuno=document.getElementById("hdnPrecioDesayunoAsociacion");
		}//end else
	}else{
		descuentoSpeaker=document.getElementById("hdnPrecioSpeaker");
		descuentoDesayuno=document.getElementById("hdnPrecioDesayuno");
	}//end else
	
	precioBase=parseFloat(document.getElementById("hdnPrecioMinimo").value);

	
	if(document.getElementById("chkHelper").checked){
		precioBase-=parseFloat(descuentoSpeaker.value);
	}//end if
	

	//Miramos si esta desayuno seleccionado y en ese caso generamos descuento
	if(document.getElementById("chkDinner").checked){
		precioBase-=parseFloat(descuentoDesayuno.value);
	}//end if

	//Miramos si esta desayuno seleccionado y en ese caso generamos descuento
	if(document.getElementById("SwalWorkshop").checked){
		precioBase-=parseFloat(descuentoDesayuno.value);
	}//end if
	

	//Workshops activados
	$("#containerWorkshop input.hiddenDate").each(function(index,valor){
		if(valor.value!=""){
			tallerSeleccionado++;
		}//end if
	})//end workshop each
	
	var tallerMiniSeleccionado=($("#containerWorkshop input.miniSession:checked").length);

	if (tallerSeleccionado>=1){
		if(tallerMiniSeleccionado>0){
			precioBase+=(15*tallerMiniSeleccionado)
		}else if(tallerSeleccionado>1){
			precioBase+=((30*tallerSeleccionado)-30);
		}
	}else if(tallerMiniSeleccionado>2){
		precioBase+=((15*tallerMiniSeleccionado)-30);
		if(tallerSeleccionado>0){
			precioBase+=(30*tallerSeleccionado);
		}
	}

	if(document.getElementById("cmbInvitados").value>"0"){
		precioBase+=(30*document.getElementById("cmbInvitados").value);
	}

	document.getElementById("totalPayable").innerHTML=precioBase;
	
}//end if

function bloquearTalleres(invocador){
	if(invocador.checked){
		$(".selectorWorkshop").attr("disabled","true").removeAttr("checked");
		$("input.hiddenDate").val("");
		
		$(".miniSession").attr("disabled","true").removeAttr("checked");
	}else{
		$(".selectorWorkshop").removeAttr("disabled");
		$(".miniSession").removeAttr("disabled");
	}//end else
	
	reinicializarPrecioTotal();
}

function checkCode(){
	var pref = "**********";
	var corr = "coimbra2015";
	var code = document.getElementById("RegCode").value;
	var precioFinal = document.getElementById("totalPayable").innerHTML;
	var precioCodigo = parseInt(50);


	var btn = document.getElementById("RegCodeVer");
	var msgContenido = document.getElementById("msgAlert");
	var blError = true;
	var txtError = "";
	btn.disabled = true;
	limpiarErrores();


	if(code==pref) {
		blError = false;
		destacarError(document.frmConferenceRegister.RegCode);
		txtError = "Porfavor Introduce un Codigo Valido";
	}else if (code==corr){
		document.getElementById("hdnPrecioMinimo").value = precioCodigo;
		document.getElementById("hdnPrecioMinimoAux").value = precioCodigo;
		document.getElementById("totalPayable").innerHTML = document.getElementById("hdnPrecioMinimo").value;
		//document.getElementById('totalPayable').id = 'totalPayableCode';
		msgContenido.style.display = "none";
		document.getElementById('RegCodeVer').style.background='#29B23B';
	}else{
		blError = false;
		destacarError(document.frmConferenceRegister.RegCode);
		txtError = "El codigo Introducido es Incorrecto";
	}

	if(!blError){
		msgContenido.innerHTML = txtError;
		msgContenido.setAttribute("class", "msgKO");
		msgContenido.style.display = "block";
		moverWeb();
	}

	btn.disabled = false;
}