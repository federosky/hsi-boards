<?php
/**
 * 
 */
require_once(dirname(__FILE__).'/../lib/logger.class.php');

$logger = Logger::instance('Syslog', 'IVR_generators', LOGGER_ALL);
/**
 *
 */
function IVR_generate_deleted( $data )
{
	if( !is_array($data) || !count($data) ) return false;
	require_once(dirname(__FILE__).'/ivr.dictionary.php');
	$file_path = dirname(__FILE__).'/../tmp/ivr/borrados';
	$raw_data = array();

	$raw_data = $data;
	foreach( $raw_data as $race => $deleted ){
		$deleted = array_map('IVR_array_ltrim', $deleted);
		$text = sprintf('Borrados de la %s , %s', IVR_tr('CARRERA_'.$race), implode(', ',$deleted));
		$file = sprintf('%s/%s.txt', $file_path, $race);
		file_put_contents($file, $text);
	}
}

/**
 *
 */
function IVR_generate_results( $data )
{
	/*
	segunda carrera, ganador el 6, segundo el 5, tercero el 4, cuarto el 3, quinto el 2, sexto el 1, Distancia del segundo al tercero MEDIA CABEZA, Distancia del tercero al cuarto CABEZA, Distancia del cuarto al quinto MEDIO PEZCUEZO, Distancia del quinto al sexto HOCICO, Distancia del sexto al s�ptimo VARIOS CUERPOS
	*/
	if( !is_array($data) ) return false;
	require_once(dirname(__FILE__).'/ivr.dictionary.php');
	$file_path = dirname(__FILE__).'/../tmp/ivr/resultados';

	foreach( $data as $race => $info )
	{
		// Sort array by keys
		ksort($info);
		if( count($info) >= 6 )
			$info = array_slice($info, 0, 6, true);
		$text = sprintf('%s', IVR_tr('CARRERA_'.$race));
		
		$pieces_order = array();
		$pieces_diff  = array();
		foreach( $info as $puesto => $spc_info ){
			$puesto = ltrim($puesto,' 0');
			array_push($pieces_order,
							sprintf('%s el %s',
									IVR_tr(sprintf('PUESTO_%s',$puesto)),
									ltrim($spc_info['orden'],' 0')
							)
			);
			// Diferencias
			if( !empty($spc_info['diferencia']) ){
				$text_diff = sprintf('distancia del %s al %s %s',
									IVR_tr(sprintf('PUESTO_%s',($puesto)-1)),
									IVR_tr(sprintf('PUESTO_%s',$puesto)),
									IVR_tr_diff(sprintf('%s',strtoupper($spc_info['diferencia'])))
							);
				array_push($pieces_diff, $text_diff);
			}
		}

		$text.= ', '.implode(', ', $pieces_order).'.';
		$text.= ' '.implode(', ', $pieces_diff).'.';

		$file = sprintf('%s/%s.txt', $file_path, $race);
		file_put_contents($file, $text);
	}
}

/**
 *
 */
function IVR_generate_changed($IVR_changed){
	/* Cambios de monta de la primera carrera, jockey del numero # <nombre> */
	require_once(dirname(__FILE__).'/ivr.dictionary.php');
	$file_path = dirname(__FILE__).'/../tmp/ivr/cambios';

	echo '<pre>Changed >> '.print_r($IVR_changed,1).'</pre>';

	foreach( $IVR_changed as $race => $changed ){
		$text = sprintf('Cambios de monta de la %s:', IVR_tr('CARRERA_'.$race));
		$changes = array();
		foreach( $changed as $info ){
			array_push($changes, sprintf('jockey del numero %s, %s',
										ltrim($info['orden'], ' 0'),
										$info['jockey'])
			);
		}
		$text.= sprintf(' %s', implode(', ',$changes)).'.';
		echo 'Text >> '.$text;
		$file = sprintf('%s/%s.txt', $file_path, $race);
		file_put_contents($file, $text);
	}
}

/**
 *
 */
function IVR_generate_race_defaults( $races )
{
	require_once(dirname(__FILE__).'/ivr.dictionary.php');

	$file_path_deleted = dirname(__FILE__).'/../tmp/ivr/borrados';
	$file_path_results = dirname(__FILE__).'/../tmp/ivr/resultados';
	$file_path_changed = dirname(__FILE__).'/../tmp/ivr/cambios';

	foreach( $races as $race_no => $race ){
		$file_deleted = sprintf('%s/%s.txt', $file_path_deleted, $race_no);
		file_put_contents($file_deleted, sprintf('Borrados de la %s, corren todos.', IVR_tr('CARRERA_'.$race_no)));

		$file_results = sprintf('%s/%s.txt', $file_path_results, $race_no);
		file_put_contents($file_results, sprintf('%s, sin efectuarse.', IVR_tr('CARRERA_'.$race_no)));

		$file_changed = sprintf('%s/%s.txt', $file_path_changed, $race_no);
		file_put_contents($file_changed, sprintf('%s, sin cambios.', IVR_tr('CARRERA_'.$race_no)));
	}

}

/**
 *
 */
function IVR_array_ltrim($value){
	return ltrim($value, ' 0');
}

?>