<?php
/**
 * Application run Context representation
 */
require_once(dirname(__FILE__).'/Session.php');
require_once(dirname(__FILE__).'/../logger.class.php');

/**
 * Context
 *
 * @package    Libs
 * @access private
 */
class Context
{
	/**
	 * @var array
	 * @access private
	 */
	private $_params  = array();

	/**
	 * @var Session
	 * @access public
	 */
	public $session;
	/**
	 * Logger Intance
	 * @var Logger
	 */
	public $logger;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		if (!$_REQUEST && array_key_exists('QUERY_STRING', $_SERVER)) {
			parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
		}
		if (!$_REQUEST) {
			$_REQUEST = array();
		}

		if (!array_key_exists('REQUEST_URI', $_SERVER)) {
			$_SERVER['REQUEST_URI'] = '/';
		}

		$this->_params                             =  array_merge($_REQUEST, $_SERVER);
		$this->_params['app.server']               =  $_SERVER;
		$this->_params['app.request.params.get']   =  $_GET;
		$this->_params['app.request.params.post']  =  $_POST;
		$this->_params['app.request.cookies']      =  $_COOKIE;
		$this->_params['app.request.params.files'] =  $_FILES;
		$this->_params['app.request.body']         =  @file_get_contents('php://input');

		$this->session = new Session();
		$this->logger = Logger::instance('Syslog', 'Context', LOGGER_ALL);
		$this->action($_SERVER['REQUEST_URI']);
		$this->actionParams(array());

		if (isset($_SERVER['PHP_AUTH_USER'])) $this->param('auth.username',  $_SERVER['PHP_AUTH_USER']);
		if (isset($_SERVER['PHP_AUTH_PW']))   $this->param('auth.password',  $_SERVER['PHP_AUTH_PW']);
	}

	/**
	 * Retorna la lista de parametros contenidas en el contexto
	 *
	 * @return array
	 * {@source}
	 */
	public function params()
	{
		return $this->_params;
	}

	/**
	 * Get params heys identifiers
	 *
	 * @return array
	 */
	public function paramsKeys()
	{
		return array_keys($this->_params);
	}

	/**
	 * Accesor de Lectura/Escritura de los parametros del Contexto
	 *
	 * @param string $name El nombre del par�metro
	 * @param mixed  [$value=null] El valor del par�metro
	 * @return mixed
	 * {@source}
	 */
	public function param($name, $value=null)
	{
		if (!is_null($value)) {
			$this->_params[$name] = $value;
		}

		if (array_key_exists($name, $this->_params)) {
			$return = $this->_params[$name];
		} else {
			$return = null;
		}

		return $return;
	}

	/**
	 * Realiza un merge entre _params y el array pasado como argumento
	 *
	 * Retorna la lista de parametros contenidas en el contexto
	 *
	 * @param array $array
	 * @return array
	 * {@source}
	 */
	public function paramsMerge($array)
	{
		$array = is_array($array) ? $array : array($array);

		$this->_params = array_merge($this->_params,$array);

		return $this->params();
	}

	/**
	 * M�todo utilizado para consultar las existencia de uno o m�s par�metros dentro del Contexto
	 *
	 * @param array|string $param
	 * @return boolean
	 */
	public function hasParam($params)
	{
		$params = is_array($params) ? $params : array($params);
		foreach ($params as $param) {
			if (!array_key_exists($param, $this->_params)) return false;
		}

		return true;
	}

	/**
	 * M�todo utilizado borrar un par�metro dentro del Contexto
	 *
	 * @param string $name
	 * @return boolean
	 * {@source}
	 */
	public function paramDelete($name)
	{
		unset($this->_params[$name]);
	}

	/**
	 * Accesor de Lectura/Escritura de la accion a ejecutar
	 *
	 * @param mixed $value Valor de la Accion (Opcional)
	 * @return string
	 * {@source}
	 */
	public function action($value = null)
	{
		return $this->param('app.request.uri', $value);
	}

	/**
	 * Accesor de Lectura/Escritura de los par�metros
	 * extraidos de la acci�n
	 *
	 * @param mixed $value Valor de la Accion (Opcional)
	 * @return string
	 * {@source}
	 */
	public function actionParams($value=null)
	{
		return $this->param('app.request.uri.params', $value);
	}
}
