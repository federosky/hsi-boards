<?php
/**
 * 
 * Enter description here ...
 * @param unknown_type $str
 * @param unknown_type $key
 */
function empty2str( $str, $key )
{
	$str = trim($str);
	if( empty( $str ) ) $str = '\'\'';
	return $str;
}

/**
 *
 */
function screens_values($screens, $races){

	$_SESSION['screens_set'] = true;
	$amount = $screens;
	$screens = array();
	$remaining_races   = $races;
	$remaining_screens = $amount;

	for( $i=1; $i <= $amount; $i++ ){
		if( $races <= 4 ){
			$screens[$i] = array('prev' => 0, 'max' => $races, 'offset' => 1);
			continue;
		}
		$max_races = round(($remaining_races / $remaining_screens));
		$screens[$i] = array(
							'prev' => 0,
							'max' => $max_races,
							'offset' => ($races - $remaining_races + 1)
						);
		$remaining_races -= $max_races;
		$remaining_screens--;
	}

	return $screens;
}

/**
 * Returns number of race to be display
 */
function get_term_current_race($screen_no){

	$screen = $_SESSION['screens'][$screen_no];

	$prev   = $screen['prev'];
	$max    = $screen['max'];
	$offset = $screen['offset'];

	$max_races    = ($offset + $max) - 1;
	$current_race = $offset + $prev;

	if( $current_race < $max_races ) $screen['prev'] = ++$prev;
		else $screen['prev'] = 0;

	$_SESSION['screens'][$screen_no] = $screen;

	return $current_race;
}

/**
 * Text wrap
 */
function text_wrap($text, $length){
	$wrapped = $text;
	if( strlen($text) > $length ){
		$wrapped = substr($text,0,$length);
		$wrapped = trim($wrapped).'..';
	}
	return $wrapped;
}
function jockey_name_wrap($text){
	$parts = array();
	$parts = explode(' ',$text);

	$formatted_name = '';
	$formatted_name.= $parts[0];
	$formatted_name.= ' '.substr($parts[1],0,1).'.';

	return $formatted_name;
}

function jockey_weight_wrap( $jockey_kg){
	if( strlen($jockey_kg) > 2 ){
		$jockey_kg = substr($jockey_kg,0,2);
		$html = '<span style="float:left;">';
		$html.= $jockey_kg;
		$html.= '</span>';
		$html.= '<span style="float:left;padding-top:3px;font-size:20px;font-weight:bold;">&frac12</span>';
		$jockey_kg = $html;
	}
	return $jockey_kg;
}

function format_race_time($race_time){
	$formated_race_time = 0;
	if( !empty($race_time) ){
		$formated_race_time = '';
		$part3 = substr($race_time,-3);
		$part2 = substr($race_time,-5,2);
		$part1 = substr($race_time,0,-5);

		$formated_race_time = $part1.'\''.$part2.'\'\''.$part3;
	}

	return $formated_race_time;
}
?>
