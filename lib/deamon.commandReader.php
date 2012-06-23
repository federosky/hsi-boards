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
				//'micro-server' => 'http://localhost/pizarras/commands.response.php',
				'micro-server' => 'http://192.168.2.33:1000/',
				'server'       => 'http://localhost/pizarras/services',
				'request-timeout' => '2',
				'delay'  => 1.2 // time in seconds to sleep before next cycle
			);
$actions = array(
				'FWD' => '/race/forward',
				'RWD' => '/race/rewind',
				'END' => '/race/finish',
				'RUN' => '/race/start',
				'PWR' => '/command/power'
			);
			
$lap = 0;
$requestConfig = array(
					'timeout' => 0.5
				);
$request = new HTTP_Request('', $requestConfig);

while(1)
{
	$requestStatus = null;
	$request->setURL($config['micro-server']);
	$requestStatus = $request->sendRequest();
	if( !is_bool($requestStatus) )
	{
	/*	echo('HTTP Request error:');
		print_r($requestStatus);*/
		$lap++;
		continue;
	}
	
	$responseCode = $request->getResponseCode();
	$responseBody = trim($request->getResponseBody());
	if( $responseCode != '200' )
	{
		//echo('Error: '.$responseCode."\n");
	}
	else
	{
		//echo('Run command '.$responseBody."\n");
		$request->setURL($config['server'].$actions[$responseBody]);
		$requestStatus = $request->sendRequest();
	}
	
	$lap++;
//	if( $lap == 15 ) break;
	usleep($config['delay'] * 1000000);
}
