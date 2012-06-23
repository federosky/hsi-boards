<?php
/**
 * 
 */
require_once('controller.class.php');

Class CRace extends Controller
{
	/**
	 * @param Dispatcher $app
	 * @param Context $context
	 */
	public function hdefault(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		$current = $context->session->param('current');
		if( empty($current) )
		{
			$context->session->param('current', 1);
		}
		$context->param('app.view.template.inline', 'Current race: '.$context->session->param('current'));
		
		return $app->status->handled;
	}
	
	/**
	 * @param Dispatcher $app
	 * @param Context $context
	 */
	public function hforward(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$current = (integer)$context->session->param('current');
		$count   = (integer)$context->session->param('count');
		
		$current = ( $current == $count )? 1 : ++$current;
			
		$context->session->param('current', $current);
		$context->param('app.view.template.inline', sprintf('Current race forwarded to %s', $current));
		
		$app->logger->info(sprintf('%s Current race forwarded to %s', __METHOD__, $current));
		
		return $app->status->handled;
	}
	
	public function hrewind(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$current = (integer)$context->session->param('current');
		$count   = (integer)$context->session->param('count');
		
		$current = ( $current == '1' )? $count : --$current;
			
		$context->session->param('current', $current);
		$context->param('app.view.template.inline', sprintf('Current race rewinded to %s', $current));
		
		$app->logger->info(sprintf('%s Current race rewinded to %s', __METHOD__, $current));
		
		return $app->status->handled;
	}
	
	public function hstart(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$current = (integer)$context->session->param('current');
		$context->param('app.view.template.inline', sprintf('Race no. %s started', $current));
		
		return $app->status->handled;
	}
	
	public function hfinish(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		$current = (integer)$context->session->param('current');
		$context->param('app.view.template.inline', sprintf('Race no. %s finished', $current));
		/**
		 * @todo Forward to next race
		 */
		
		return $app->status->handled;
	}
	
	/**
	 * Displays the current race
	 * 
	 * @param Dispatcher $app
	 * @param Context $context
	 */
	public function hcurrent(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		
		if( $context->has_param('race.param.number') )
		{
			$current_race = $context->param('race.param.number');
			$context->session->param('current', $current_race);
			$app->logger->info(sprintf('%s::%s Current race set to %s', __CLASS__, __FUNCTION__, $current_race));
		}
		
		$current = (integer)$context->session->param('current');
		$context->param('app.view.template.inline', sprintf('%s', $current));
		
		return $app->status->handled;
	}
	
	/**
	 * * 
	 * Enter description here ...
	 * @param Dispatcher $app
	 * @param Context $context
	 */
	public function hcheck_schedule(Dispatcher $app, Context $context)
	{
		$app->logger->debug(sprintf('%s', __METHOD__));
		$dbconfig = $context->param('app.db.resource');
		// get current time
		$curtime = time();
		try {
		    $dbh = new PDO('mysql:host='.$dbconfig['host'].';dbname='.$dbconfig['db'],
		    				$dbconfig['username'], $dbconfig['password']);
		    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		    $sql = 'SELECT fecha, carrera, horario';
		    $sql.= ' FROM carreras';
			$sql.= ' WHERE fecha = (SELECT MAX( fecha )FROM carreras )';
			$sql.= ' GROUP BY carrera';
		    foreach($dbh->query($sql) as $row)
		    {
				print_r($row);
				$race_schedule = strtotime(sprintf('%s %s',$row['fecha'],$row['horario'])) - 300;
				if( ($curtime-30 <= $race_schedule) && ($race_schedule <= $curtime+30) )
				{
					// set current to row.carrera
					$context->session->param('current', $row['carrera']);
					$app->logger->info(sprintf('%s Current race set to %s',__METHOD__,$row['carrera']));
					break;
				}
				echo(sprintf('%s < %s < %s', $curtime-150, $race_schedule, $curtime+150));
				echo("\n".'- - - - - - - -'."\n");
		    }
		    $dbh = null;
		} catch (PDOException $e) {
		    $app->logger->error(sprintf("Database Error: %s", $e->getMessage()));
		    $context->param('app.view.template.inline', sprintf("Database Error: %s", $e->getMessage()));
		}
	}

}
