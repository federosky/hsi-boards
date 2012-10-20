<?php
/**
 * Setup Context / Dispatcher
 *
 * @package    Config
 * @subpackage Environments
 * @filesource
 */

/**
 *
 */
$file_dirname = dirname(__FILE__);
require_once($file_dirname.'/../lib/object.class.php');
require_once($file_dirname.'/../lib/logger.class.php');
require_once($file_dirname.'/../lib/context.class.php');
require_once($file_dirname.'/../lib/dispatcher.class.php');
require_once($file_dirname.'/../lib/template.class.php');

global $context, $app;
$logger  = Logger::instance('Syslog', 'hsi-boards', LOGGER_ALL);
$context = new Context();
$app     = new Dispatcher();

/**
 * @global string app.environment Contexto en el que corre la aplicacion
 */
$context->param('app.environment', (
	isset($_ENV['APP_ENVIRONMENT']) ?
		$_ENV['APP_ENVIRONMENT'] :
		(isset($_SERVER['APP_ENVIRONMENT']) ?
			$_SERVER['APP_ENVIRONMENT'] :
			'production'
		)
	)
);

/**
 * @global string app.rootpath Ubicacion de la aplicacion
 */
$app_rootpath = $context->param('app.rootpath',    dirname($file_dirname) .'/');
$context->param('app.root.url',    '/');

/**
 * @global string app.tmppath Ubicacion de los archivos temporales
 */
$context->param('app.tmppath', $app_rootpath.'tmp/');

/**
 * @global string app.message path Ubicacion de los mensajes
 */
$context->param('app.messagepath', $context->param('app.tmppath').'messages/');


/**
 * @global string app.queuepath Ubicacion de las queues
 */
$context->param('app.queuepath', $context->param('app.tmppath').'queue/');

/**
 * @global string app.sessionpath Ubicacion de las sesiones
 */
//$context->param('app.session.savepath', $app_rootpath.'tmp/sessions/');
$context->param('app.session.savepath', '/tmp');

/**
 * @global string app.uploadpath Ubicacion donde se realizan Uploads
 */
$context->param('app.uploadpath', $app_rootpath.'var/upload/');

/**
 * @global string app.configpath Ubicacion de las configuraciones
 */
$context->param('app.configpath', $app_rootpath.'config/');

/**
 * @global string app.includepaths Ubicaciones adicionales de inclusion.
 */
$context->param('app.includepaths', array('app/controllers/', 'lib/', 'vendor/', 'vendor/pear', '', 'app/'));

/**
 * Views default config
 */
$context->param('app.view.template.dir',    $app_rootpath.'app/views');
$context->param('app.view.template.format', 'tpl');
$context->param('app.view.template.layout', '');

$context->param('app.static.url', $context->param('app.root.url').'public');

/**
 * @global array app.plugins Plugins habilitados
 */
$context->param('app.plugins', array());

/**
 * Carga la configuracion particular para el ambiente
 * en el que se ejecuta (production/development/etc)
 */
require_once($file_dirname.'/environments/'.$context->param('app.environment').'.inc.php');

/**
 * Config Sessions
 */
$context->session->setup($context->param('app.session.config'));

/**
 * Config INCLUDEPATH
 */
$strPath = $context->param('app.rootpath').join($context->param('app.includepaths'), PATH_SEPARATOR.$context->param('app.rootpath'));
ini_set('include_path', $strPath.PATH_SEPARATOR.ini_get('include_path'));


/**
 * Config PLUGINS
 */
foreach($context->param('app.plugins') as $plugin)
	require_once("$plugin/init.inc.php");

?>
