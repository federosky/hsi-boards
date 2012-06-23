/**
 * Define global settings/properties
 */
(function($){
	$.current = 1;
	$.showing = 1;
	$.timeout = 10*1000; // miliseconds
})(jQuery);

/**
 * Function to be executed every given time
 * @param current_race
 * @param timeout_action (get_next|cicle)
 */
function startRefresh()
{
	getCurrentRace();
	if( $.current != $.showing ){
		getContentUpdate($.current);
		$.showing = $.current;
	}

	setTimeout(
			function(){
				startRefresh()
			},
			$.timeout
	);
}

function getCurrentRace(){
	// Prepare Ajax request
	$.ajaxSetup({
		async: false,
		dataType: "text"
	});
	$.get(
		'getCurrentRace.php',
		function(data){
			$.current = data;
	 	}
	);
}

function getContentUpdate(){
	$.ajaxSetup({
		dataType: "html"
	});
	$('div#content').load(
		'get_race_info.php?race=' + $.current,
		function(){
			$('.grid_line:even').css('background','#e9e9e9');
			$('.grid_line:odd').css('background','#ffffff');
			$('.not_running').css('background','#ffe0e0');
		}
	);
}

$(document).ready(function(){
	startRefresh();
});
