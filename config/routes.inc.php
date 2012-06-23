<?php
/**
 * Configuracion de Mapeo de URL a Controladores y Metodos
 *
 * @package    Config
 * @subpackage Routes
 * @filesource
 */

/**
 *
 */
$file_dirname = dirname(__FILE__);
global $app, $context;


/* Bind to Router */
$app->register_handler('^/app::router::broadcast/([^?]+|$)', 'CConnector_Router_Broadcast::h{1}');
$app->register_handler('^/app::router/([^?]+|$)',            'CConnector_Router::h{1}');

$app->register_handler('/app::run/',                         'CApp::hrun');
$app->register_handler('/app::redirecto/',                   'CApp::hredirecto');

/* Bind Views */
$app->register_handler('/app::view::http/',    	 	    	 array('CView_HTTP::hsend'));
$app->register_handler('/app::view::render_uri/',        	 array('CView_PHP::hfilter_inline', 'CView_PHP::hrender'));
$app->register_handler('/app::view::default/',               array('CView_PHP::hrender', 'CView::hsend'));
$app->register_handler('/app::view::render_default/', 	     array('CView_PHP::hrender'));
$app->register_handler('/app::view::render_php/', 	      	 array('CView_PHP::hrender'));
$app->register_handler('/app::view::system/', 	    		 array('CView_System::hexecute'));

/* Customs Errors */
$app->register_handler('^/app::error/([0-9]*)/([^/]*)',      new CallbackAlias('/app::error/', array( 'error.status' => '{1}', 'error.description' => '{2}')));
$app->register_handler('/app::error/bad_request/',           new CallbackAlias('/app::error/', array( 'error.status' => '400', 'error.description' => 'Bad Request')));
$app->register_handler('/app::error/unhandled/',             new CallbackAlias('/app::error/', array( 'error.status' => '500', 'error.description' => 'Unhandled')));
$app->register_handler('/app::error/',                       'CApp::herror');


/**
 * Carga las rutas particulares para el ambiente
 * en el que se ejecuta (production/development/etc)
 */
//require_once($file_dirname.'/routes/'.$context->param('app.environment').'.inc.php');
$app->register_handler(
					'/services/command/(.*)',
					'CCommand::h{1}'
);

$app->register_handler(
					'/services/race/([a-z].+)/([0-9].*)',
					'CRace::h{1}',
					array(
						'race.param.number' => '{2}'
					)
);
$app->register_handler(
					'/services/race/(.*)',
					'CRace::h{1}'
);

?>
