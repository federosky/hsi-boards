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
class CView_PHP extends CView
{
	/**
	 * Render view
	 *
	 * Context input params:
	 * - app.view.template.dir    : templates directory
	 * - app.tmppath              : temporal directory
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

			$dir      = str_replace('../','',$c->param('app.view.template.dir'));
			$filename = str_replace('../','',$c->param('app.view.template.file'));
			$format   = str_replace('../','',$c->param('app.view.template.format'));
			$inline   = $c->param('app.view.template.inline');
			$layout   = str_replace('../','',$c->param('app.view.template.layout'));

			$params   =& $c->params();

			$content_for_layout = '';

			if ($inline) {
    			ob_start();
     			eval("?>".$inline);
     			$content_for_layout = ob_get_clean();
			} elseif ($filename) {
				ob_start();
 				require "$dir/$filename.$format";
 				$content_for_layout = ob_get_clean();
			}

			if ($layout) {
				ob_start();
 				require "$dir/$layout.$format";
 				$output = ob_get_clean();
			} else {
				$output = $content_for_layout;
			}

			$c->param('app.view.response.output', $output);
		}
		return $app->status->handled;
	}

	/**
	 *
	 * Transforma todas las ocurrencias de texto con el formato "{texto}" en "<?php echo($params['texto'])?>"
	 *
	 * Context input params:
	 * - app.view.template.inline : inline template
	 *
	 * Context output params:
	 * - app.view.template.inline : inline template
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function hfilter_inline(&$app, &$c) {
		$c->param('app.view.template.inline',
			ereg_replace('(\{([^}]+)})', '<?php echo($params[\'\\2\'])?>', $c->param('app.view.template.inline'))
		);
	}
}

?>