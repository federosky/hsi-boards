<?
require_once(dirname(__FILE__).'/odbc.class.php');

class Results extends OdbcPDO
{
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $table;

	function __construct($dsn = '')
	{
		parent::__construct($dsn);
		$this->_table('Resultados');
	}
	
	private function _table( $tableName = null )
	{
		if( !is_null($tableName) )
			$this->table = $tableName;
		return $this->table;
	}
	
	public function setUpdateable( $race )
	{
		$sql = 'UPDATE '.$this->_table().' SET CodOper = 1 where NroCarrera = '.$race;
		$this->execute($sql);
	}
}
?>