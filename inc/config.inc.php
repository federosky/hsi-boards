<?php
/**
 * Database connection info
 */
$config = array();
$file_dirname = dirname(__FILE__);

/**
 * Delay para el cambio del quinto monitor
 * en minutos
 */
$config['server.scriptexecutiontime'] = 1;

$host = 'localhost';
$db_data = array(
			'host'     => $host,
			'username' => 'carreras',
			'password' => 'piernodoyuna',
			'db_name'  => 'hipodromo'
		);
/**
 * app.rootpath Ubicacion de la aplicacion
 */
$config['app.rootpath'] = dirname($file_dirname) .'/';

/**
 * Shared folder where to drop files
 */
$config['app.dbf.sharedfolder'] = '/tmp/dbf/';
$config['app.dbf.localfolder']  = $config['app.rootpath'].'tmp/dbf/';

/**
 * Forward current race delay in minutes.
 */
$config['app.race.current.delay'] = 1;
$config['app.race.forward-url'] = 'http://localhost/pizarras/services/race/forward';
$config['app.race.current-url'] = 'http://pizarras-hsi.local/services/race/current';

/**
 * Config INCLUDEPATH
 */
$config['app.includepaths'] = array('inc/','lib/','vendor/pear/');
$strPath = $config['app.rootpath'].join($config['app.includepaths'], PATH_SEPARATOR.$config['app.rootpath']);
ini_set('include_path', $strPath.PATH_SEPARATOR.ini_get('include_path'));
