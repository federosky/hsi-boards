<?php
/**
 * Especific Config for Production Environment
 *
 * @package    Config
 * @subpackage Environments
 */

/**
 * @global Application configuration
 */
$app->logger->level(LOGGER_ERROR|LOGGER_INFO|LOGGER_WARNING);

/**
 * Database Access
 */
$db = array(
	'dbms'     => 'mysql',
	'host'     => 'localhost',
	'db'       => 'hipodromo',
	'username' => 'carreras',
	'password' => 'piernodoyuna',
	'table'    => ''
);

/**
 * App. root url
 */
$context->param('app.host', 'http://localhost/pizarras');
$context->param('app.root.url',    '/');

$context->param('app.db.resource', $db);
