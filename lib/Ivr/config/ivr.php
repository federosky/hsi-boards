<?php
/**
 * base_path.src -> app.rootpath + 'tmp/ivr' + (borrados|cambios|resultados)
 * base_path.dst ->
 *
 * directory.src
 * directory.dst
 */

// Load General app config
$config = require_once(dirname(__FILE__).'/../../../config/main.php');

$ivr_config = array();

/**
 * IVR files path
 */
$ivr_config['base_path.src'] = $config['app.rootpath'].'tmp/ivr';

/**
 * smbfs shared directory
 * To use with 'autofs'
 * 		auto.master: /smb.share		/etc/auto.hsi
 * 		auto.hsi:	hsi-ivr		-fstype=smbfs,guest,uid=1000,gid=1000   ://192.168.1.161/hipo
 */
//$ivr_config['smb.shared'] = '/media/samba/gintonic/tmp';
$ivr_config['smb.shared'] = '/smb.share/hsi-ivr';

$ivr_config['types'] = array('default', 'deleted', 'changed', 'results');

/**
 * ivr.uri
 */
//$ivr_config['ivr.uri'] = 'http://localhost/pizarras/lib/mdb/ivr.update.php';
$ivr_config['ivr.uri'] = 'http://192.168.1.163/hsi-ivr/ivr.update.php';
/**
 * Directory for generated files
 */
$ivr_config['deleted'] = array(
	'directory.src' => $ivr_config['base_path.src'].'/borrados',
	'directory.dst' => $ivr_config['smb.shared'].'/borrados'
);

$ivr_config['changed'] = array(
	'directory.src' => $ivr_config['base_path.src'].'/cambios',
	'directory.dst' => $ivr_config['smb.shared'].'/cambios'
);

$ivr_config['results'] = array(
	'directory.src' => $ivr_config['base_path.src'].'/resultados',
	'directory.dst' => $ivr_config['smb.shared'].'/resultados'
);

return $ivr_config;
