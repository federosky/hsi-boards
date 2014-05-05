<?php
/**
 * @package IVR
 * @subpackage IVR_Generator
 */
require_once(dirname(__FILE__).'/../../lib/logger.class.php');
require_once(dirname(__FILE__).'/Dictionary.php');
/**
 * IVR Generator class
 * Generates messages for each event type
 */
class Ivr_Generator
{
	const TYPE_DEFAULT = 'default';
	const TYPE_DELETED = 'deleted';
	const TYPE_CHANGED = 'changed';
	const TYPE_RESULTS = 'results';
	/**
	 * Instance holder
	 * @var IVR_Generator
	 */
	static private $_instance = null;
	/**
	 * Configuration
	 * @var array
	 */
	public $config;
	/**
	 * Logger instance
	 */
	protected $logger;

	/**
	 * Class constructor
	 */
	protected function __construct()
	{
		$this->config = require_once(dirname(__FILE__).'/config/ivr.php');
		$this->logger = Logger::instance('Syslog', 'IVR_Generator', LOGGER_ALL);
	}

	/**
	 * Class instanciate
	 *
	 * @return IVR_Generator
	 */
	static public function getInstance()
	{
		if (empty(self::$_instance)) {
			self::$_instance = new IVR_Generator();
		}
		return self::$_instance;
	}

	/**
	 *
	 * @param unknown_type $items
	 */
	public function defaults($items)
	{
		$this->logger->debug(__METHOD__);
		//
		foreach( $items as $race_no => $race ){
		    $file_deleted = sprintf('%s/%s.txt', $this->config[self::TYPE_DELETED]['directory.src'], $race_no);
		    file_put_contents(
	    		$file_deleted,
	    		sprintf('Borrados de la %s, corren todos.', IVR_Dictionary::tr('CARRERA_'.$race_no, IVR_Dictionary::TAXONOMY_RACE))
    		);

		    $file_results = sprintf('%s/%s.txt', $this->config[self::TYPE_RESULTS]['directory.src'], $race_no);
		    file_put_contents(
	    		$file_results,
	    		sprintf('%s, sin efectuarse.', IVR_Dictionary::tr('CARRERA_'.$race_no, IVR_Dictionary::TAXONOMY_RACE))
    		);

		    $file_changed = sprintf('%s/%s.txt', $this->config[self::TYPE_CHANGED]['directory.src'], $race_no);
		    file_put_contents(
	    		$file_changed,
		    	sprintf('%s, sin cambios.', IVR_Dictionary::tr('CARRERA_'.$race_no, IVR_Dictionary::TAXONOMY_RACE))
    		);
		}
	}

	/**
	 * Generates messages for deleted horses from a race
	 *
	 * @param array $items
	 *
	 * @return array Collection of messages
	 */
	public function deleted($items)
	{
		$this->logger->debug(__METHOD__);
		$messages = array();
		foreach( $items as $race => $deleted ){
		    $deleted = array_map('IVR_array_ltrim', $deleted);
		    $text = sprintf(
	    		'Borrados de la %s, %s',
		    	IVR_Dictionary::tr('CARRERA_'.$race, IVR_Dictionary::TAXONOMY_RACE),
		    	implode(', ',$deleted)
    		);
		    $messages[$race] = $text;
		}

		return $messages;
	}

	/**
	 * Generates messages for changes on horse's jockey
	 *
	 * @param array $changed
	 *
	 * @return array
	 */
	public function changed($items)
	{
		$this->logger->debug(__METHOD__);
		$messages = array();

		foreach ($items as $race => $changed) {
		    $text = sprintf(
	    		'Cambios de monta de la %s:',
		    	IVR_Dictionary::tr('CARRERA_'.$race, IVR_Dictionary::TAXONOMY_RACE)
    		);
		    $changes = array();
		    foreach ($changed as $info){
		        array_push(
		            $changes,
		            sprintf('jockey del numero %s, %s', ltrim($info['orden'], ' 0'), $info['jockey'])
		        );
		    }
		    $text.= sprintf(' %s', implode(', ',$changes)).'.';
		    $messages[] = $text;
		}

		return $messages;
	}

	/**
	 *
	 * @param unknown_type $items
	 * @return multitype:
	 */
	public function results($items)
	{
		$this->logger->debug(__METHOD__);
		$messages = array();

		foreach ($items as $race => $info) {
		    // Sort array by keys
		    ksort($info);
		    if( count($info) >= 6 ) {
		        $info = array_slice($info, 0, 6, true);
		    }
		    $text = IVR_Dictionary::tr('CARRERA_'.$race, IVR_Dictionary::TAXONOMY_RACE);

		    $pieces_order = array();
		    $pieces_diff  = array();
		    foreach ($info as $puesto => $spc_info) {
		        $puesto = ltrim($puesto,' 0');
		        array_push($pieces_order,
		            sprintf('%s el %s',
	            		IVR_Dictionary::tr(sprintf('PUESTO_%s', $puesto), IVR_Dictionary::TAXONOMY_STANDING),
		                ltrim($spc_info['orden'],' 0')
		            )
		        );
		        // Diferencias
		        if( !empty($spc_info['diferencia']) ){
		            $text_diff = sprintf(
	            		'distancia del %s al %s %s',
		            	IVR_Dictionary::tr(sprintf('PUESTO_%s', ($puesto)-1), IVR_Dictionary::TAXONOMY_STANDING),
	            		IVR_Dictionary::tr(sprintf('PUESTO_%s', $puesto), IVR_Dictionary::TAXONOMY_STANDING),
	            		IVR_Dictionary::tr(strtoupper($spc_info['diferencia']), IVR_Dictionary::TAXONOMY_DIFFERENCE)
            		);
		            array_push($pieces_diff, $text_diff);
		        }
		    }

		    $text.= ', '.implode(', ', $pieces_order).'.';
		    $text.= ' '.implode(', ', $pieces_diff).'.';
		    array_push($messages, $text);
		}

		return $messages;
	}

	/**
	 *
	 * @param unknown_type $type
	 * @param unknown_type $race
	 * @param unknown_type $messages
	 */
	public function files($type, $messages)
	{
		$this->logger->debug(__METHOD__);
		if (in_array($type, $this->config['types'])) {
			$file_path = $this->config[$type]['directory.src'];
			foreach ($messages as $race => $message) {
				$file = sprintf('%s/%s.txt', $file_path, $race);
				file_put_contents($file, $message);
			}
		} else {
			$this->logger->error(__METHOD__.' >> Trying to generate missing message type.');
		}
	}

	protected function _getMessage()
	{

	}
}

/**
 * @deprecated
 */
function IVR_generate_changed($IVR_changed)
{
	/* Cambios de monta de la primera carrera, jockey del numero # <nombre> */
	require_once(dirname(__FILE__).'/ivr.dictionary.php');
	$file_path = dirname(__FILE__).'/../tmp/ivr/cambios';
	if (count($IVR_changed)) {
		echo '<pre>Changed >> '.print_r($IVR_changed,1).'</pre>';
	}

	foreach( $IVR_changed as $race => $changed ){
		$text = sprintf('Cambios de monta de la %s:', IVR_tr('CARRERA_'.$race));
		$changes = array();
		foreach ($changed as $info){
			array_push(
				$changes,
				sprintf('jockey del numero %s, %s', ltrim($info['orden'], ' 0'), $info['jockey'])
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
	echo('<pre>Generating defaults for races:'.print_r($races, 1).'</pre>');
	$file_path_deleted = dirname(__FILE__).'/../tmp/ivr/borrados';
	$file_path_results = dirname(__FILE__).'/../tmp/ivr/resultados';
	$file_path_changed = dirname(__FILE__).'/../tmp/ivr/cambios';

	foreach( $races as $race_no => $race ){
		$file_deleted = sprintf('%s/%s.txt', $file_path_deleted, $race_no);
		file_put_contents($file_deleted, sprintf('Borrados de la %s, corren todos.', IVR_Dictionary::tr('CARRERA_'.$race_no, 'race')));

		$file_results = sprintf('%s/%s.txt', $file_path_results, $race_no);
		file_put_contents($file_results, sprintf('%s, sin efectuarse.', IVR_Dictionary::tr('CARRERA_'.$race_no, 'race')));

		$file_changed = sprintf('%s/%s.txt', $file_path_changed, $race_no);
		file_put_contents($file_changed, sprintf('%s, sin cambios.', IVR_Dictionary::tr('CARRERA_'.$race_no, 'race')));
	}

}

/**
 * Trim function wrapped to use in array_walk
 */
function IVR_array_ltrim($value)
{
	return ltrim($value, ' 0');
}
