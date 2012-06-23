<?php
/**
 *
 */

require_once(dirname(__FILE__).'/../inc/ivr.config.inc.php');
require_once(dirname(__FILE__).'/../vendor/pear/HTTP/Request.php');

$actions = array('changed','deleted','results');
$update_mdb = array();

/**
 * Check wheter the IVR is enabled or not
 */
#if(!$ivrEnabled) exit();

foreach( $actions as $action ){
	$output = array();
	$update_mdb[$action] = array();
	
	$source_path      = $ivr_config[$action]['directory.src'];
	$destination_path = $ivr_config[$action]['directory.dst']; 
	$command = 'ls '.$source_path;
	exec($command, $output);
	
	if( !count($output) ){
		echo 'Nothing to do in '.$source_path."\n";
		continue;
	}
	
	foreach( $output as $file ){
		if( copy($source_path.'/'.$file, $destination_path.'/'.$file) ){
			echo 'File copied: '.$source_path.'/'.$file."\n";
			$pieces = array();
			$pieces = explode('.', $file);
			$race   = $pieces[0]; 
			array_push($update_mdb[$action], $race);
			unlink($source_path.'/'.$file);
		}
	}
	
	if( count($update_mdb[$action]) ){
		foreach( $update_mdb[$action] as $race ){
			$http = new HTTP_Request();
			// Request config
			$http->setMethod(HTTP_REQUEST_METHOD_GET);
			$http->addHeader('User-Agent', 'pizarras-hsi.org');
			$url = sprintf('%s', $ivr_config['ivr.uri']);
			$http->setUrl($url);
			$http->addQueryString('action', $action);
			$http->addQueryString('race', $race);
			// Send request
			$http->sendRequest();
			// Handle response
			echo 'Request sent to: "'.$url.'" >> '.$http->getResponseBody()."\n";
			sleep(5);
		}
	}
}
?>
