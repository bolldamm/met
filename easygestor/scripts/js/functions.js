var extensionesImagenPermitidas=new Array("jpg","jpeg","gif");
var extensionesMediaPermitidas=new Array("jpg","jpeg","gif","flv");
var extensionesNewsletterPermitidas=new Array("jpg","jpeg","gif","swf","pdf");
var geocoder="";
var map="";
function nuevoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");  		// Creaci�n del objeto ajax para navegadores diferentes a Explorer
	} catch (e) {
		try {								// o bien
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");		// Creaci�n del objeto ajax para Explorer
		} catch (E) {
			xmlhttp = false;
		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function validarEmail(email){
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	return reg.test(email);
}

function trim(cadena){
	return cadena.replace(/^\s+|\s+$/gi, "");
}
function isArray(elemento){
	return typeof elemento=="object";
}

function refrescarCaptcha(){
	document.getElementById("image").src = "../classes/securimage/securimage_show.php?sid=" + Math.random();
	document.forms[0].txtCode.value="";
}

function limpiarInputValue(elemento,fraseDefecto){
	if(elemento.value==fraseDefecto){
		elemento.value="";
	}
}

function restaurarInputValue(elemento,fraseDefecto){
	if(elemento.value==""){
		elemento.value=fraseDefecto;
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

function tratarCombosAsociados(elemento){
	if(elemento.value==1){
		$(".itemFormulario").css("display","block");
	}else{
		$(".itemFormulario").css("display","none");
		document.frmMenu.cmbFormulario.value="0";
	}
	
}


function tratarPagado(elemento){
	if(elemento.value==1){
		$(".yearPaid").css("display","");
	}else{
		$(".yearPaid").css("display","none");
	}
	
}



function eliminarElementoGuardar(invocador,elementoOculto,elementoMostrar,valor){
	var input=document.getElementById(elementoOculto);
	input.value=valor;
	//Ocultamos elemento inicial
	invocador.style.display="none";
	//Mostramos elemento nueva
	document.getElementById(elementoMostrar).style.display="";	
}



function configurarPosicionMenu(invocador){
	document.getElementById("hdnIdPosicion").value=invocador.value;
	//Enviamos formulario
	document.forms[0].submit();
}
function configurarCategoriaProducto(invocador){
	document.getElementById("hdnIdCategoria").value=invocador.value;
	//Enviamos formulario
	document.forms[0].submit();
}

function configurarTipoMiembroMenu(invocador){
	document.getElementById("hdnIdTipoMiembro").value=invocador.value;
	//Enviamos formulario
	document.forms[0].submit();
}

function configurarCampoOrden(invocador){
	establecerExcel(0);
	document.getElementById("hdnOrden").value=invocador.value;
	//Enviamos formulario

	document.forms[0].submit();
}

function establecerExcel(valor){
	var excel=document.getElementById("excel");
	if(excel){
		excel.value=valor;
	}	
} 

function configurarNumeroRegistros(invocador){
	establecerExcel(0);
	document.getElementById("hdnRegistros").value=invocador.value;
	//Enviamos formulario
	document.forms[0].submit();
}


function seleccionarFila(invocador){
	var idNumerico=invocador.id.split("_")[1];
	var icono=document.getElementById("icon_"+idNumerico);
	//Este item nos dira si esta seleccionado o no
	var itemEstado=document.getElementById("hdnSeleccionado_"+idNumerico);
	var classAll=document.getElementById("checkAll");
	var esCheck;
	var contenedor;
	var filas;
	var totalFilas;
	var i;
	var idNumerico;
	if(parseInt(itemEstado.value)==0){
		//Ahora lo seleccionaremos
		itemEstado.value=1;
		icono.className="checked";
		
		//Comprobamos si tenemos todos seleccionados, si es asi el checkAll se activa
		contenedor=document.getElementById("tblListItem");
		filas=contenedor.getElementsByTagName("tr");
		totalFilas=filas.length;
		i=0;
		esCheck=true;
		
		//Recorremos las filas
		while(i<totalFilas && esCheck){
			idNumerico=filas[i].id.split("_")[1];
			if(document.getElementById("hdnSeleccionado_"+idNumerico).value=="0"){
				esCheck=false;
			}
			i++;
		}
		if(esCheck){
			classAll.className="checkAll";
		}
		
	}else{
		icono.className="unChecked";
		itemEstado.value=0;
		classAll.className="unCheckAll";
	}
}

function seleccionarTodo(invocador){
	var contenedor=document.getElementById("tblListItem");
	var filas=contenedor.getElementsByTagName("tr");
	var totalFilas=filas.length;
	var i;
	var idNumerico;
	var valorInput;
	var claseConcreta;
	if(invocador.className=="unCheckAll"){
		valorInput=1;
		claseConcreta="checked";
		invocador.className="checkAll";
		
	}else{
		valorInput=0;
		claseConcreta="unChecked";
		invocador.className="unCheckAll";
	}
	
	for(i=0;i<totalFilas;i++){
		idNumerico=filas[i].id.split("_")[1];
		document.getElementById("hdnSeleccionado_"+idNumerico).value=valorInput;
		document.getElementById("icon_"+idNumerico).className=claseConcreta;
	}
}

function activarCheck(invocador,idInput){
	var input=document.getElementById(idInput);
	if(parseInt(input.value)==0){
		invocador.className="checked";
		input.value=1;
	}else{
		invocador.className="unChecked";
		input.value=0;
	}
}


function multiItem(titulo,mensaje,botonYes,botonNo,formularioEnviar,idBody,routineName){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	var form=document.getElementById(formularioEnviar);
	var esDenegado=false;
	var totalClicado=0;
	var permisoEscritura;
	
	for(i=0;i<totalFilas;i++){
		var idNumerico=tBody.rows[i].id.split("_")[1];
		if(document.getElementById("hdnSeleccionado_"+idNumerico).value==1){
			permisoEscritura=document.getElementById("hdnPermisoEscritura_"+idNumerico);
			
			//Si estamos tratando con permisos...
			if(permisoEscritura){
				if(permisoEscritura.value==1){
					cadenaIdFila+=idNumerico+separador;
				}else{
					esDenegado=true;
				}
			}else{
				//Para las secciones que no trabajen con permisos...
				cadenaIdFila+=idNumerico+separador;
			}
			totalClicado++;
		}
	}
		
	if(esDenegado && totalClicado==1){
		mostrarAlerta(document.getElementById("permisoEscrituraFraseTitulo").innerHTML,document.getElementById("permisoEscrituraFraseDescripcion").innerHTML,false);
	}else{
		if(cadenaIdFila!=""){
			$("#dialog-message").dialog({
				modal: true,
				title: titulo,
				resizable:false,
				open: function(){
					this.style.display="";
					this.innerHTML=mensaje;
				},
				buttons: [{
					text:botonYes,
					click:function() {
						$(this).dialog("close");
						this.style.display="none";
						cadenaIdFila=cadenaIdFila.substring(0,cadenaIdFila.length-1);
						//document.getElementById("hdnId").value=cadenaIdFila;
                                                form.elements.hdnId.value = cadenaIdFila;
						form.method="post";
					// form.setAttribute("action","main_app.php?section=invoice&action=generatebatch");
                    //  form.setAttribute("action","sections/invoice/generate_batch_invoice.php");
                      	form.setAttribute("action",routineName);
						
						//Enviamos
						form.submit();
					}
				},
				{
					text:botonNo,
					click:function(){
						$(this).dialog("close");
						this.style.display="none";
					}
				}]
			});
		}
	}
	
}


function eliminarElemento(titulo,mensaje,botonYes,botonNo,formularioEnviar,idBody){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	var form=document.getElementById(formularioEnviar);
	var esDenegado=false;
	var totalClicado=0;
	var permisoEscritura;
	
	for(i=0;i<totalFilas;i++){
		var idNumerico=tBody.rows[i].id.split("_")[1];
		if(document.getElementById("hdnSeleccionado_"+idNumerico).value==1){
			permisoEscritura=document.getElementById("hdnPermisoEscritura_"+idNumerico);
			
			//Si estamos tratando con permisos...
			if(permisoEscritura){
				if(permisoEscritura.value==1){
					cadenaIdFila+=idNumerico+separador;
				}else{
					esDenegado=true;
				}
			}else{
				//Para las secciones que no trabajen con permisos...
				cadenaIdFila+=idNumerico+separador;
			}
			totalClicado++;
		}
	}
		
	if(esDenegado && totalClicado==1){
		mostrarAlerta(document.getElementById("permisoEscrituraFraseTitulo").innerHTML,document.getElementById("permisoEscrituraFraseDescripcion").innerHTML,false);
	}else{
		if(cadenaIdFila!=""){
			$("#dialog-message").dialog({
				modal: true,
				title: titulo,
				resizable:false,
				open: function(){
					this.style.display="";
					this.innerHTML=mensaje;
				},
				buttons: [{
					text:botonYes,
					click:function() {
						$(this).dialog("close");
						this.style.display="none";
						cadenaIdFila=cadenaIdFila.substring(0,cadenaIdFila.length-1);
						//document.getElementById("hdnId").value=cadenaIdFila;
                                                form.elements.hdnId.value = cadenaIdFila;
						form.method="post";
						form.setAttribute("action","delete_item.php");
						
						//Enviamos
						form.submit();
					}
				},
				{
					text:botonNo,
					click:function(){
						$(this).dialog("close");
						this.style.display="none";
					}
				}]
			});
		}
	}
	
}

function editarElemento(seccion){
	var elementos=$("td.checked");
	var totalElementos=elementos.length;
	var idNumerico;
	if(totalElementos==1){
		idNumerico=elementos.attr("id").split("_")[1];
		switch(seccion){
			case "menu":
				parametrosAdicionales="&id_menu="+idNumerico;
				break;
			case "new":
				parametrosAdicionales="&id_noticia="+idNumerico;
				break;
			case "conference":
				parametrosAdicionales="&id_conferencia="+idNumerico;
				break;
			case "workshop":
				parametrosAdicionales="&id_taller="+idNumerico;
				break;
			case "thematic":
				parametrosAdicionales="&id_tematica="+idNumerico;
				break;
			case "job":
				parametrosAdicionales="&id_oferta_trabajo="+idNumerico;
				break;
			case "success":
				parametrosAdicionales="&id_caso_exito="+idNumerico;
				break;
			case "access_button":
				parametrosAdicionales="&id_boton_acceso="+idNumerico;
				break;
			case "diary":
				parametrosAdicionales="&id_agenda="+idNumerico;
				break;
			case "movement":
				parametrosAdicionales="&id_movimiento="+idNumerico;
				break;
			case "concept":
				parametrosAdicionales="&id_concepto="+idNumerico;
				break;
			case "payment_type":
				parametrosAdicionales="&id_tipo_pago="+idNumerico;
				break;
			case "member":
				parametrosAdicionales="&id_miembro="+idNumerico;
				break;
			case "eletter":
				parametrosAdicionales="&id_eletter="+idNumerico;
				break;
			case "history_eletter":
				parametrosAdicionales="&id_eletter="+idNumerico;
				break;
			case "history_email":
				parametrosAdicionales="&id_history_email="+idNumerico;
				break;
			case "invoice":
				parametrosAdicionales="&id_factura="+idNumerico;
				break;
			case "conference_registered":
				parametrosAdicionales="&id_inscripcion="+idNumerico+"&id_conferencia="+document.frmListaConferencia.id_conferencia.value;
				break;
				
		}		
		window.location="main_app.php?section="+seccion+"&action=edit"+parametrosAdicionales;
	}
}

function enviarElemento(seccion){
	var elementos=$("td.checked");
	var totalElementos=elementos.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	if(totalElementos==1){
		//Casos donde solo se permite 1 seleccion a la vez
		idNumerico=elementos.attr("id").split("_")[1];
		switch(seccion){
			case "eletter":
				parametrosAdicionales="&id_elemento="+idNumerico+"&id_tipo=1";
				break;
		}		
		window.location="main_app.php?section=eletter&action=send"+parametrosAdicionales;
	}
}

function gestionarFactura(){
	var elementos=$("td.checked");
	var totalElementos=elementos.length;
	var idNumerico;
	if(totalElementos==1){
		idNumerico=elementos.attr("id").split("_")[1];	
		window.location="main_app.php?section=invoice&action=edit&id_movimiento="+idNumerico;
	}
}


function reactivarElemento(titulo,mensaje,botonYes,botonNo,formularioEnviar,idBody){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	var form=document.getElementById(formularioEnviar);
	var esDenegado=false;
	var totalClicado=0;
	var permisoEscritura;
	
	for(i=0;i<totalFilas;i++){
		var idNumerico=tBody.rows[i].id.split("_")[1];
		if(document.getElementById("hdnSeleccionado_"+idNumerico).value==1){
			permisoEscritura=document.getElementById("hdnPermisoEscritura_"+idNumerico);
			
			//Si estamos tratando con permisos...
			/*if(permisoEscritura){
				if(permisoEscritura.value==1){
					cadenaIdFila+=idNumerico+separador;
				}else{
					esDenegado=true;
				}
			}else{
				//Para las secciones que no trabajen con permisos...
				cadenaIdFila+=idNumerico+separador;
			}*/
			
			cadenaIdFila+=idNumerico+separador;
			
			totalClicado++;
		}
	}
		
	if(cadenaIdFila!=""){
		$("#dialog-message").dialog({
			modal: true,
			title: titulo,
			resizable:false,
			open: function(){
				this.style.display="";
				this.innerHTML=mensaje;
			},
			buttons: [{
				text:botonYes,
				click:function() {
					$(this).dialog("close");
					this.style.display="none";
					cadenaIdFila=cadenaIdFila.substring(0,cadenaIdFila.length-1);
					document.getElementById("hdnId").value=cadenaIdFila;
					form.method="post";
					form.setAttribute("action","reactivate_item.php");
					
					//Enviamos
					form.submit();
				}
			},
			{
				text:botonNo,
				click:function(){
					$(this).dialog("close");
					this.style.display="none";
				}
			}]
		});
	}
	
}


function cancelarElemento(titulo,mensaje,botonYes,botonNo,formularioEnviar,idBody){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	var form=document.getElementById(formularioEnviar);
	var esDenegado=false;
	var totalClicado=0;
	var permisoEscritura;
	
	for(i=0;i<totalFilas;i++){
		var idNumerico=tBody.rows[i].id.split("_")[1];
		if(document.getElementById("hdnSeleccionado_"+idNumerico).value==1){
			permisoEscritura=document.getElementById("hdnPermisoEscritura_"+idNumerico);
			
			//Si estamos tratando con permisos...
			/*if(permisoEscritura){
				if(permisoEscritura.value==1){
					cadenaIdFila+=idNumerico+separador;
				}else{
					esDenegado=true;
				}
			}else{
				//Para las secciones que no trabajen con permisos...
				cadenaIdFila+=idNumerico+separador;
			}*/
			
			cadenaIdFila+=idNumerico+separador;
			
			totalClicado++;
		}
	}
		
	if(cadenaIdFila!=""){
		$("#dialog-message").dialog({
			modal: true,
			title: titulo,
			resizable:false,
			open: function(){
				this.style.display="";
				this.innerHTML=mensaje;
			},
			buttons: [{
				text:botonYes,
				click:function() {
					$(this).dialog("close");
					this.style.display="none";
					cadenaIdFila=cadenaIdFila.substring(0,cadenaIdFila.length-1);
					document.getElementById("hdnId").value=cadenaIdFila;
					form.method="post";
					form.setAttribute("action","cancel_item.php");
					
					//Enviamos
					form.submit();
				}
			},
			{
				text:botonNo,
				click:function(){
					$(this).dialog("close");
					this.style.display="none";
				}
			}]
		});
	}
	
}


function modificarEstadoElemento(formularioEnviar,idBody){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var cadenaIdFila="";
	var separador=",";
	var idNumerico;
	var form=document.getElementById(formularioEnviar);
	var esDenegado=false;
	var totalClicado=0;
	var permisoEscritura;
	
	for(i=0;i<totalFilas;i++){
		idNumerico=tBody.rows[i].id.split("_")[1];
		if(document.getElementById("hdnSeleccionado_"+idNumerico).value==1){
			idNumerico=tBody.rows[i].id.split("_")[1];
			cadenaIdFila+=idNumerico+separador;
		}
	}

	
	cadenaIdFila=cadenaIdFila.substring(0,cadenaIdFila.length-1);
	document.getElementById("hdnId").value=cadenaIdFila;
	form.method="post";
	form.setAttribute("action","change_item_state.php");
	
	//Enviamos
	form.submit();
}


function modificarBooleanoElemento(idNumerico,idDestino,contenedorRespuesta){
	var valorActual=document.getElementById("hdnBooleanoElemento_"+contenedorRespuesta+"_"+idNumerico);

	
	$.ajax({
        type: "POST",
        url: "ajax/save_boolean_information.php",
        data: "idDestino="+idDestino+"&idElemento="+idNumerico+"&valorActual="+valorActual.value,
        success: function(data){
        	var respuesta=data.split("-");
        	document.getElementById(contenedorRespuesta+"_"+idNumerico).innerHTML=respuesta[0];
        	valorActual.value=respuesta[1];
      	}
	});

}


//Dada el id de un tbody de tabla, obtenemosel id numero de cada fila, por ejemplo de fila_1, obtenemos el 1
function obtenerIdentificadoresTabla(idBody){
	var tBody=document.getElementById(idBody);
	var totalFilas=tBody.rows.length;
	var i=0;
	var cadenaIdFila="";
	var separador=",";
	
	for(i=0;i<totalFilas;i++){
		if(i==totalFilas-1){
			separador="";
		}
		cadenaIdFila+=tBody.rows[i].id.split("_")[1]+separador;
	}
	
	return cadenaIdFila;
}

function modificarTab(invocador,tabActual,contenidoTabDesc,tipoTab){
	var idNumericoActual=tabActual.split("_")[1];
	var idNumericoDestino=invocador.id.split("_")[1];
	
	//Aspecto grafico de las tabs
	document.getElementById(tabActual).className="";
	invocador.className="active";
	
	//Contenido relacionado con las tabs
	document.getElementById(contenidoTabDesc+"_"+idNumericoActual).style.display="none";
	document.getElementById(contenidoTabDesc+"_"+idNumericoDestino).style.display="";
	
	
	switch(tipoTab){
		case 1:
			//Trabajamos con idioma
			tabIdioma=idNumericoDestino;
			//Desactivamos tab opcion
			document.getElementById("generalContainer").style.display="";
			document.getElementById("optionsContainer").style.display="none";
			document.getElementById("opciones").className="";
		break;
	}
}

function expandirSeccion(contenedorIcono,contenidoSeccion){
	let container=document.getElementById(contenedorIcono);
	let sectionContent=document.getElementById(contenidoSeccion);
	if(container.className==="iconExpand"){
		container.className="iconCollapse";
		sectionContent.style.display="none";
	}else{
		container.className="iconExpand";
		sectionContent.style.display="";
	}
}

function obtenerComboSubCategoria(invocador){
	var contenedor=document.getElementById("comboSubCategoria");
	
	//No se selecciona nada
	if(invocador.value==='0'){
		contenedor.innerHTML="";
	}else{
		var ajax=nuevoAjax();
		ajax.open("GET","ajax/load_combo.php?c=1&id="+invocador.value);
		ajax.onreadystatechange=function(){
			if(ajax.readyState===4){
				contenedor.innerHTML=ajax.responseText;
			}
		};
		ajax.send(null);
	}
}

function obtenerComboSubConcepto(invocador){
	var contenedor=document.getElementById("comboConcepto");
	
	//No se selecciona nada
	if(invocador.value===0){
		$(".contenedorSubConcepto").css("display","none");
		contenedor.innerHTML="";
	}else{
		var ajax=nuevoAjax();
		ajax.open("GET","ajax/load_combo.php?c=4&id="+invocador.value);
		ajax.onreadystatechange=function(){
			if(ajax.readyState===4){
				$(".contenedorSubConcepto").css("display","");
				contenedor.innerHTML=ajax.responseText;
			}
		};
		ajax.send(null);
	}
}

//On change in user type dropdown (Member, Editor, Council, etc.) in “Create member” form
function obtenerComboModalidadUsuario(invocador){
	let contenedor=document.getElementById("comboModalidad");
	let contenedorLabel=document.getElementById("labelComboModalidad");
	//Initially, hide all non-nominee elements and the “Institution” dropdown
	$(".noNominee").css("display","");
	$(".institucionAsociada").css("display","none");
	$(".situacionAdicional").css("display","none");

	
	if(invocador.value==='0'){
		//If no user type is selected, hide the User mode (Individual/ Institution) dropdown
		contenedor.innerHTML="";
		contenedorLabel.style.display="none";
	}else{
		//Otherwise, load the User mode dropdown
		let ajax=nuevoAjax();
		ajax.open("GET","ajax/load_combo.php?c=3&id="+invocador.value);
		ajax.onreadystatechange=function(){
			if(ajax.readyState===4){
				contenedor.innerHTML=ajax.responseText;
				contenedorLabel.style.display="";
				if(invocador.value==='5'){
					//If User type = Nominee, hide all non-nominee elements and display “Institution” dropdown
					$(".noNominee").css("display","none");
					$(".institucionAsociada").css("display","block");
				}
			}
		};
		ajax.send(null);
	}
}

/*
 * On change in User type dropdown in the “Edit member” form
 * If member type is "Nominee" (5), hide and display elements as appropriate
 */
function gestionarBloqueNominee(invocador){
	let notForNominees = $(".noNominee");
	let associatedInstitution = $(".institucionAsociada");
	if(invocador.value==='5'){
		notForNominees.css("display","none");
		associatedInstitution.removeClass("noDisplayed");
		associatedInstitution.addClass("displayed");
	}else{
		notForNominees.css("display","");
		associatedInstitution.removeClass("displayed");
		associatedInstitution.addClass("noDisplayed");
	}
}

//Get items for user mode dropdown
function obtenerBloqueModalidad(invocador){
	$(".displayed").removeClass("displayed").addClass("noDisplayed");
	$(".situacionAdicional").css("display","none");
	$(".institucionAsociada").css("display","");
	$(".institucionAsociada").css("display","none");
	if(invocador.value==1){
		//Individual
		document.getElementById("bloqueIndividual").className="displayed";
		document.getElementById("infoImage_1").className="displayed";
		document.getElementById("infoImage_2").className="noDisplayed";
		$(".situacionAdicional").css("display","");
		$(".institucionAsociada").css("display","none");
		$(".institucionAsociada").css("display","");
		
	}else if(invocador.value==2){
		//Institucion
		document.getElementById("bloqueInstitucion").className="displayed";
		document.getElementById("infoImage_1").className="noDisplayed";
		document.getElementById("infoImage_2").className="displayed";
	}
}


function obtenerComboMenu(invocador){
	var contenedor=document.getElementById("containerMenuCombo");
	var elementoDiv;
	var crearDiv=true;
	var ajax;
	var vectorElementoDiv;
	var totalVectorElementoDiv;
	var i;
	var idInvocador=invocador.parentNode.id.split("_")[1];

	vectorElementoDiv=contenedor.getElementsByTagName("div");
	totalVectorElementDiv=vectorElementoDiv.length;
	for(i=0;i<totalVectorElementDiv;i++){
		if(parseInt(vectorElementoDiv[i].id.split("_")[1])>parseInt(idInvocador)){
			vectorElementoDiv[i].innerHTML="";
		}
	}	
	
	if(invocador.value!="0"){
		contadorMenu++;
		elementoDiv=document.createElement("div");
		elementoDiv.id="containerItemCombo_"+contadorMenu;
		elementoDiv.className="comboMenuCopy";
	
		
		ajax=nuevoAjax();
		ajax.open("GET","ajax/load_combo.php?c=2&id="+invocador.value+"&idContenedor="+contadorMenu+"&idMenu="+document.getElementById("hdnIdMenu").value);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==4){
				elementoDiv.innerHTML=ajax.responseText;
				contenedor.appendChild(elementoDiv);
			}
		}
		ajax.send(null);
	}
}

function obtenerComboTipo(invocador) {
	var contenedor=document.getElementById("containerTipoMultimedia");
	if(invocador.value == 3) {
		contenedor.style.display="";
	} else {
		contenedor.style.display="none";
		document.getElementById("cmbTipoMedia").value=0;
	}
}

function mostrarOpciones(invocador){
	document.getElementById("generalContainer").style.display="none";
	document.getElementById("optionsContainer").style.display="";
	invocador.className="active";
	
	//No class active
	document.getElementById("idiomaTab_"+tabIdioma).className="";
}

function mostrarMenuAsuntoFormulario(invocador){
	var displayAsunto=""
	if(invocador.value==0){
		displayAsunto="none";
	}
	$(".datosFormulario").css("display",displayAsunto);
}

function inArray(array,valor){
	var i=0;
	var totalElementos=array.length;
	var encontrado=false;
	
	while(i<totalElementos && !encontrado){
		if(array[i]==valor){
			encontrado=true;
		}
		i++;
	}
	return encontrado;
}

function refrescarPagina(){
	window.location.reload(true);
}

function mostrarAlerta(titulo,mensaje,refrescar){
	$("#dialog-message").dialog({
		modal: true,
		title: titulo,
		resizable:false,
		open: function(){
			this.style.display="";
			this.innerHTML=mensaje;
		},
		close:function(){
			if(refrescar){
				refrescarPagina();
			}
		},
		buttons: {
			Ok: function() {
				$( this ).dialog("close");
				this.style.display="none";
				
				if(refrescar){
					refrescarPagina();
				}
			}
		}
	});
}

function initialize() {
	geocoder = new google.maps.Geocoder();
	var myOptions = {
      scrollwheel: true,
      draggable: true,
      disableDoubleClickZoom: true,
      streetViewControl: true,
      mapTypeControl: false,
      scaleControl: true,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
	map = new google.maps.Map(document.getElementById("mapa"), myOptions);
	
}
	  	
function centrarMapa(address, zoom){
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

function buscarMiembros(){
	var cadenaDeseleccionada;
	var idNumerico;
	var seleccionable;
	$.ajax({
        type: "POST",
        url: "ajax/search_conference_member.php",
        data: "texto="+document.frmConferencia.txtQuery.value+"&id_conferencia="+document.frmConferencia.hdnIdConferencia.value+"&desel="+document.frmConferencia.hdnDeseleccionados.value,
        success: function(data){
        	//Si es correcto...
        	document.getElementById("contenidoBusqueda").innerHTML=data;
        	
        	//Miro los que hay selected
        	$("#listMembers input").each(function(index,valor){
        		idNumerico=valor.id.split("_")[1];
        		seleccionable=document.getElementById("chkUsuario_"+idNumerico);
        		if(seleccionable){
        			seleccionable.checked=true;
        		}
        	});
        	
      	}
	});
}

function buscarMiembrosEletter(){
	var cadenaDeseleccionada;
	var idNumerico;
	var seleccionable;
	$.ajax({
        type: "POST",
        url: "ajax/search_eletter_member.php",
        data: "texto="+document.frmEletter.txtQuery.value+"&desel="+document.frmEletter.hdnDeseleccionados.value,
        success: function(data){
        	//Si es correcto...
        	document.getElementById("contenidoBusqueda").innerHTML=data;
        	
        	//Miro los que hay selected
        	$("#listMembers input").each(function(index,valor){
        		idNumerico=valor.id.split("_")[1];
        		seleccionable=document.getElementById("chkUsuario_"+idNumerico);
        		if(seleccionable){
        			seleccionable.checked=true;
        		}
        	});
        	
      	}
	});
}

function buscarMiembrosTalleres(){
	var cadenaDeseleccionada;
	var idNumerico;
	var seleccionable;
	$.ajax({
        type: "POST",
        url: "ajax/search_workshop_member.php",
        data: "texto="+document.frmTaller.txtQuery.value+"&id_taller="+document.frmTaller.hdnIdTaller.value+"&desel="+document.frmTaller.hdnDeseleccionados.value,
        success: function(data){
        	//Si es correcto...
        	document.getElementById("contenidoBusqueda").innerHTML=data;
        	
        	//Miro los que hay selected
        	$("#listMembers input").each(function(index,valor){
        		idNumerico=valor.id.split("_")[1];
        		seleccionable=document.getElementById("chkUsuario_"+idNumerico);
        		if(seleccionable){
        			seleccionable.checked=true;
        		}
        	});
        	
      	}
	});
}

function buscarMiembrosInvoice(){
	var cadenaDeseleccionada;
	var idNumerico;
	var seleccionable;
	$.ajax({
        type: "POST",
        url: "ajax/search_invoice_member.php",
        data: $("#frmBuscadorUsuario").serialize(),
        success: function(data){
        	//Si es correcto...
        	document.getElementById("contenidoBusquedaMiembro").innerHTML=data;        	
      	}//end success
	});
}


function buscarMovimientosInvoice(){
	var cadenaDeseleccionada;
	var idNumerico;
	var seleccionable;
	$.ajax({
        type: "POST",
        url: "ajax/search_invoice_movement.php",
        data: $("#frmBuscadorMovimiento").serialize(),
        success: function(data){
        	//Si es correcto...
        	document.getElementById("contenidoBusqueda").innerHTML=data;
        	
        	//Miro los que hay selected
        	$("#listMovements input").each(function(index,valor){
        		idNumerico=valor.id.split("_")[1];
        		seleccionable=document.getElementById("chkMovimiento_"+idNumerico);
        		if(seleccionable){
        			seleccionable.checked=true;
        		}
        	});
        	
      	}
	});
}


function deseleccionarUsuario(id){
	var check=document.getElementById("chkUsuario_"+id);
	if(check){
		check.checked=false;		
	}
	var nodo=document.getElementById("hdnIdMiembroActivo_"+id);
	var padre=nodo.parentNode;
	document.getElementById("listMembers").removeChild(padre);
}


function deseleccionarMovimiento(id){
	var check=document.getElementById("chkMovimiento_"+id);
	if(check){
		check.checked=false;		
	}
	var nodo=document.getElementById("hdnIdMovimientoActivo_"+id);
	var padre=nodo.parentNode;
	document.getElementById("listMovements").removeChild(padre);
}

function gestionSeleccionUsuarios(invocador){
	var id=invocador.id;
	var idNumerico=id.split("_")[1];
	if(invocador.checked){
		var itemLista=document.createElement("li");
		var itemInput=document.createElement("input");
		itemInput.name="hdnIdMiembroActivo_"+idNumerico;
		itemInput.id=itemInput.name;
		itemInput.type="hidden";
		
		var literalUsuario=document.createElement("span");
		literalUsuario.innerHTML=document.getElementById("literalUsuario_"+idNumerico).innerHTML;
		
		var itemImagen=document.createElement("img");
		itemImagen.src="images/delete.png";
		itemImagen.style.cursor="pointer";
		itemImagen.onclick=function(){
			deseleccionarUsuario(idNumerico);
		};
		
		itemLista.appendChild(itemInput);
		itemLista.appendChild(literalUsuario);
		itemLista.appendChild(itemImagen);
		document.getElementById("listMembers").appendChild(itemLista);
	}else{
		var nodo=document.getElementById("hdnIdMiembroActivo_"+idNumerico);
		var padre=nodo.parentNode;
		document.getElementById("listMembers").removeChild(padre);
	}	
}

function gestionSeleccionMovimientos(invocador){
	var id=invocador.id;
	var idNumerico=id.split("_")[1];
	if(invocador.checked){
		var itemLista=document.createElement("li");
		var itemInput=document.createElement("input");
		itemInput.name="hdnIdMovimientoActivo_"+idNumerico;
		itemInput.id=itemInput.name;
		itemInput.type="hidden";
		
		var literalMovimiento=document.createElement("span");		
		literalMovimiento.innerHTML="<a href='main_app.php?section=movement&action=edit&id_movimiento="+idNumerico+"' target='_blank'>"+document.getElementById("movementConcept_"+idNumerico).innerHTML+"-"+document.getElementById("movementPaymentType_"+idNumerico).innerHTML+"</a>";
		
		var itemImagen=document.createElement("img");
		itemImagen.src="images/delete.png";
		itemImagen.style.cursor="pointer";
		itemImagen.onclick=function(){
			deseleccionarMovimiento(idNumerico);
		};
		
		itemLista.appendChild(itemInput);
		itemLista.appendChild(literalMovimiento);
		itemLista.appendChild(itemImagen);
		document.getElementById("listMovements").appendChild(itemLista);
	}else{
		var nodo=document.getElementById("hdnIdMovimientoActivo_"+idNumerico);
		var padre=nodo.parentNode;
		document.getElementById("listMovements").removeChild(padre);
	}	
}

function agregarFecha(){	
	var contenedorDate=document.getElementById("containerDate");
	var fila=document.createElement("tr");
	fila.id="filaDate_"+contadorFecha;
	

	
	var columna1=document.createElement("td");
	var columna2=document.createElement("td");
	var columna3=document.createElement("td");
	var columna4=document.createElement("td");
	var columna5=document.createElement("td");
	var columna6=document.createElement("td");
	var columna7=document.createElement("td");
	var columna8=document.createElement("td");
	

	
	//Ponemos date picker inicio
	var inputFecha=document.createElement("input");
	inputFecha.type="text";
	inputFecha.className="inputWorkshopDate";
	inputFecha.name="txtFecha_"+contadorFecha;
	inputFecha.id=inputFecha.name;
	
	//Asignamos a columna 1
	columna1.appendChild(inputFecha);
	
	
	//Ponemos conference
	var inputConferencia=document.createElement("input");
	inputConferencia.type="checkbox";
	inputConferencia.name="chkEsConferencia_"+contadorFecha;
	inputConferencia.id=inputConferencia.name;
	
	//Asignamos a columna 2
	columna2.appendChild(inputConferencia);
	
	//Ponemos precio miembro
	var inputPrecio=document.createElement("input");
	inputPrecio.type="text";
	inputPrecio.className="inputWorkshopText";
	inputPrecio.name="txtPrecio_"+contadorFecha;
	inputPrecio.id=inputPrecio.name;
	
	//Asignamos a columna 3
	columna3.appendChild(inputPrecio);
	
	//Ponemos precio miembro sister
	inputPrecio=document.createElement("input");
	inputPrecio.type="text";
	inputPrecio.className="inputWorkshopText";
	inputPrecio.name="txtPrecioSister_"+contadorFecha;
	inputPrecio.id=inputPrecio.name;
	
	//Asignamos a columna 3
	columna4.appendChild(inputPrecio);
	
	//Ponemos precio NO miembro
	inputPrecio=document.createElement("input");
	inputPrecio.type="text";
	inputPrecio.className="inputWorkshopText";
	inputPrecio.name="txtPrecioNoSocio_"+contadorFecha;
	inputPrecio.id=inputPrecio.name;
	
	//Asignamos a columna 3
	columna5.appendChild(inputPrecio);
	
	
	//Ponemos plazas
	var inputPlazas=document.createElement("input");
	inputPlazas.type="text";
	inputPlazas.className="inputWorkshopText";
	inputPlazas.name="txtPlaza_"+contadorFecha;
	inputPlazas.id=inputPlazas.name;
	
	//Asignamos a columna 6
	columna6.appendChild(inputPlazas);
	
	//Ponemos plazas
	var inputMember=document.createElement("input");
	inputMember.type="checkbox";
	inputMember.name="chkEsMiembro_"+contadorFecha;
	inputMember.id=inputMember.name;
	
	//Asignamos a columna 7
	columna7.appendChild(inputMember);
	
	
	var imagen=document.createElement("img");
	imagen.src="images/delete.png";
	imagen.style.cursor="pointer";
	imagen.onclick=function(){
		contenedorDate.removeChild(document.getElementById(fila.id));
	}
	columna8.appendChild(imagen);

	
	//Incluimos columnas en la fila
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);
	fila.appendChild(columna4);
	fila.appendChild(columna5);
	fila.appendChild(columna6);
	fila.appendChild(columna7);
	fila.appendChild(columna8);

	
	//Incluimos fila en body
	contenedorDate.appendChild(fila);
	
	//Asignamos date picker a fecha inicio
	$( "#"+inputFecha.id ).datepicker({
		dateFormat:"dd-mm-yy",
		changeMonth: true,
		changeYear: true
	});

	
	contadorFecha++;
}


function deseleccionarRadio(invocador){
	var seleccionadoActual=document.getElementById("hdnFecha_"+invocador.name.split("_")[1]);
	var seleccionadoActualValor=seleccionadoActual.value;

	
	//Sobreescribimos
	seleccionadoActual.value=invocador.value;
	
	
	if(seleccionadoActualValor!=""){
		if(seleccionadoActualValor==seleccionadoActual.value){
			invocador.checked=false;
			seleccionadoActual.value="";
		}
	}	
	

	if(seleccionadoActual.value!=""){

		$("#container_mini_session_"+invocador.name.split("_")[1]+" input[type='checkbox']").attr("disabled","true");
		$("#container_mini_session_"+invocador.name.split("_")[1]+" input[type='checkbox']").removeAttr("checked");

	}else{
		//Al no escoger workshop, puedes escoger mini
		$("#container_mini_session_"+invocador.name.split("_")[1]+" input[type='checkbox']").removeAttr("disabled");
	}//end if


}

function autoCompletaInvoice(idFila){
	document.frmFactura.txtNif.value=document.getElementById("nif_"+idFila).innerHTML;
	document.frmFactura.txtNombreCliente.value=document.getElementById("name_"+idFila).innerHTML;
	document.frmFactura.txtNombreEmpresa.value=document.getElementById("company_"+idFila).innerHTML;
	document.frmFactura.txtDireccion.value=document.getElementById("address_"+idFila).innerHTML;
	document.frmFactura.txtCodigoPostal.value=document.getElementById("zipcode_"+idFila).innerHTML;
	document.frmFactura.txtCiudad.value=document.getElementById("city_"+idFila).innerHTML;
	document.frmFactura.txtProvincia.value=document.getElementById("province_"+idFila).innerHTML;
	document.frmFactura.txtPais.value=document.getElementById("country_"+idFila).innerHTML;
}//end function

