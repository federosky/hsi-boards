<?php
require_once('inc/config.inc.php');
require_once('HTTP/Request.php');

$http = new HTTP_Request();

/* Request config */
$http->setMethod(HTTP_REQUEST_METHOD_GET);
$http->addHeader('User-Agent', 'pizarras-hsi.org');
$http->setUrl('http://pizarras-hsi.local/services/race/current');

/* Send request */
$http->sendRequest();

/* Handle response */
header('X-Race-Number:'.$http->getResponseHeader('X-Race-Number'));
echo $http->getResponseBody();
