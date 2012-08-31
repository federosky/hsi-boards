<?php
/**
 * Assumptions:
 * 	- DBF file only contains record for the current meeting (today)
 */
require_once(dirname(__FILE__).'/../inc/config.inc.php');
require_once(dirname(__FILE__).'/phpxbase/Table.class.php');
require_once('HTTP/Request.php');

/**
 * Include Yii framework libraries
 */
// change the following paths if necessary
$yii=dirname(__FILE__).'/../../hsi-queries/framework/yii.php';
$yiiMainConfig=dirname(__FILE__).'/../../hsi-queries/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
$app = Yii::createWebApplication($yiiMainConfig);

$file = $config['app.dbf.localfolder'].'detadiv.dbf';

if( !file_exists($file) )
	exit('File "'.$file.'" not found. Aborting process.'."\n");
$start_time = microtime(1);

$dbf = new XBaseTable($file);
$dbf->open();

$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	$db_connected = true;
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.'."\n";
	exit($error_str);
}

$replicate = true;
// Search backwards for last record to get Date
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
// Go back to first record to process
$dbf->reset();

if( !$meeting = Meeting::model()->findByAttributes(array('date' => $date)) )
{
	$replicate = false;
}

mysql_select_db($db_data['db_name'],$dbh);
//mysql_query('SELECT GET_LOCK(\'hipodromo\',50)');
$affected_rows = 0;
$races_processed = array();
while ($record = $dbf->nextRecord()) {
	$record_data = $record->choppedData;
	$record_data = array_change_key_case($record->choppedData,CASE_LOWER);
	if( empty($record_data['carrera']) || empty($record_data['nombre']) || empty($record_data['ordenes']) ){
		continue;
	}
	$race_number = $record_data['carrera'];
	$race_date   = $record_data['fecha'];
	$sql = 'SELECT * FROM dividendos';
	$sql.= ' WHERE carrera = '.$record_data['carrera'].' AND fecha = "'.$record_data['fecha'].'"';
	$sql.= ' AND nombre LIKE \'%'.$record_data['nombre'].'%\'';
	$sql.= ' AND ordenes = \''.$record_data['ordenes'].'\'';
	//echo 'Find: '.$sql."\n";
	$rs = mysql_query($sql, $dbh);

	if( mysql_num_rows($rs) ){
		$saved_record = mysql_fetch_assoc($rs);
		if( ($saved_record['ordenes'] != $record_data['ordenes']) || ($saved_record['importe'] != $record_data['importe']) ){
			update_record($dbh, $record_data, $saved_record['id']);
			$affected_rows++;
		}
	}
	else{
		insert_record($dbh, $record_data);
		$affected_rows++;
	}

	if( !in_array($record_data['carrera'],$races_processed) )
		array_push($races_processed,$record_data['carrera']);

	mysql_free_result($rs);
}

/**
 * Update carreras.disputada
 */
if( count($races_processed) ){
	$sql = 'UPDATE carreras SET disputada = 1 WHERE carrera IN ('.implode(',',$races_processed).') AND fecha = \''.$race_date.'\';';
	if( !mysql_query($sql) ){
		$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
		$error_str.= 'Executing "'.$sql.'"'."\n";
		echo $error_str;
	}
}
if( $replicate )
{
	foreach( $races_processed as $number )
	{
		$attrs = array('meeting_id' => $meeting->id, 'number' => $number); 
		if( $race = Race::model()->findByAttributes($attrs) )
		{
			$race->raced = 1;
			$race->save();			
		}
	}	
}

$end_time = microtime(1);
echo 'Affected rows: '.$affected_rows.' in '.(substr($end_time - $start_time,0,6)).' seconds.'."\n";
$dbf->close();

/**
 * 
 * Enter description here ...
 * @param $dbh
 * @param $data
 */
function insert_record($dbh, $data){
	$values = array();
	foreach( $data as $key => $value ){
		$value = '\''.mysql_real_escape_string($value,$dbh).'\'';
		array_push($values, $value);
	}
	$sql = '';
	$sql.= 'INSERT INTO dividendos (';
	$sql.= implode(',',array_keys($data));
	$sql.= ') VALUES (';
	$sql.= implode(',',$values);
	$sql.= ');';
	//echo 'SQL: '.$sql."\n";
	if( mysql_query($sql, $dbh) ){
		echo "\t".'Row successfuly inserted.'."\n";
	}
		else{
			$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
			$error_str.= 'Executing "'.$sql.'"'."\n";
			echo $error_str;
		}
}

function update_record( $dbh, $data, $record_id ){
	$fields2update = array('ordenes','importe');
	$sql = 'UPDATE dividendos SET ';
	$fields2join = array();
	foreach( $fields2update as $field ){
		if( !empty($data[$field]) ){
			$value = $data[$field];
			if( is_string($value) ) $value = '\''.$value.'\'';
			$value = $field.'='.$value;
			array_push($fields2join, $value);
		}
	}
	array_push($fields2join, 'modificado = now()');
	$sql.= implode(', ', $fields2join);
	$sql.= ' WHERE id = '.$record_id;
	//echo 'SQL: '.$sql."\n";
	if( mysql_query($sql, $dbh) ){
		echo "\t".'Row successfuly updated.'."\n";
	}
		else{
			$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
			$error_str.= 'Executing "'.$sql.'"'."\n";
			echo $error_str;
		}
}

?>
