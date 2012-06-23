<?php
/**
 * 
 */
require_once('controller.class.php');

class CCommand extends Controller
{
	/**
	 * Controllers default action
	 * 
	 * @param unknown_type $app
	 * @param unknown_type $context
	 */
	public function hdefault($app, $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$context->param('app.view.template.inline', 'Que lo tiro!');
		$app->logger->info('Call Command default action');
		
		return $app->status->error;
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $app
	 * @param unknown_type $context
	 */	
	public function hpower(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		//$context->param('app.view.template.inline', 'System Halt');
		$context->param('app.view_system.param.command', '/bin/fixa-down-sys.sh');
		$app->forward('/app::view::system/', $context);
		
		$status = $context->param('app.view_system.response.status');
		$output = $context->param('app.view_system.response.output');
		$output = unserialize($output);

		$context->param('app.view.response.code', $status);
		$context->param('app.view.response.cmd_output', print_r($output));
		if( $status )
			return $app->status->error;
			
		$context->param('app.view.template.file','system/execute');
		
		return $app->status->handled;
	}
}
