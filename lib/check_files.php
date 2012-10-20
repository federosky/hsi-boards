<?php
/**
 * 
 */
require_once(dirname(__FILE__).'/../inc/config.inc.php');
require_once('logger.class.php');

$logger = Logger::instance('Syslog', 'hsi-boards', LOGGER_ALL);
$logger->debug(sprintf('App::CheckFiles Start process'));

$source_base_path      = $config['app.dbf.sharedfolder'];
$destination_base_path = $config['app.dbf.localfolder'];

$source_file      = '';
$destination_file = '';

$process_action  = '';
$process_details = dirname(__FILE__).'/process_details.php';
$process_results = dirname(__FILE__).'/process_results.php';
$shutdown        = dirname(__FILE__).'/shutdown.php';

$output = array();
$command = 'ls '.$source_base_path;
exec($command, $output);

/**
 * Ordeno en forma descendente el listado, para que se procese primero detalle.dbf
 */
arsort($output);

if( !count($output) ) exit("Nothing to do..\n");

foreach( $output as $file ){
	echo 'File: '.$file."\n";
	$source_file      = $source_base_path.$file;
	$destination_file = $destination_base_path.strtolower($file);
	if( !checkFileExtension($file) ){
		continue;
	}
	switch( strtolower($file) ){
		case 'detalle.dbf':
			$action = 'process_details'; break;
		case 'detadiv.dbf':
			$action = 'process_results'; break;
		case 'halt.cmd':
			$action = 'shutdown'; break;
		default:
			echo(sprintf('No valid data file %s \n.', $file));
			continue;
	}

	echo 'Copying '.$source_file.' to '.$destination_file.'..'."\n";
	copy($source_file, $destination_file);
	echo 'Deleting '.$source_file.'..'."\n";
	unlink($source_file);
	echo 'Processing..'."\n";
	$process_action = '/usr/bin/php '.$$action;
	exec($process_action, $process_output);
	echo print_r($process_output,1)."\n";
	// Force delay..
	sleep(3);
}

/**
 * Verify file extension
 *  
 * @param string $file_name
 */
function checkFileExtension( $file_name )
{
	$validExtensions = array('dbf','cmd');
	$file_name = strtolower($file_name);

	if( !empty($file_name) ){
		foreach($validExtensions as $extension ){
			$fileExtension = strtolower(substr($file_name,-3,3));
			if( !strcmp($fileExtension, $extension) ) return true;
		}
	}
	echo 'Invalid file extension.'."\n";
	return false;
}
?>
