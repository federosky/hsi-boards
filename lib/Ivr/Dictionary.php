<?php
/**
 * IVR Dictionary representation class
 *
 * @package IVR
 * @subpackage IVR_Dictionary
 */
class IVR_Dictionary
{
	//
	const TAXONOMY_RACE       = 'race';
	const TAXONOMY_STANDING   = 'standing';
	const TAXONOMY_DIFFERENCE = 'difference';
	
	/**
	 * Race number label
	 * @var array
	 */
	static public $race = array(
		'CARRERA_1' => 'Primera carrera',
		'CARRERA_2' => 'Segunda carrera',
		'CARRERA_3' => 'Tercer carrera',
		'CARRERA_4' => 'Cuarta carrera',
		'CARRERA_5' => 'Quinta carrera',
		'CARRERA_6' => 'Sexta carrera',
		'CARRERA_7' => 'Septima carrera',
		'CARRERA_8' => 'Octava carrera',
		'CARRERA_9' => 'Novena carrera',
		'CARRERA_10' => 'Decima carrera',
		'CARRERA_11' => 'Decimo primera carrera',
		'CARRERA_12' => 'Decimo segunda carrera',
		'CARRERA_13' => 'Decimo tercera carrera',
		'CARRERA_14' => 'Decimo cuarta carrera',
		'CARRERA_15' => 'Decimo quinta carrera',
		'CARRERA_16' => 'Decimo sexta carrera',
		'CARRERA_17' => 'Decimo septima carrera',
		'CARRERA_18' => 'Decimo octava carrera',
		'CARRERA_19' => 'Decimo novena carrera',
		'CARRERA_20' => 'Vigesima carrera',
		'CARRERA_21' => 'Vigesima primera carrera',
		'CARRERA_22' => 'Vigesima segunda carrera',
	);
	/**
	 * Horse finish standing
	 * @var array
	 */
	static public $standing = array(
		'PUESTO_1' => 'primero',
		'PUESTO_2' => 'segundo',
		'PUESTO_3' => 'tercero',
		'PUESTO_4' => 'cuarto',
		'PUESTO_5' => 'quinto',
		'PUESTO_6' => 'sexto',
		'PUESTO_7' => 'septimo',
		'PUESTO_8' => 'octavo',
		'PUESTO_9' => 'noveno',
		'PUESTO_10' => 'decimo',
		'PUESTO_11' => 'decimo primero',
		'PUESTO_12' => 'decimo segundo',
		'PUESTO_13' => 'decimo tercero',
		'PUESTO_14' => 'decimo cuarto',
		'PUESTO_15' => 'decimo quinto',
		'PUESTO_16' => 'decimo sexto',
		'PUESTO_17' => 'decimo septimo',
		'PUESTO_18' => 'decimo octavo',
		'PUESTO_19' => 'decimo noveno',
		'PUESTO_20' => 'vigesimo'
	);
	/**
	 * Difference betwen horses
	 * @var array
	 */
	static $difference = array(
		'VM'     => 'ventaja minima',
		'MIN.'   => 'ventaja minima',
		'HOCICO' => 'hocico',
		'1/2CZA' => 'media cabeza',
		'CABEZA' => 'cabeza',
		'1/2PZO' => 'medio pescuezo',
		'PZO'    => 'pescuezo',
		'1/2C'   => 'medio cuerpos',
		'1/2CPO' => 'medio cuerpo',
		'3/4CPO' => 'tres cuartos cuerpo',
		'CPOS'   => 'cuerpos',
		'CZA'    => 'cabeza',
		'CPO'    => 'cuerpo',
		'COS'    => 'cuerpos'
	);
	
	/**
	 * Translate given label into a Synthesizer readable text
	 *
	 * @param string $text
	 * @param string $subject
	 *
	 * @return string
	 */
	static public function tr($text, $taxonomy)
	{
		if (!property_exists(__CLASS__, $taxonomy)) {
			throw new \Excpetion('Property does not exists in class ' . __CLASS__ . '::$' . $taxonomy);
		}
		switch ($taxonomy) {
			case self::TAXONOMY_RACE:
				if (!empty(self::$race[$text])) {
					$text = self::$race[$text];
				}
				break;
			case self::TAXONOMY_STANDING:
			    if (!empty(self::$standing[$text])) {
			        $text = self::$standing[$text];
			    }
			    break;
			case self::TAXONOMY_DIFFERENCE:
				if (strpos($text, ' ')) {
					$pieces = explode(' ', $text);
					if( count($pieces) > 1 && !empty(self::$difference[$pieces[1]])){
					    $text = $pieces[0].' '.@constant($pieces[1]);
					}
				}
				if (!empty(self::$difference[$text])) {
					
				}
				break;
			default:
				break;
		}
			    
	    return $text;
	}
}
