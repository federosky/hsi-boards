/**
* Auto Refresh Page with Time script
* By JavaScript Kit (javascriptkit.com)
* Over 200+ free scripts here!
*/

/**
* enter refresh time in "minutes:seconds"
* Minutes should range from 0 to inifinity.
* Seconds should range from 0 to 59
*/
var current_race = 0;
var current_race_count = 0;
// Time to change race in Seconds
var time_limit = 60;
// Time count
var time_count = 0;

var path = window.location.pathname;
var pecies = new Array();
pecies = path.split('.');
var term_no = pecies[1];

/**
 * Function to be executed every certain time
 * @param current_race
 * @param timeout_action (get_next|cicle)
 */
function beginrefresh( timeout_action ){

	/*****************************************************/
	if( timeout_action == 'get_next' ){
		var next_race;
		// gets next race number
		next_race = getNextRace();
		$('#content').slideUp(1500,
			function(){
				$('#content').html('<div id="separator"><br/>.:Carrera n&ordm;'+next_race+':.</div>').slideDown(1000);
			}
		);
		// gets race contents to display
		getContentUpdate(next_race);
	}
	/*****************************************************/
	else if( timeout_action == 'cicle' ){
		if( window['riders'] > 11 ) cicle();
	}
	/*****************************************************/
	time_limit = window['time_limit'];
	time_count = window['time_count'];
	if( time_count >= time_limit ){
		timeout_action = 'get_next';
		window['time_count'] = 0;
	}
	else{
		timeout_action = 'cicle';
		window['time_count'] += 5;
	}
	/*****************************************************/
	window['timeout_action'] = timeout_action;

	/*****************************************************/
	// funcionn que se ejecuta cada xxx milisegundos..
	setTimeout('beginrefresh(timeout_action)', 5*1000);
}

function getNextRace(){

	var response;
	xmlHttp = getXmlHttpObject();
	if( xmlHttp==null ){
		alert ("Your browser does not support AJAX!");
		return;
	}

	var term_number = window['term_no']
	url = 'get_next_race.php';
	if( !isNaN(term_number) ) url += '?screen=' + term_number;

	xmlHttp.onreadystatechange = function(response){
		if( xmlHttp.readyState==4  || xmlHttp.readyState == "complete" ){
			window['next_race'] = xmlHttp.responseText;
			response = xmlHttp.responseText;
		}
	}
	xmlHttp.open('GET',url,false);
	xmlHttp.send(null);
	response = xmlHttp.getResponseHeader('X-Race-Number');
	window['riders'] = xmlHttp.getResponseHeader('X-Rider-Count');
	return response;
}

function getContentUpdate( race_number ){
	xmlHttp = getXmlHttpObject();
	if( xmlHttp==null ){
		alert ("Your browser does not support AJAX!");
		return;
	}

	var term_number = window['term_no'];
	var url = 'get_race_info.php';
	url += '?race=' + race_number;
	//parche, prueba local
	if( !isNaN(term_number) ) url += '&term=' + term_number;

	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open('GET',url,true);
	xmlHttp.send(null);
}

function stateChanged(){
	if( xmlHttp.readyState==4  || xmlHttp.readyState == "complete" ){
		$('#content').append(xmlHttp.responseText);
		$('.grid_line:even').css('background','#e9e9e9');
		$('.grid_line:odd').css('background','#ffffff');
		$('.not_running').css('background','#ffe0e0');
		$('#separator').slideUp(1500);
	}
}

function cicle(){
	$('div.grid_line:first').slideUp(1*1000,
		function(){
			$('div.grid_line:first').appendTo('div#grid_box');
			$('div.grid_line:hidden').show(1*1000);
		}
	);
}
