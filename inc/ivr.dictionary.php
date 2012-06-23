<?php
// carreras
define('CARRERA_1', 'Primera carrera');
define('CARRERA_2', 'Segunda carrera');
define('CARRERA_3', 'Tercer carrera');
define('CARRERA_4', 'Cuarta carrera');
define('CARRERA_5', 'Quinta carrera');
define('CARRERA_6', 'Sexta carrera');
define('CARRERA_7', 'Septima carrera');
define('CARRERA_8', 'Octava carrera');
define('CARRERA_9', 'Novena carrera');
define('CARRERA_10', 'Decima carrera');
define('CARRERA_11', 'Decimo primera carrera');
define('CARRERA_12', 'Decimo segunda carrera');
define('CARRERA_13', 'Decimo tercera carrera');
define('CARRERA_14', 'Decimo cuarta carrera');
define('CARRERA_15', 'Decimo quinta carrera');
define('CARRERA_16', 'Decimo sexta carrera');
define('CARRERA_17', 'Decimo septima carrera');
define('CARRERA_18', 'Decimo octava carrera');
define('CARRERA_19', 'Decimo novena carrera');
define('CARRERA_20', 'Vigesima carrera');

// distancias
define('PUESTO_1', 'primero');
define('PUESTO_2', 'segundo');
define('PUESTO_3', 'tercero');
define('PUESTO_4', 'cuarto');
define('PUESTO_5', 'quinto');
define('PUESTO_6', 'sexto');
define('PUESTO_7', 'septimo');
define('PUESTO_8', 'octavo');
define('PUESTO_9', 'noveno');
define('PUESTO_10', 'decimo');
define('PUESTO_11', 'decimo primero');
define('PUESTO_12', 'decimo segundo');
define('PUESTO_13', 'decimo tercero');
define('PUESTO_14', 'decimo cuarto');
define('PUESTO_15', 'decimo quinto');
define('PUESTO_16', 'decimo sexto');
define('PUESTO_17', 'decimo septimo');
define('PUESTO_18', 'decimo octavo');
define('PUESTO_19', 'decimo noveno');
define('PUESTO_20', 'vigesimo');

function IVR_tr($text){
	if( !defined($text) ) return $text;
	return constant($text);
}

define('VM','ventaja minima');
define('HOCICO','hocico');
define('1/2CZA','media cabeza');
define('CABEZA','cabeza');
define('1/2PZO','medio pescuezo');
define('PZO','pescuezo');
define('1/2C','medio cuerpos');
define('1/2CPO','medio cuerpo');
define('3/4CPO','tres cuartos cuerpo');
define('CPOS','cuerpos');
define('CZA','cabeza');

function IVR_tr_diff($diff){
	if( !defined($diff) ){
		$pieces = explode(' ',$diff);
		if( count($pieces) > 1 ){
			$diff = $pieces[0].' '.constant($pieces[1]);
		}
	}
	else $diff = constant($diff);

	return $diff;
}
?>