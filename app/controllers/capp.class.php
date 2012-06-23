<?php
/**
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    App
 * @subpackage Controllers
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 */

/**
 *
 */
require_once("controller.class.php");

/**
 * CApp
 * Implementaci�n de m�todos
 * @package    App
 *
 * {@inheritdoc}
 */
class CApp extends Controller {

	/**
	 * Entry Point Method
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hrun(Dispatcher $app, Context $c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$this->hbegin($app, $c);
		$status = $app->forward($c->action(), $c);
		if ($status == $app->status->unhandled)
			$app->forward('/app::error/unhandled/', $c);
		elseif ($status == $app->status->error)
			$app->forward('/app::error/', $c);
		$this->hend($app, $c);

		return $app->status->handled;
	}

	/**
	 * Method execute at begin of Request
	 * Check pre-conditions
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hbegin(Dispatcher $app, Context $c)	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));
		// Init session handler
		$c->session->open();
		//$app->forward('/auth/login_required', $c);
		return $app->status->handled;
	}

	/**
	 * Method execute at end of Request
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hend(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$app->forward('/app::view::default/', $c);
		// Tear down session handle
		$c->session->close();
		return $app->status->handled;
	}

	/**
	 * Method execute by Default
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hdefault(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$c->param('url', $c->param('app.root.url').'auth/welcome');
		return $app->forward('/app::redirecto/', $c);
	}

	/**
	 * Redirect to a URL
	 *
	 * Context input params:
	 * - url: url to redirect
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hredirecto(&$app, &$c)	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$c->param('app.view.response.headers', array('Location' => $c->param('url')));
		$c->Param('app.view.response.output',sprintf('<a href="%s">%s</a>', $c->param('url'), $c->param('url')));
		return $app->status->handled;
	}

	/**
	 * Set Response status error
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function herror(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$status = $c->Param('error.status');
		$description = $c->Param('error.description');
		if (!$status)
			$status = '404';
		if (!$description)
			$description = 'Not Found';
		$status = sprintf('%s %s', $status, $description);
		$c->param('app.view.response.status', $status);
		$c->param('app.view.response.headers', array('Content-type' => 'text/plain'));
		$c->param('app.view.response.output', $status);
		return $app->status->handled;
	}

	/**
	 * Check context params exists
	 *
	 * Context input params:
	 * - params.required : array of params to check
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus status->error on fail and status->continue on ok
	 */
	function hcheckparams(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$params  =& $c->param('params.required');
		foreach($params as $p) {
			if (!$c->has_param($p)) return $app->status->error;
		}
		return $app->status->continue;
	}
}

?>