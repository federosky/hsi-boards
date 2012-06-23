<?php
/**
 * 
 */
require_once('inc/config.inc.php');
require_once('HTTP/Request.php');

if( !empty($_REQUEST['screen']) ){
	$screen_no = $_REQUEST['screen'];
}

$http = new HTTP_Request();

/* Request config */
$http->setMethod(HTTP_REQUEST_METHOD_GET);
$http->addHeader('User-Agent', 'pizarras-hsi.org');
$http->setUrl('http://127.0.0.1/pizarras/next_race.php');
if( !empty($screen_no) ){
	$http->addQueryString('screen', $screen_no);
	$http->addHeader('X-Screen-Number', $screen_no);
}
/* Send request */
$http->sendRequest();

/* Handle response */
header('X-Race-Number:'.$http->getResponseHeader('X-Race-Number'));
header('X-Rider-Count:'.$http->getResponseHeader('X-Rider-Count'));
echo $http->getResponseBody();

//sleep(2);