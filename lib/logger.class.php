<?php
/**
 *
 */
require_once(dirname(__FILE__).'/object.class.php');

define('LOGGER_NONE', 0);

//-> error conditions
define('LOGGER_ERROR', 1);
//-> warning conditions
define('LOGGER_WARNING', 2);
//-> debug-level message
define('LOGGER_DEBUG', 4);
//-> normal, but significant, condition
define('LOGGER_NOTICE', 8);
//-> normal, but significant, condition
define('LOGGER_INFO', 16);

define('LOGGER_ALL', 2147483647);

/**
 * Logger
 *
 * @access private
 *
 */
class Logger extends Object
{
	private $_name;
	
	private $_level;

	public function __construct($name, $level=null, $config=array()) {
		parent::__construct();
		$this->level((is_null($level) ? LOGGER_NONE : $level));
		$this->name($name);
		$this->open();
    	set_error_handler('Logger_php_error_handler');
    	error_reporting(E_ALL );
	}

	public function accessor($name, $value=null) {
		if ($value !== null) { $this->$name = $value; }
		return $this->$name;
	}

	/**
	 * Read/Write accessor for property name
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function name($value=null) { return $this->accessor('_name', $value); }

	/**
	 * Read/Write accessor for property level
	 *
	 * @param int $value
	 *
	 * @return int
	 */
	public function level($value=null)	{ return $this->accessor('_level', $value); }

	/**
	 * Get Logger instance
	 *
	 * @param string $class
	 * @param string $name
	 * @param int $level
	 * @param array $config
	 *
	 * @return Logger
	 */
    public static function instance($class=null, $name=null, $level=null, $config=array())
    {
        if (!is_null($class) || !array_key_exists('LOGGER', $GLOBALS)) {
            $GLOBALS['LOGGER'] = self::factory($class, $name, $level, $config);
        }

        return $GLOBALS['LOGGER'];
    }

	/**
	 * Create a instance of type Logger_$class
	 *
	 * @param string $class
	 * @param string $name
	 * @param int $level
	 * @param array $config
	 *
	 * @return Logger
	 */
    private static function factory($class, $name, $level, $config = array())
    {
        $classname = "Logger_$class";
        $classfile = dirname(__FILE__).strtolower("/logger/$class.class.php");

        if (!class_exists($classname) && file_exists($classfile)) { include_once($classfile); }

        if (class_exists($classname)) {
            $obj = new $classname($name, $level, $config);
            return $obj;
        } else {
        	$obj = new Logger($name, $level, $config);
        }

        return $obj;
    }

	/**
	 * Get level string
	 *
	 * @param int $level
	 * @return string
	 */
	public function level_to_s($level) {
		$string = array(
			LOGGER_NONE    => '',
			LOGGER_ERROR   => 'ERROR',
			LOGGER_WARNING => 'WARNING',
			LOGGER_DEBUG   => 'DEBUG',
			LOGGER_NOTICE  => 'NOTICE',
			LOGGER_INFO    => 'INFO',
			LOGGER_ALL     => 'ALL'
		);
		return $string[$level];
	}

	/**
	 * Verify if level is enabled
	 *
	 * @param int $level
	 * @return bool
	 */
	public function has_level($level) {
		return ($this->level() & $level);
	}

	/**
	 * Abstract implementation of the open() method
	 */
	private function open() { return false;}

	/**
	 * Abstract implementation of the close() method
	 */
	private function close() { return false; }

	/**
	 * Abstract implementation of the flush() method
	 */
	public function flush() { return false; }

	/**
	 * Abstract implementation of the log() method
	 *
	 * @param string $msg
	 * @param int $level
	 */
	public function log($msg, $level) { return false; }

	/**
	 * Error message
	 *
	 * @param string $msg
	 * @return bool
	 */
	public function error($msg) {
		return $this->log($msg, LOGGER_ERROR);
	}

	/**
	 * Warning message
	 *
	 * @param string $msg
	 * @return bool
	 */
	public function warning($msg) {
		return $this->log($msg, LOGGER_WARNING);
	}

	/**
	 * Notice message
	 *
	 * @param string $msg
	 * @return bool
	 */
	public function notice($msg) {
		return $this->log($msg, LOGGER_NOTICE);
	}

	/**
	 * Info message
	 *
	 * @param string $msg
	 * @return bool
	 */
	public function info($msg) {
		return $this->log($msg, LOGGER_INFO);
	}

	/**
	 * Debug message
	 *
	 * @param string $msg
	 * @return bool
	 */
	public function debug($msg) {
		return $this->log($msg, LOGGER_DEBUG);
	}
	
}

/**
 * Callback for PHP error handler
 */
function Logger_php_error_handler($code, $message, $file, $line)
{
    $logger = Logger::instance();

    /* Map the PHP error to a Log priority. */
    switch ($code) {
	    case E_WARNING:
	    case E_USER_WARNING:
	        $priority = LOGGER_WARNING;
	        break;
	    case E_NOTICE:
	    case E_USER_NOTICE:
	        $priority = LOGGER_NOTICE;
	        break;
	    case E_ERROR:
	    case E_USER_ERROR:
	        $priority = LOGGER_ERROR;
	        break;
	    default:
	        $priority = LOGGER_INFO;
    }
    $logger->log($message . ' in ' . $file . ' at line ' . $line, $priority);
}

?>
