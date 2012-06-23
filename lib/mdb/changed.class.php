<?
require_once(dirname(__FILE__).'/odbc.class.php');

class Changed extends OdbcPDO
{
	private $table;

	function __construct($dsn = '')
	{
		parent::__construct($dsn);
		$this->_table('CambiosDeMonta');
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
		$this->execute($sql, true);
	}
}
?>