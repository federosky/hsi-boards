<?php
/**
 * 
 */
require_once('inc/config.inc.php');
require_once('HTTP/Request.php');

$race_no = $_REQUEST['race'];
if( !empty($_REQUEST['term']) ){
	$term_no = $_REQUEST['term'];
}

$http = new HTTP_Request();

/* Request config */
$http->setMethod(HTTP_REQUEST_METHOD_GET);
$http->addHeader('User-Agent', 'pizarras-hsi.org');
$http->addHeader('X-Race-Number', $race_no);
$http->setUrl($config['app.root.url'].'/');
$http->addQueryString('race', $race_no);
if( !empty($term_no) ) $http->addQueryString('term', $term_no);
/* Send request */
$http->sendRequest();

/* Handle response */
echo $http->getResponseBody();

sleep(2);
?>