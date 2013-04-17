<?php
/**
 * Assumptions:
 * 	- DBF file only contains record for the current meeting (today)
 */
require_once(dirname(__FILE__).'/../inc/config.inc.php');
require_once(dirname(__FILE__).'/phpxbase/Table.class.php');
require_once(dirname(__FILE__).'/../inc/ivr.generators.php');

/**
 * Include Yii framework libraries
 */
// change the following paths if necessary
$yii       = $config['yii.location'].'/framework/yii.php';
$yiiConfig = $config['yii.location'].'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
$app = Yii::createWebApplication($yiiConfig);

$file = dirname(__FILE__).'/../tmp/dbf/detalle.dbf';
if( !file_exists($file) )
	exit('File "'.$file.'" not found. Aborting process.'."\n");
$start_time = microtime(1);

$dbf = new XBaseTable($file);
$dbf->open();

$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	$db_connected = true;
	mysql_select_db($db_data['db_name'],$dbh);
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.'."\n";
	exit($error_str);
}

$affected_rows = 0;
$ignore_race_no = 0;

$IVR_deleted = array();
$IVR_results = array();
$IVR_changed = array();
$IVR_default = array();

// Replicate data for other systems
$replicate = true;

// Search backwards for last record to get Date and RaceCount
$index = $dbf->getRecordCount();
while( $last = $dbf->moveTo($index) )
{
	// Break iterations when we've found the last not-empty row.
	if( !$last->isDeleted() && !empty($last->choppedData['FECHA']) && !empty($last->choppedData['CARRERA']) )
		break;
	$index--;
}
// Get date & RaceCount
$date = date('Y-m-d',strtotime($last->choppedData['FECHA']));
$raceCount = (int) $last->choppedData['CARRERA'];

// Go back to first record to process
$dbf->reset();

if (!$meeting = Meeting::model()->findByAttributes(array('date' => $date))) {
	$meeting = new Meeting();
	$data = array(
		'date' => $date,
		'racetrack' => 'SI',
		'race_count' => $raceCount
	);
	$meeting->attributes = $data;
	if( !$meeting->save(true) )
	{
		$replicate = false;
	}
}

while ($record = $dbf->nextRecord()) {
	if( $record->choppedData['FORFAIT'] ) continue;
	$record_data = array_change_key_case($record->choppedData,CASE_LOWER);
	$record_data['fecha']   = date('Y-m-d',strtotime($record_data['fecha']));
	$record_data['nacioel'] = date('Y-m-d', strtotime($record_data['nacioel']));
	$attrs = array('meeting_id' => $meeting->id, 'number' => $record_data['carrera']);
	if( $replicate && !$race = Race::model()->findByAttributes($attrs) )
	{
		$race = new Race();
		$data = array(
			'number'        => $record_data['carrera'],
			'meeting_id'    => $meeting->id,
			'title'         => $record_data['nombre'],
			'type'          => $record_data['tipo'],
			'lane'          => $record_data['pista'],
			'lane_state'    => $record_data['estado'],
			'distance'      => $record_data['distancia'],
			'time_enlapsed' => $record_data['tiempo']
		);
		$race->attributes = $data;
		$race->save(array_keys($data));
	}

	$saved_record = array();
	/******************/
	$check_sql = 'SELECT id,fecha,carrera,orden,puesto,difere,divipa,jockey,tiempo,disputada FROM carreras ';
	$check_sql.= ' WHERE fecha = \''.$record_data['fecha'].'\'';
	$check_sql.= ' AND carrera = '.$record_data['carrera'];
	$order = '\''.$record_data['orden'].'\'' ;
	$check_sql.= ' AND orden = '.$order;
	$check_sql.= ' LIMIT 1';

	$race_no = $record_data['carrera'];
	$rs = mysql_query($check_sql);
	/**/
	if( mysql_num_rows($rs) ){
		$saved_record = mysql_fetch_assoc($rs);
		if( !empty($saved_record['disputada']) ) continue;
		// IVR Results
		$puesto = $record_data['puesto'];
		$divipa = (int) $record_data['divipa'];
		if( !empty($puesto) && !empty($divipa) ){
			if( !array_key_exists($race_no,$IVR_results) ) $IVR_results[$race_no] = array();
			$rs = $record_data['puesto'];
			$IVR_results[$race_no][$rs] = array(
												'orden'      => $record_data['orden'],
												'diferencia' => $record_data['difere']
											);
		}
		// IVR Changed
		if( $record_data['jockey'] != $saved_record['jockey'] ){
			if( !array_key_exists($race_no,$IVR_changed) ) $IVR_changed[$race_no] = array();
			array_push($IVR_changed[$race_no], array(
													'orden'  => ltrim($record_data['orden'],' 0'),
													'jockey' => $record_data['jockey']
												)
			);
		}
		// Update existing entry
		update_record($dbh, $record_data, $saved_record['id']);
		$affected_rows++;
		if( $replicate ) //&& ($record_data['nombre'] != $race->title || $record_data['tipo'] != $race->type) )
		{
			$data = array(
				'title'         => $record_data['nombre'],
				'type'          => $record_data['tipo'],
				'lane'          => $record_data['pista'],
				'lane_state'    => $record_data['estado'],
				'distance'      => $record_data['distancia'],
				'time_enlapsed' => $record_data['tiempo']
			);
			$race->attributes = $data;
			$race->save(true);
		}
	}
		else{
			insert_record($dbh, $record_data);
			if( !array_key_exists($race_no,$IVR_default) ) $IVR_default[$race_no] = array();
			$affected_rows++;
		}
	// IVR Deleted
	if( preg_match('/[A-Z]{2}/',$record_data['puesto']) ){
		if( !array_key_exists($race_no,$IVR_deleted) ) $IVR_deleted[$race_no] = array();
		array_push($IVR_deleted[$race_no], $record_data['orden']);
	}
}

IVR_generate_race_defaults($IVR_default);
IVR_generate_deleted($IVR_deleted);
IVR_generate_results($IVR_results);
IVR_generate_changed($IVR_changed);

$end_time = microtime(1);
echo 'Affected rows: '.$affected_rows.' in '.(substr($end_time - $start_time,0,6)).' seconds.'."\n";
$dbf->close();

/**
 * Updated an existing race record
 *
 * @param DB resource $dbh
 * @param Array $data
 * @param Integer $record_id
 */
function update_record( $dbh, $data, $record_id ){
	$fields2update = array(
		'tiempo','puesto','difere','divipa','kiloscab','kilos','kilosrea','herraje','jockey','edadeje',
		'tratamient', 'estado'
	);
	$sql = 'UPDATE carreras SET ';
	$fields2join = array();
	foreach( $fields2update as $field ){
		$value = ltrim($data[$field], ' 0');
		if( is_string($value) ) $value = '\''.$value.'\'';
		$value = $field.'='.$value;
		array_push($fields2join, $value);
	}
	array_push($fields2join, 'modificado = now()');
	$sql.= implode(', ', $fields2join);
	$sql.= ' WHERE id = '.$record_id;
	if( mysql_query($sql, $dbh) ){
		echo 'Row successfuly updated.'."\n";
	}
		else{
			$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
			$error_str.= 'Executing "'.$sql.'"'."\n";
			echo $error_str;
		}
}

function insert_record($dbh, $data){
	$values = array();
	foreach( $data as $key => $value )
	{
		if( !is_valid_column($key) )
		{
			unset($data[$key]);
			continue;
		}
		$value = '\''.mysql_real_escape_string($value,$dbh).'\'';
		array_push($values, $value);
	}
	$sql = '';
	$sql.= 'INSERT INTO carreras (';
	$sql.= implode(',',array_keys($data));
	$sql.= ') VALUES (';
	$sql.= implode(',',$values);
	$sql.= ');';

	if( mysql_query($sql, $dbh) ){
		echo 'Row successfuly inserted.'."\n";
	}
		else{
			$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
			$error_str.= 'Executing "'.$sql.'"'."\n";
			echo $error_str;
		}
}

/**
 *
 */
function is_valid_column($column = '')
{
	$validColumns = get_racedetail_columns();
	return in_array($column, $validColumns);
}
/**
 * Return available columns to insert into / update
 */
function get_racedetail_columns()
{
	return array(
		'hipodromo',
		'fecha',
		'carrera','nombre','tipo','pista','estado','distancia','tiempo',
		'orden','forfait','puesto','difere','divipa','kiloscab','kilos','kilosrea','tratamient','herraje','ejemplar',
		'edadeje', 'caballer','cuidador',
		'jockey','categojoc',
		'capataz','peon','sereno','nuejem','nucaba','nucuid','nujock','nucapa','nupeon','nusere','modificado',
		'disputada','horario',
		'edaddesde', 'edadhasta', 'sexo', 'sexocab', 'ganadasdes', 'ganadashas', 'condicion', 'condicion2', 'apuestas',
		'tierecord', 'totalprem1', 'totalprem2', 'totalprem3', 'totalprem4', 'totalprem5', 'premiotota',
		'ultimas', 'pelo', 'padre', 'madre', 'abuelo', 'criador', 'nacioel'
	);
}
?>
