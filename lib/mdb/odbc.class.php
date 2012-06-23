<?php
/**
 * 
 */
class OdbcPDO extends PDO
{
	/**
	 * 
	 * Enter description here ...
	 * @var Integer $_affectedRows
	 */
	private $_affectedRows = 0;
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $dsn
	 * @param unknown_type $username
	 * @param unknown_type $passwd
	 * @param unknown_type $options
	 */
	public function __construct($dsn, $username = null, $passwd = null, $options = null)
	{
		return parent::__construct($dsn, $username, $passwd, $options);
	}
	
	
	public function execute($query)
	{
		$collection = array();
		$statement = parent::prepare($query);
		$statement->execute();
		$collection = $statement->fetchAll(PDO::FETCH_ASSOC);
		$this->_affectedRows = $statement->rowCount();
		
		return $collection;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PDO::beginTransaction()
	 */
	public function beginTransaction()
	{
		throw new PDOException(sprintf('ODBC Driver does not support transactions'));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PDO::rollBack()
	 */
	public function rollBack()
	{
		throw new PDOException(sprintf('ODBC Driver does not support transactions'));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PDO::commit()
	 */
	public function commit()
	{
		throw new PDOException(sprintf('ODBC Driver does not support transactions'));
	}
	
	/**
	 * (non-PHPdoc) 
	 * @see PDO::inTransaction()
	 */
	public function inTransaction()
	{
		throw new PDOException(sprintf('ODBC Driver does not support transactions'));
	}
}