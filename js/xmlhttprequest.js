function getXmlHttpObject(){
	var xmlHttp;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp = new XMLHttpRequest();
	}
	catch (e){  // Internet Explorer
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
	    }
		catch (e){
			try{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
				catch (e){
					alert("Your browser does not support AJAX!");
					return false;
				}
		}
	}
	return xmlHttp;
}

/*
	The onreadystatechange Property
	
	* 0 = The request is not initialized
	* 1 = The request has been set up
	* 2 = The request has been sent
	* 3 = The request is in process
	* 4 = The request is complete

	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.myForm.time.value=xmlHttp.responseText;
		}
	}
	
	M�todos

		abort() - Detiene la petici�n en curso.
		getAllResponseHeaders() - Devuelve todas las cabeceras de la respuesta (etiquetas y valores) como una cadena.
		getResponseHeader(etiqueta) - Devuelve el valor de la etiqueta en las cabecerasde la respuesta.
		open(m�todo,URL,as�ncrona,nombre,password) - Abre una conexi�n con esa URL mediante ese metodo (GET o POST), aplicando los valores opcionales de la derecha (ahora se explicar�).
		send(contenido) - Env�a el contenido al servidor.
		setRequestHeader(etiqueta,valor) - Establece el valor de una etiqueta de las cabeceras de petici�n.
	
	Propiedades

		onreadystatechange - Contiene el nombre de la funci�n que se ejecuta cada vez que el estado de la conexi�n cambie.
		readyState - Estado de la conexi�n, puede valer desde 0 (no iniciada) hasta 4 (completado).
		responseText - Datos devueltos por el servidor en formato cadena.
		responseXML - Datos devueltos por el servidor en forma de documento XML que puede ser recorrido mediante las funciones del DOM (getEementsByTagName, etc).
		status - C�digo enviado por el servidor, del tipo 404 (documento no encotrado) o 200 (OK).
		statusText - Mensaje de texto enviado por el servidor junto al c�digo (status), para el caso de c�digo 200 contendr� �OK�.
*/