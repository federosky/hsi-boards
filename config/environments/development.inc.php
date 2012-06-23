<?php
/**
 * Especific Config for development Environment
 *
 * @package    Config
 * @subpackage Environments
 */

/**
 * @global Application configuration
 */
$app->logger->level(LOGGER_INFO|LOGGER_ERROR|LOGGER_WARNING);

/**
 * Database Access
 */
$db = array(
	'dbms'     => 'mysql',
	'host'     => 'localhost',
	'db'       => 'hipodromo_development',
	'username' => 'carreras',
	'password' => 'piernodoyuna',
	'table'    => ''
);

/**
 * Session Config
 */
$sessionConfig = array(
					'id'        => 'hipodromo-hsi',
					'name'      => 'pizarras',
					'save_path' => '/tmp'
				);

/**
 * App. root url
 */
$context->param('app.root.url',    '/');

$context->param('app.db.resource', $db);

/**
 * Session
 */
$context->param('app.session.config', $sessionConfig);
