function mostrarImagen(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#img_destino').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showMessage(message) {
    $(".messages").html("").show();
    $(".messages").html(message);
}

//comprobamos si el archivo a subir es una imagen
//para visualizarla una vez haya subido
function isImage(extension) {
    switch (extension.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'png':
        case 'jpeg':
            return true;
            break;
        default:
            return false;
            break;
    }
}

$(document).ready(function () {
    $('.info1').hide();
    $('.info2').hide();
    $('.info3').hide();

    $("#fileimagen").change(function () {
        $('.info1').hide();
        $('.info2').hide();
        $('.info3').hide();
        var file = $("#fileimagen")[0].files[0];

        //obtenemos el nombre del archivo
        var fileName = file.name;
        //obtenemos la extensión del archivo
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        //obtenemos el tamaño del archivo
        var fileSize = file.size;
        //obtenemos el tipo de archivo image/png ejemplo
        var fileType = file.type;

        if (isImage(fileExtension)) {
            if (fileSize < 500000) {

                $('#img_destino').show();
                $('#subirimagen').show();
                $("#subirimagen").click(function () {
                    subirimagen();
                });
                mostrarImagen(this);
            } else {
                $('.info2').show();
            }
        } else {
            $('.info1').show();
        }
    });


});

function subirimagen() {

    var tiempo = new Date();

    var hora = tiempo.getHours();
    var minuto = tiempo.getMinutes();
    var segundo = tiempo.getSeconds();
    var randomimage = Math.floor(Math.random() * 20000000000);
    randomimage = randomimage + "-" + hora + minuto + segundo + "." + fileExtension;


    $("#valorimagen").val(randomimage);
    /* Creamos un objeto FormData que es un formulario con 	enctype=multipart/form-data
y le pasamos como parametro el formulario HTML */
  	var formID = $("form").attr("id");
	var form = document.getElementById(formID);
    /* var form = document.getElementById("frmConferenceRegister");  */
    var Data = new FormData(form);


    /* Creamos el objeto que hara la petición AJAX al servidor, debemos de validar si existe el 	objeto “ XMLHttpRequest” ya que en internet explorer viejito no esta, y si no esta usamos
    “ActiveXObject” */

    if (window.XMLHttpRequest) {
        var Req = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        var Req = new ActiveXObject("Microsoft.XMLHTTP");
    }

    //Pasándole la url a la que haremos la petición
    Req.open("POST", "grab_photo_main.php", true);

    /* Le damos un evento al request, esto quiere decir que cuando
    termine de hacer la petición, se ejecutara este fragmento de
    código */

    Req.onload = function (Event) {
        //Validamos que el status http sea  ok
        if (Req.status == 200) {
            /*Como la info de respuesta vendrá en JSON
            la parseamos */
            var st = JSON.parse(Req.responseText);

            if (st.success) {
                $('.info3').show();
            } else {
                message = $("<span class='error'>An error has occurred.</span>");
                showMessage(message);
            }
        } else {
            console.log(Req.status); //Vemos que paso.
        }
    };

    //Enviamos la petición
    Req.send(Data);
}

/** Funciones generales **/

/* carousel_resume_initCallback */
function carrousel_resume_initCallback(carousel) {
    // Disable autoscrolling if the user clicks the prev or next button.
    carousel.buttonNext.bind('click', function () {
        carousel.startAuto(0);
    });

    carousel.buttonPrev.bind('click', function () {
        carousel.startAuto(0);
    });

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function () {
        carousel.stopAuto();
    }, function () {
        carousel.startAuto();
    });
}

/* Creamos el objeto Ajax */
function nuevoAjax() {
    var xmlhttp = false;
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
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

/* validar formato del correo electrónico */
function validarEmail(email) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return reg.test(email);
}

function ejecutarIntroLogin(evento) {
    if (evento.keyCode == 13) {
        document.frmLogin.btnLogin.click();
    }
}

/* recuperamos el valor seleccionado de un conjunto de radiobuttons */
function radioButtonValorSeleccionado(elemento) {
    var totalElementos = elemento.length;
    var i = 0;

    while (i < totalElementos) {
        if (elemento[i].checked) {
            return elemento[i].value;
        }
        i++;
    }

    return -1;
}

/* Dado un elemento, si este esta activo activara el campo de texto */
function abrirTexto(elemento, campoTexto) {
    if (elemento.checked) {
        document.getElementById(campoTexto).style.display = "block";
    } else {
        document.getElementById(campoTexto).style.display = "none";
    }
}

/* validamos si el valor es numerico */
function IsNumeric(elemento) {
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
function trim(cadena) {
    return cadena.replace(/^\s+|\s+$/gi, "");
}

/* placeholder vaciar input text */
function limpiarInputValue(elemento, fraseDefecto) {
    if (elemento.value == fraseDefecto) {
        elemento.value = "";
    }
}

/* placeholder restaurar input text */
function restaurarInputValue(elemento, fraseDefecto) {
    if (elemento.value == "") {
        elemento.value = fraseDefecto;
    }
}

/* placeholder vaciar textarea */
function limpiarTextAreaValue(elemento, fraseDefecto) {
    if (elemento.value == fraseDefecto) {
        elemento.value = "";
    }
}

/* placeholder restaurar textarea */
function restaurarTextAreaValue(elemento, fraseDefecto) {
    if (elemento.value == "") {
        elemento.value = fraseDefecto;
    }
}

/* placeholder vaciar textarea */
function limpiarTextAreaEditorValue(editor, fraseDefecto) {
    var instancia = CKEDITOR.instances[editor];
    if (instancia.getData() == fraseDefecto) {
        instancia.document.getBody().setHtml("");
    }
}

/* placeholder restaurar textarea */
function restaurarTextAreaEditorValue(editor, fraseDefecto) {
    var instancia = CKEDITOR.instances[editor];
    if (instancia.getData().replace("<br />", "").trim() == "") {
        instancia.document.getBody().setHtml(fraseDefecto);
    }
}

function limpiarInputPassword(elemento, destino) {
    elemento.style.display = "none";
    document.getElementById(destino).style.display = "";
    document.getElementById(destino).focus();
}

function restaurarInputPassword(elemento, destino, fraseDefecto) {
    if (elemento.value == "") {
        elemento.style.display = "none";
        var passAuxiliar = document.getElementById(destino);
        passAuxiliar.style.display = "";
        passAuxiliar.value = fraseDefecto;
    }
}

/* Movemos la situación de la web arriba */
function moverWeb(elemento) {
    scroll(0, 0);
}

function eliminarElementoGuardar(invocador, elementoOculto, elementoMostrar, valor) {
    var input = document.getElementById(elementoOculto);
    input.value = valor;
    invocador.style.display = "none";
    document.getElementById(elementoMostrar).style.display = "";
}


/* Capa de cargando */
function mensajeCargando(mensaje) {
    $.blockUI({
        message: mensaje, css: {
            border: 'none',
            padding: '15px',
            fontFamily: 'Arial',
            fontSize: '14px',
            fontWeight: "bold",
            border: '10px solid #348a88',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}

/* anclar pantalla */
function anclarElemento(elemento) {
    location.hash = "#" + elemento;
}

function centrarPantallaElemento(elemento) {
    $("html, body").animate({
        scrollTop: $(elemento).offset().top
    }, 1000);
}

function mostrarMensajeError(mensaje) {
    document.getElementById("messageError").innerHTML = mensaje;
    document.getElementById("messageError").style.display = "block";
}

function obtenerComboSubConcepto(invocador) {
    var contenedor = document.getElementById("comboConcepto");

    //No se selecciona nada
    if (invocador.value == 0) {
        $(".contenedorSubConcepto").css("display", "none");
        contenedor.innerHTML = "";
    } else {
        var ajax = nuevoAjax();
        ajax.open("GET", "ajax/load_combo.php?c=1&id=" + invocador.value);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                $(".contenedorSubConcepto").css("display", "");
                contenedor.innerHTML = ajax.responseText;
            }
        };
        ajax.send(null);
    }
}

/* MET Guidelines (How to Choose)
window.addEventListener('load', function(){
    console.log('in');*/

/*var tabControls = document.querySelectorAll('.tab-controls div[data-target]');
Array.prototype.forEach.call(tabControls, function(control, i){
    control.addEventListener('click', function(){
        var target = this.dataset.target;
        var tab = document.querySelector('.tab[data-section="'+target+'"]');
        var activeTab = document.querySelector('.tab.active[data-section]') || false;

        if(activeTab && activeTab != tab){
            activeTab.classList.remove('active');
            tab.classList.add('active');
        }else{
            tab.classList.toggle('active');
        }
    });
});
*/

/* FAQ Accordion (How to choose an editor or translator) */
window.addEventListener('load', function () {
    var faqControls = document.querySelectorAll('h3[data-faq]');
    Array.prototype.forEach.call(faqControls, function (control, i) {
        control.addEventListener('click', function () {
            var target = this.dataset.faq;
            var targetEle = document.querySelector('div[data-faq="' + target + '"]');
            var activeEle = document.querySelector('div[data-faq].active') || false;

            if (activeEle && activeEle != targetEle) {
                activeEle.classList.remove('active');
                targetEle.classList.add('active');
            } else {
                targetEle.classList.toggle('active');
            }
        });
    });
});

// Add support for HTML5 placeholder in form fields in older browsers
// From http://diveintohtml5.org/detect.html
function supports_input_placeholder() {
    var i = document.createElement("input");
    return "placeholder" in i;
}

if (!supports_input_placeholder()) {
    var fields = document.getElementsByTagName("INPUT");
    for (var i = 0; i < fields.length; i++) {
        if (fields[i].hasAttribute("placeholder")) {
            fields[i].defaultValue = fields[i].getAttribute("placeholder");
            fields[i].onfocus = function () {
                if (this.value === this.defaultValue) this.value = "";
            };
            fields[i].onblur = function () {
                if (this.value === '') this.value = this.defaultValue;
            }
        }
    }
}