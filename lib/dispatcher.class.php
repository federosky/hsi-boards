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
 * @package    Libs
 * @subpackage MVC
 * @author     Rodrigo Garcia <rodrigo.garcia@corp.terra.com.ar>
 * @access private
 */

/**
 *
 */
require_once(dirname(__FILE__).'/object.class.php');
require_once(dirname(__FILE__).'/logger.class.php');

/**
 * DispatcherStatus
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class DispatcherStatus extends Object {

	/**
	 * @var int Request Handled
	 */
	var $handled   = 1;

	/**
	 * @var int Request Unhandled
	 */
	var $unhandled = 2;

	/**
	 * @var int Request Continue
	 */
	var $continue  = 3;

	/**
	 * @var int Request Error
	 */
	var $error     = 4;

	/**
	 * @var int Request Break
	 */
	var $break     = 5;
}

/**
 * Dispatcher
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class Dispatcher extends Object {

	/**
	 * @var array
	 * @access private
	 */
	var $_handlers = array();

	/**
	 * @var integer
	 * @access private
	 */
	var $_handlers_count = 0;

	/**
	 * @var array
	 * @access private
	 */
	var $_keys     = array();

	/**
	 * @var Logger
	 */
	var $logger    = null;

	/**
	 * @var DistatcherStatus
	 */
	var $status    = null;

	function __construct() {
		parent::__construct();
		$this->logger = Logger::instance();
		$this->status = new DispatcherStatus();
	}

	/**
	 * Register a handler
	 *
	 * ej: $dispatcher->register_handler('/home/*' , 'CApp::hdefault')
	 *
	 * @param string $name
	 * @param string|array|Callback $callback
	 * @param array $params
	 */
	function register_handler($name, $callback, $params=null) {
		if (is_string($callback))
			$callback =& new CallbackLocal($callback);
		elseif (is_array($callback))
			$callback =& new CallbackChain($callback);

		if ($callback->is_callback) {
			if (!array_key_exists($name, $this->_keys)) {
				array_push($this->_keys, $name);
				$this->_handlers_count++;
			}
			if(is_array($params))
				$callback->params($params);

			$this->_handlers[$name] =& $callback;
		}
	}

	/**
	 * Process request with context
	 *
	 * @param Context &$context
	 */
	function run(&$context)	{
		$this->forward('/app::run/', $context);
	}

	/**
	 * Redirect the control to $name
	 *
	 * @param string $name
	 * @param Context &$context
	 */
	function forward($name, &$context) {
		$this->logger->debug(sprintf('%s::%s: Forward to %s', __CLASS__, __FUNCTION__, $name));

		/**
		 * @TODO ver bien el manejo de estado en la devolucion
		 */
		if (array_key_exists($name, $this->_handlers)) {
			$this->logger->debug(sprintf('%s::%s: Call Exact Matching Rule %s', __CLASS__, __FUNCTION__, $name));
			if ($this->_handlers[$name]->call($this, $context) != $this->status->unhandled)	return $this->status->handled;
		}

		$regs = array();
		for($i=0; $i<$this->_handlers_count; $i++)
		{
			$k = $this->_keys[$i];

			if (preg_match('/'.addcslashes($k, '/').'/is', $name, $regs))
			{
				$this->logger->debug(sprintf('%s::%s: Call Matching Rule %s', __CLASS__, __FUNCTION__, $k));
				$context->action_params($regs);

				/**
				 * @todo ver bien el manejo de estado en la devolucion
				 */
				$status = $this->_handlers[$k]->call($this, $context);
				if ($status != $this->status->unhandled) return $status;
			}
		}

		return $this->status->unhandled;
	}
}

/**
 * Abstract class for execution of methods
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class Callback extends Object {

	/**
	 * @var array
	 * @access private
	 */
	var $_params      = null;

	/**
	 * @var Logger
	 */
	var $logger       = null;

	/**
	 * True if instance is a child of Callback
	 *
	 * @var boolean
	 */
	var $is_callback  = true;

	/**
	 *
	 */
	function __construct($params=array()) {
		parent::__construct();
		$this->_params = $params;
	}

	/**
	 *
	 * @access public
	 * @param array &params
	 * @return void
	 */
	function params(&$params) {
		$this->_params = $params;
	}

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
	function call(&$app, &$context) {
		$this->_add_params_to_context($context);
		$this->logger  =& $app->logger;
		return $app->status->unhandled;
	}

	/**
	 * @access private
	 */
	function _replace_action_params($text, &$context) {
		foreach($context->action_params() as $i => $v) {
			$text = str_replace("{".$i."}", $v, $text);
		}
		return $text;
	}

	/**
	 * @access private
	 */
	function _add_params_to_context(&$context) {
		foreach($this->_params as $i => $v) {
			$v = $this->_replace_action_params($v, $context);
			$context->param($i, $v);
		}
		return $context;
	}
}

/**
 * Load, instance class and call methods with &$app, &$context params
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackLocal extends Callback
{
	/**
	 * @var array
	 * @access private
	 */
	var $_instances = array();

	/**
	 * @var string
	 * @access private
	 */
	var $_callback  = '';

	/**
	 *
	 */
	function __construct($callback, $params=array())
	{
		parent::__construct($params);
		$this->_callback = $callback;
	}

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
	function call(&$app, &$context)
	{
		parent::call($app, $context);

		list($class, $method) = $this->_parse_callback($context);
		$object = $this->_instance($class, $app, $context);

		$this->logger->debug(sprintf('%s::%s: Call to %s::%s', __CLASS__, __FUNCTION__, $class, $method));
		if (!method_exists($object, $method)) $method = 'hdefault';
		if (!method_exists($object, $method)) return $app->status->unhandled;


		return ($object !== NULL ? $object->$method($app, $context) : $app->status->unhandled);
	}

	/**
	 * @param Context &$context
	 * @access private
	 */
	function _parse_callback(&$context)
	{
		$callback = $this->_replace_action_params($this->_callback, $context);
		$component = explode('::', $callback);
		$class     = array_shift($component);
		$method    = array_shift($component);

		return array($class, $method);
	}

	/**
	 * @param string $class
	 * @access private
	 */
	function _class_to_parts($class) {
		$parts           = explode('/', $class);
		$classname       = array_pop($parts);
		$classname_parts = explode('_', $classname);
		$classfile       = array_pop($classname_parts).'.class.php';
		$classfile       = strtolower(join('/', array_merge($parts, $classname_parts, array($classfile))));

		return array('classname' => $classname, 'classfile' => $classfile);
	}

	/**
	 * @param string $class
	 * @access private
	 */
	function &_instance($class, &$app, &$context)
	{
		$parts = $this->_class_to_parts($class);

		$classname       = $parts['classname'];
		$classfile       = $parts['classfile'];

		$this->logger->debug(sprintf('%s::%s: Get Instance class %s: %s', __CLASS__, __FUNCTION__, $classname, $classfile));

		if (empty($this->_instances[$class]))
		{
			$obj = null;

			if (!class_exists($classname)) {
				$this->logger->debug(sprintf('%s::%s: Load class %s: %s', __CLASS__, __FUNCTION__, $classname, $classfile));
				include_once($classfile);
			}

			if (!class_exists($classname))
				$this->logger->error(sprintf('%s::%s: Cannot load class %s: %s', __CLASS__, __FUNCTION__, $classname, $classfile));
			else
				$obj = new $classname($app, $context);

			if ($obj === null)
				$this->logger->error(sprintf('%s::%s: Cannot instance class %s: %s', __CLASS__, __FUNCTION__, $classname, $classfile));

			$this->_instances[$class] =& $obj;
		}

		return $this->_instances[$class];
	}
}

/**
 * Perform calls to a list of Callbacks
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackChain extends Callback
{
	/**
	 * @var array
	 * @access private
	 */
	var $_callbacks = array();

	/**
	 * @param array $callbacks array of string|Callbacks. If contains string a CallbackLocal instance is created
	 * @param array $params
	 */
	function __construct($callbacks, $params=array())
	{
		parent::__construct($params);
		foreach ($callbacks as $callback)
		{
			if (is_string($callback)) $callback = new CallbackLocal($callback);

			if ($callback->is_callback)
				array_push($this->_callbacks, $callback);
		}
	}

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
	function call(&$app, &$context)	{
		parent::call($app, $context);

		$status = $app->status->unhandled;

		$this->logger->debug(sprintf('%s::%s: Call', __CLASS__, __FUNCTION__));
		foreach ($this->_callbacks as $callback) {
			$status = $callback->call($app, $context);
			if ($status == $app->status->break) break;
		}
		return $status;
	}
}

/**
 * CallbackAlias
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackAlias extends Callback
{
	/**
	 * @var string
	 * @access private
	 */
    var $_callback;

	function __construct($callback, $params=array())
	{
		parent::__construct($params);
		$this->_callback = $callback;
	}

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
    function call(&$app, &$context)
    {
    	parent::call($app, $context);
    	$callback = $this->_replace_action_params($this->_callback, $context);

		$this->logger->debug(sprintf('%s::%s: Forward to %s', __CLASS__, __FUNCTION__, $callback));
        return $app->forward($callback, $context);
    }
}

/**
 * CallbackInclude
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackInclude extends Callback
{
	/**
	 * @var string
	 * @access private
	 */
    var $_file;

	function __construct($file, $params=array())
	{
		parent::__construct($params);
		$this->_file = $file;
	}

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
    function call(&$app, &$context)
    {
    	parent::call($app, $context);
    	$file = $this->_replace_action_params($this->_file, $context);

    	$this->logger->debug(sprintf('%s::%s: Include file %s', __CLASS__, __FUNCTION__, $file));
    	include_once($file);

    	return $app->status->unhandled;
    }
}

/**
 * CallbackHandled
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackHandled extends Callback
{

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
    function call(&$app, &$context)
    {
    	parent::call($app, $context);
    	return $app->status->handled;
    }
}

/**
 * CallbackUnHandled
 *
 * @access private
 * @package Libs
 * @subpackage MVC
 */
class CallbackUnHandled extends Callback
{

	/**
	 * @param Dispatcher &$app
	 * @param Context &$context
	 * @return DispatcherStatus
	 * @access public
	 */
    function call(&$app, &$context)
    {
    	parent::call($app, $context);
    	return $app->status->unhandled;
    }
}

?>