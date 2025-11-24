function loadEasyNotify(itemNotify,idClass){
	//Contenedor de los mensajes de error
	var containerEasyNotify=document.getElementById("containerEasyNotify");
	var contentEasyNotify="";
	var i=0;
	var lenItemNotify;
	var className=getClassNameEasyNotify(idClass);
	
	//Si pasamos un vector de notificaciones, generaremos tantos itemsNotify como notificaciones tenga el vector
	if(isArray(itemNotify)){
		lenItemNotify=itemNotify.length;
		for(i=0;i<lenItemNotify;i++){
			contentEasyNotify+=getItemEasyNotify(itemNotify[0]);
		}
	}else{
		contentEasyNotify=getItemEasyNotify(itemNotify);
	}
	
	//Si ya teniamos una clase hecha en la maquetacion, por ejemplo para posicionar el div, no la pisamos, unicamente agregamos una nueva
	if(containerEasyNotify.className!=""){
		//Debemos tener en cuenta que si ya habiamos llamado al componente easynotify, ya tendremos una clase de tipo easynotify...
		if(containerEasyNotify.innerHTML!=""){
			var classes=containerEasyNotify.className.split(" ");
			//Eliminamos la primera clase, que siempre sera la de tipo easynotify
			classes.splice(0,1);
			//Generamos un string donde cada clase esta separada por un espacio en blanco
			containerEasyNotify.className=classes.join(" ");
		}
		containerEasyNotify.className=className+" "+containerEasyNotify.className;
	}else{
		containerEasyNotify.className=className;
	}
	
	containerEasyNotify.innerHTML=contentEasyNotify;
	
	//Mostramos lentamente mediante accion jquery
	$("#containerEasyNotify").fadeIn("slow");
}

function getClassNameEasyNotify(idClass){
	var className;
	switch(idClass){
		case 1:
			//Ponemos la clase errorNotify
			className="errorNotify";
			break;
		case 2:
			//Ponemos la clase successNotify
			className="successNotify";
			break;
	}
	
	return className;
}


function getItemEasyNotify(valueNotify){
	var content="<div class=\"iconNotify\"></div>";
	content+="<div class=\"textNotify\">"+valueNotify+"</div>";
	content+="<div class=\"clear\"></div>";
	
	return content;
}


