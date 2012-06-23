<?php
/**
 *
 */
require_once('cview.class.php');

/**
 * CView_PHP
 *
 * @package    App
 * @subpackage Controllers-Views
 */
class CView_System extends CView
{
	/**
	 * Render view
	 *
	 * Context input params:
	 * - app.view.command
	 * - app.view.command.options
	 * 
	 * - app.view.template.file   : template name
	 * - app.view.template.format : template format
	 * - app.view.template.inline : inline template
	 * - app.view.template.layout : name of layout template
	 * - app.view.response.output : rendered view
	 *
	 * Context output params:
	 * - app.view_system.response.code   : Command exit code
	 * - app.view_system.response.output : Command output
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 * 
	 * @todo Validate params
	 */
	function hexecute($app, $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$command = $context->param('app.view_system.param.command');
		$output = array();
		$status = null;
		exec($command, $output, $status);
		$app->logger->info(sprintf('%s::%s Execute \'%s\', output %s', __CLASS__, __FUNCTION__, $command, serialize($output)));
		
		if( $status )
		{
			$app->logger->error(sprintf('%s::%s Error executing \'%s\', output %s', __CLASS__, __FUNCTION__, $command, serialize($output)));
		}
		
		$context->param('app.view_system.response.status', $status);
		$context->param('app.view_system.response.output', print_r($output,1));
		$context->param('app.view_system.response.output_serialized', serialize($output));
		
		return $app->status->handled;
	}

}
