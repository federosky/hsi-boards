<?
/**
 * This should be place in the Windows shadow server
 * 
 * @author Fixa	<http://www.fixa.com.ar>
 * @package IVR
 */

if( empty($_REQUEST['action'])  || empty($_REQUEST['race']) ){
	echo 'Error: Invalid params.';
	exit(1);
}
	
$mdbFilePath = dirname(__FILE__).'/tmp/mdb/Carreras.mdb';

$action = $_REQUEST['action'];
$action = strtolower($action);
$actionClass = ucwords($action);

$validActions = array('deleted','results','changed');
if( !in_array($action, $validActions) ){
	echo 'Error: No valid action.';
	exit(1);
}

$race = trim($_REQUEST['race']);

require_once(dirname(__FILE__).'/lib/mdb/'.$actionClass.'.class.php');

$actionHandler = new $action($mdbFilePath);
if( $actionHandler->open() ){
	echo 'Updating..<hr/>';
	$actionHandler->setUpdateable($race);
	$actionHandler->close();
}
?>