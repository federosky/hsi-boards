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
 * @subpackage Controllers-Views
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 */

/**
 *
 */
require_once('cview.class.php');
require_once('HTTP/Request.php');

/**
 * CView_HTTP
 *
 * @package    App
 *
 * {@inheritdoc}
 */
class CView_HTTP extends CView {

	/**
	 * @access private
	 * @var HTTP_Request
	 */
	var $_http;

	function __construct() {
		parent::__construct();
		$this->_http = &new HTTP_Request(null, array( 'timeout' => 10, 'readTimeout' => array(30,0)));
		$this->_http->addHeader('User-Agent', 'TERRA.LA');
	}

	/**
	 *
	 * HTTP View
	 *
	 * Context input params:
	 * - app.view_http.request.method        : string
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hsend(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$http_method = $c->param('app.view_http.request.method');
		if ((strcasecmp($http_method, 'post')) == 0)
			return $this->hsend_post($app, $c);
		else
			return $this->hsend_get($app, $c);
	}

	/**
	 *
	 * HTTP GET View
	 *
	 * Context input params:
	 * - app.view_http.request.uri          : string
	 * - app.view_http.request.auth.username: string
	 * - app.view_http.request.auth.password: string
	 * - app.view_http.request.headers      : array
	 * - app.view_http.request.params       : array
	 *
	 * Context ouput params:
	 * - app.view_http.response.body
	 * - app.view_http.response.status
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hsend_get(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$this->_http->setMethod(HTTP_REQUEST_METHOD_GET);

		$this->_http->setURL($c->param('app.view_http.request.uri'));

		$this->_set_basic_auth($app, $c);

		$this->_load_get_params($app, $c);

		$this->_load_headers($app, $c);

		$this->_http->sendRequest();

		$this->_handler_response($app, $c);

		return $app->status->handled;
	}

	/**
	 *
	 * HTTP POST View
	 *
	 * Context input params:
	 * - app.view_http.request.uri       : string
	 * - [app.view_http.request.headers] : array
	 * - [app.view_http.request.params]  : array
	 * - [app.view_http.request.files]   : array
	 *
	 * Context ouput params:
	 * - app.view_http.response.body   : string
	 * - app.view_http.response.status   : string
	 *
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return DispatcherStatus
	 */
	function hsend_post(&$app, &$c) {
		$app->logger->debug(sprintf('%s::%s', __CLASS__, __FUNCTION__));

		$this->_http->setMethod(HTTP_REQUEST_METHOD_POST);

		$this->_http->setURL($c->param('app.view_http.request.uri'));

		$this->_set_basic_auth($app, $c);

		$this->_load_post_params($app, $c);

		$this->_load_headers($app, $c);

		$this->_load_body($app, $c);

		$this->_load_files($app, $c);

		$this->_http->sendRequest();

		$this->_handler_response($app, $c);

		return $app->status->handled;
	}

	/**
	 *
	 * Context input params:
	 * - app.view_http.request.auth.username
	 * - app.view_http.request.auth.password
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _set_basic_auth(&$app, &$c) {
		$u = $c->param('app.view_http.request.auth.username');
		$p = $c->param('app.view_http.request.auth.password');
		if ($u && $p)
			$this->_http->setBasicAuth($u, $p);
	}


	/**
	 *
	 * Context ouput params:
	 * - app.view_http.response.body
	 * - app.view_http.response.status
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _handler_response(&$app, &$c) {
		$c->param('app.view_http.response.status', $this->_http->getResponseCode());
		$c->param('app.view_http.response.body', $this->_http->getResponseBody());
		$c->param('app.view_http.response.headers', $this->_http->getResponseHeader());
	}

	/**
	 *
	 * Context input params:
	 * - app.view_http.request.headers: array
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_headers(&$app, &$c) {
		$headers =& $c->param('app.view_http.request.headers');
		if (is_array($headers)) {
			foreach($headers as $h_name => $h_value) {
				$this->_http->addHeader($h_name, $h_value);
			}
		}
	}

	/**
	 * Metodo temporal utilizado para poder mantener la conf. "antigua" del Router hasta migrar a la nueva
	 *
	 * Context input params:
	 * - app.view_http.request.uri
	 * - app.view.response.output
	 *
	 * Context output params:
	 * - app.view.response.output
	 * - app.view.template.inline
	 * - app.view_http.request.uri
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_oldway_params(&$app, &$c) {
		$app_view_http_request_uri = $c->param('app.view_http.request.uri');
		$uri = $app_view_http_request_uri;
		$params = '';
		if (strpos($app_view_http_request_uri, '?') !== false)
			list($uri, $params) = explode('?', $app_view_http_request_uri, 2);

		$uri    = preg_replace('/(\{([^}]+)})/i', '<?php echo($params[\'$2\'])?>', $uri);
		$url = $uri;
		if ($params) {
			$params = preg_replace('/(\{([^}]+)})/i', '<?php echo(urlencode($params[\'$2\']))?>', $params);
			$url .= '?' . $params;
		}

		$c->param('app.view.template.inline', $url);

		$app_view_response_output_aux = $c->param('app.view.response.output');
		$c->param_delete('app.view.response.output');
		$app->forward('/app::view::render_php/', $c);

		$c->param('app.view_http.request.uri', $c->param('app.view.response.output'));

		$c->param_delete('app.view.template.inline');
		if (is_null($app_view_response_output_aux))
			$c->param_delete('app.view.response.output');
		else
			$c->param('app.view.response.output', $app_view_response_output_aux);
	}

	/**
	 *
	 * Context input params:
	 * - app.view_http.request.params: array
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_get_params(&$app, &$c) {
		$params =& $c->param('app.view_http.request.params');
		if (is_array($params)) {
			foreach($params as $p_name => $p_value) {
				$this->_http->addQueryString($p_name, $p_value);
			}
		}
	}

	/**
	 *
	 * Context input params:
	 * - app.view_http.request.params: array
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_post_params(&$app, &$c) {
		$params =& $c->param('app.view_http.request.params');
		if (is_array($params)) {
			foreach($params as $p_name => $p_value) {
				$this->_http->addPostData($p_name, $p_value);
			}
		}
	}


	/**
	 *
	 * Context input params:
	 * - app.view_http.request.body: String
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_body(&$app, &$c) {
		$body = $c->param('app.view_http.request.body');
		if ($body != '')
			$this->_http->setBody($body);
	}


	/**
	 *
	 * Context input params:
	 * - app.view_http.request.files: array
	 *
	 * @access private
	 * @param Dispatcher &$app
	 * @param Context &$c
	 * @return void
	 */
	function _load_files(&$app, &$c) {
		$files = $c->param('app.view_http.request.files');
		if (is_array($files)) {
			foreach($files as $k => $file) {
				if (is_array($file['size'])) {
					for($i=0;$i<count($file['size']);$i++) {
						if (($file['size'][$i] > 0) && (!empty($file['tmp_name'][$i])))
							$this->_http->addFile($file['name'][$i], $file['tmp_name'][$i], $file['type'][$i]);
					}
				}
				else
					if (($file['size'] > 0) && (!empty($file['tmp_name'])))
						$this->_http->addFile($file['name'], $file['tmp_name'], $file['type']);
			}
		}
	}

	/**
	 *
	 * @access private
	 * @param array $var
	 * @return boolean
	 */
	function _is_hash($var) {
   		return is_array($var) && sizeof($var) > 0 && array_keys($var)!==range(0,sizeof($var)-1);
	}
}
?>