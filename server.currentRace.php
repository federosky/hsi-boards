<?php
/**
 * Reeds the current race number to display on the Fifth term
 * from Server Session
 */

require_once(dirname(__FILE__).'/lib/session.class.php');

$session = new Session();
//$session->open();
echo '<pre>'.print_r($session,1).'</pre>';
//echo '<hr/>';
$updateValue = (!empty($_REQUEST['current']))? $_REQUEST['current'] : null;

echo $session->param('current.race', $updateValue);
