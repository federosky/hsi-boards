<?php
/**
 * @package    Libs
 * @subpackage Session
 * @access private
 *
 */

require_once('object.class.php');

/**
 * Session
 *
 * @package    Libs
 * @subpackage Session
 * @access private
 */
class Session extends Object
{
	/**
	 * @var String $_id  Session ID 
	 */
	private $_id = 'hipodromo-hsi';
	
	/**
	 * @var String $_name  Session Name
	 */
	private $_name = 'pizarras';
	
	/**
	 * @var String $_savePath
	 */
	private $_savePath = '/tmp';
	
	/**
	 * @var array
	 * @access private
	 */
	private $_params = null;

	/**
	 * Default Class constructor
	 */
	public function __construct( $options = array() )
	{
		if( !empty($options) )
		{
			$this->setup($options);
		}
	}
	
	/**
	 * Setup Session config
	 * @param Array $options
	 * @throws Exception
	 */
	public function setup( $options = array() )
	{
		if( empty($options) )
			throw new Exception('Session config missing.');
		
		if( !empty($options['id']) )
			$this->id($options['id']);
		if( !empty($options['name']) )
			$this->name($options['name']);
		if( !empty($options['save_path']) )
			$this->savePath($options['save_path']);
	}
	
	/**
	 * Retorna la lista de parametros contenidas en Session
	 *
	 * @return array
	 */
	public function params() {
		return array_keys($this->_params);
	}
	
	

	/**
	 * Accesor de Lectura/Escritura de los par�metros de la Sesion
	 *
	 * @param string $name El nombre del par�metro
	 * @param mixed  $value El valor del par�metro (Opcional)
	 * @return mixed
	 */
	public function param($name, $value=null) {
		$this->open();
		if (!is_null($value)) $this->_params[$name] = $value;
		return $this->_params[$name];
	}

	/**
	 * M�todo utilizado para consultar las existencia de un par�metro dentro de Session
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function hasParam($params) {
		$this->open();

		if (!is_array($params)) $params = array($params);
		foreach($params as $param)
			if (array_key_exists($param, $this->_params)) return true;
		return false;
	}

	/**
	 * M�todo utilizado borrar un par�metro dentro de la Sesion
	 *
	 * @param string $name
	 * @return void
	 */
	public function delete($name) {
		$this->open();
		unset($this->_params[$name]);
	}

	/**
	 * Getter/Setter del Nombre de la Sesion
	 *
	 * @param string $name
	 * @return void
	 */
	public function name($name=null) {
		if (!is_null($name)) session_name($name);
		return session_name($this->_name);
	}

	/**
	 * Getter/Setter del Id
	 *
	 * @param string id
	 * @return string
	 */
	public function id($id=null) {
		if (!is_null($id)) session_id($id);
		return session_id($this->_id);
	}

	/**
	 * Getter/Setter del save_path
	 *
	 * @param string save_path
	 * @return string
	 */
	public function savePath( $path=null ) {
		if (!is_null($path)) session_save_path($path);
		return session_save_path($this->_savePath);
	}

	/**
	 * M�todo utilizado para verificar se encuentra abierta Session
	 *
	 * @return boolean
	 */
	public function isOpen() {
		return !is_null($this->_params);
	}

	/**
	 * M�todo utilizado iniciar la Session
	 *
	 * @return void
	 */
	public function open() {
		if ($this->isOpen()) return null;
		session_start();
		$this->_params =& $_SESSION;
	}

	/**
	 * M�todo utilizado cerrar la Session
	 *
	 * @return void
	 */
	public function close() {
		session_write_close();
		$this->_params = null;
	}

	public function destroy() {
		session_destroy();
		$this->_params = null;
	}
	
}

?>