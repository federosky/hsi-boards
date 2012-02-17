<?php
/**
 * 
 */
require_once(dirname(__FILE__).'/inc/config.inc.php');
require_once('HTTP/Request.php');

$delay = $config['server.scriptexecutiontime'] * 45;
$executionLimit = ($config['server.scriptexecutiontime'] * 60) + 5;
$request  = null;
$response = null;

// Set script max execution time 
set_time_limit( $executionLimit );

$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	$db_connected = true;
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.'."\n";
	exit($error_str);
}

mysql_select_db($db_data['db_name'],$dbh);
$sql = 'SELECT max(carrera)+1 as next FROM carreras WHERE fecha=(SELECT max(fecha) FROM carreras) AND disputada=1';
$rs = null;
if( !$rs = mysql_query($sql) ){
	$error_str = 'Error ('.mysql_errno($dbh).'): '.mysql_error($dbh)."\n";
	$error_str.= 'Executing "'.$sql.'"'."\n";
	echo $error_str;
}
$next = mysql_fetch_assoc($rs);
$next = $next['next'];
$url = $config['app.race.current-url'].'/'.$next;

// force request delay
sleep($delay);

$request = new HTTP_Request();
$request->setURL($url);
$response = $request->sendRequest();

echo $request->getResponseBody();