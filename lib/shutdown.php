<?php
/**
 * Shutdown callback handler
 * 
 * Start SHUTDOWN process 
 */
require_once('inc/config.inc.php');
require_once('HTTP/Request.php');
require_once('logger.class.php');

$logger = Logger::instance('Syslog', 'hsi-boards', LOGGER_ALL);
$http = new HTTP_Request();

/* Request config */
$http->setMethod(HTTP_REQUEST_METHOD_GET);
$http->addHeader('User-Agent', 'pizarras-hsi.org');
//$http->addHeader('X-Race-Number', $race_no);
$http->setUrl('http://localhost/pizarras/services/command/power');

$logger->info('Shutting down the system.');

/* Send request */
$http->sendRequest();
/* Handle response */
echo $http->getResponseBody();
