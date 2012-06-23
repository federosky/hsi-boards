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
 *
 * @package    App
 * @subpackage Controllers-Views
 */

/**
 *
 */
require_once('controller.class.php');
require_once('template.class.php');

/**
 * CView
 *
 * @package    App
 * @subpackage Controllers-Views
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 */
class CView extends Controller
{

	/**
	 * Render view
	 *
	 * Context input params:
	 * - app.view.template.dir    : templates directory
	 * - app.view.template.file   : template name
	 * - app.view.template.format : template format
	 * - app.view.template.inline : inline template
	 * - app.view.template.layout : name of layout template
	 * - app.view.response.output : rendered view
	 *
	 * Context output params:
	 * - app.view.response.output : rendered view
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hrender(&$app, &$c)
	{
		if (!$c->has_param('app.view.response.output')) {
			$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

			$dir = $c->param('app.view.template.dir');
			$filename = str_replace('../','',$c->param('app.view.template.file'));
			$format   = str_replace('../','',$c->param('app.view.template.format'));
			$inline   = $c->param('app.view.template.inline');
			$layout   = str_replace('../','',$c->param('app.view.template.layout'));

			$tpllayout = new Template('{content}');
			if ($dir) $tpllayout->setFileRoot($dir);
			if ($layout) $tpllayout->Open("$layout.$format");

			$tpl = new Template($inline);
			if ($dir) $tpl->setFileRoot($dir);
			if ($filename) $tpl->Open("$filename.$format");

			$tpllayout->setvar('{content}', $tpl->Template);

			foreach (array_keys($c->params()) as $key) {
				$value = $c->param($key);
				$tpllayout->setvars($value, $key);
			}

			$tpllayout->eraseEmptyTags();
			$c->param('app.view.response.output', $tpllayout->Template);

		}

		return $app->status->handled;
	}

	/**
	 * Send rendered view
	 *
	 * Context input params:
	 * - app.view.response.status : Http response status
	 * - app.view.response.headers: Http response Headers
	 * - app.view.response.output : View
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hsend(&$app, &$c)
	{
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		if($c->has_param('app.view.response.status'))
			header('HTTP/1.1 '.$c->param('app.view.response.status'));

		if($c->has_param('app.view.response.headers')){
			$hs = $c->param('app.view.response.headers');
			if(is_array($hs)){
				foreach($hs as $h => $v) header("$h: $v");
			}
		}

		echo $c->param('app.view.response.output');

		return $app->status->handled;
	}
}

?>