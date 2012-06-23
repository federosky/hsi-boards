<?php
/**
 * @param String screen
 */
session_id('hipodromo-hsi');
session_save_path('/tmp');
session_start();

require_once(dirname(__FILE__).'/inc/config.inc.php');
require_once(dirname(__FILE__).'/inc/common.php');
require_once(dirname(__FILE__).'/inc/dictionary.php');

/**
 * Conexi�n a la base..
 */
$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	// selecciono la base en la que voy a trabajar..
	mysql_select_db($db_data['db_name'],$dbh);
	$db_connected = true;
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.';
	exit($error_str);
}

/**
 * Race selection
 */
$sql = 'SELECT fecha,count(DISTINCT carrera) as total_races ' .
		'FROM carreras WHERE fecha = (SELECT MAX(fecha) FROM carreras) ' .
		'GROUP BY fecha';
$rs = mysql_query($sql);
$regs = mysql_fetch_assoc($rs);

$today = date('Y-m-d');
$update_session = false;
#print_r($_SESSION);
if (empty($_SESSION['screens_set'])) $update_session = true;
	elseif ($today != $_SESSION['date'] || $regs['fecha'] != $_SESSION['rs_date']) $update_session = true;

if( $update_session ){
	$_SESSION['count']   = $regs['total_races'];
	$_SESSION['date']    = $today;
	$_SESSION['rs_date'] = $regs['fecha']; 

	$screens = screens_values(4, $_SESSION['count']);
	$_SESSION['screens'] = $screens;
}

// Numero de carrera
$screen_no = substr($_SERVER['REMOTE_ADDR'],-1,1);
if( !empty($_REQUEST['screen']) && is_numeric($_REQUEST['screen']) ){
	$screen_no = $_REQUEST['screen'];
}

//$screen = $_SESSION['screens'][$term_no];

$race = get_term_current_race($screen_no);

/**
 * Get total count riders
 */
$sql = 'SELECT count( DISTINCT orden ) as rider_count FROM carreras';
$sql.= ' WHERE fecha = (SELECT max(fecha) FROM carreras) AND carrera = '.$race;

$rs = mysql_query($sql);
$reg = mysql_fetch_assoc($rs);
$rider_count = $reg['rider_count'];

header('X-Term-Number:'.$screen_no);
header('X-Race-Number:'.$race);
header('X-Rider-Count:'.$rider_count);
echo $race;
?>
