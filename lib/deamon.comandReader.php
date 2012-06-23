<?php
/**
 * Posible responses
 * <code>
 * 	HTTP/1.1 200 OK
 *  Content-Length: 3
 *  
 *  FWD|RWD|PWR|RUN|END
 * </code>
 */
require_once(dirname(__FILE__).'/../inc/config.inc.php');
require_once(dirname(__FILE__).'/../vendor/pear/HTTP/Request.php');
$config = array(
				'micro-server' => 'http://localhost/pizarras/commands.response.php',
				'server'       => 'http://pizarras-hsi.local/services',
				'delay'  => 3 // time in seconds to sleep before next cycle
			);
$actions = array(
				'FWD' => '/race/forward',
				'RWD' => '/race/rewind',
				'END' => '/race/finish',
				'RUN' => '/race/start',
				'PWR' => '/command/power'
			);
			
$lap = 0;
$request = new HTTP_Request();

while(1)
{
	echo 'Lap no. '.$lap."\n";
	$requestStatus = null;
	$request->setURL($config['micro-server']);
	$requestStatus = $request->sendRequest();
	if( !is_bool($requestStatus) )
	{
		print_r($requestStatus);
		continue;
	}
	
	$responseCode = $request->getResponseCode();
	$responseBody = trim($request->getResponseBody());
	if( $responseCode != '200' )
	{
		echo('Error: '.$responseCode."\n");
	}
	else
	{
		echo('Run command '.$responseBody."\n");
		$request->setURL($config['server'].$actions[$responseBody]);
		$requestStatus = $request->sendRequest();
	}
	
	$lap++;
	if( $lap == 10 ) break;
	usleep($config['delay'] * 1000000);
}