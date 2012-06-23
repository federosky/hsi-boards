<?php
/**
 * 
 */
require_once(dirname(__FILE__).'/inc/config.inc.php');
require_once(dirname(__FILE__).'/inc/common.php');
require_once(dirname(__FILE__).'/inc/dictionary.php');

/**
 * DB Connection
 */
$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	$db_connected = true;
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.';
	exit($error_str);
}

// selecciono la base en la que voy a trabajar..
mysql_select_db($db_data['db_name'],$dbh);

// Numero de carrera
$carrera = '1';
if( !empty($_GET['race']) ) $carrera = $_GET['race'];

// Check if race was raced..
$sql_raced = 'SELECT carrera,disputada FROM carreras WHERE carrera='.$carrera.' AND fecha = (SELECT max(fecha) FROM carreras ) GROUP BY carrera;';
$rs = mysql_query($sql_raced);
$reg = mysql_fetch_assoc($rs);
if ($reg['disputada'])
{
	require_once(dirname(__FILE__).'/raced.php');
}
else
{
	require_once(dirname(__FILE__).'/detail.php');
}
