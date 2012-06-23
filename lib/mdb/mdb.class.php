<?php
/*
 * Filename.....: class_mdb.php
 * Class........: mdb
 * Aufgabe......: open *.mdb MSAccess files
 * Erstellt am..: Donnerstag, 17. Juni 2004, 23:32:07
 * Changes:
 * 2006-03-08: changed function execute() to allow INSERT, UPDATE and DELETE.
 *             Inserted $RecordsAffected which seems to take random values. At least
 *             not the count of the records affected.
 *             Thanks to Pete from DigiOz Multimedia for this hint.
 * 2005-09-27: added new field $rowcount which may be written by function execute()
 *             added new field $ok which is filled after mdb() or open() is called
 *             changed function execute( ) to have a second (optional) parameter $getrowcount (default false)
 *             changed function mdb() to call open() and return the success to $mdb->ok as boolean
 *             changed function open() to return the success as boolean
 *             Using this class gets even more simple: function mdb() now calls open() directly
 * 2004-07-21: added function fieldcount()
 */

/**
 * Unique root
 */
require_once(dirname(__FILE__).'/../object.class.php');

class Mdb extends Object {

	public $RS = 0;
	public $ADODB = 0;

	public $RecordsAffected;

	public $strProvider   = 'Provider=Microsoft.Jet.OLEDB.4.0';
	public $strMode       = 'Mode=ReadWrite';
	public $strPSI        = 'Persist Security Info=False';
	public $strDataSource = '';
	public $strConn       = '';
	public $strRealPath   = '';

	public $recordcount = 0;
	public $ok = false;


	/**
	 * Constructor needs path to .mdb file
	 *
	 * @param string $dsn = path to *.mdb file
	 * @return boolean success
	 */
	function __construct( $dsn='Please enter DataSource!' ){
		$this->strRealPath = realpath( $dsn );
		if( strlen( $this->strRealPath ) > 0 ){
			$this->strDataSource = 'Data Source='.$this->strRealPath;
			$result = true;
		}
		else{
			echo sprintf('<br/>%s::%s() File not found %s <hr/>',__CLASS__,__FUNCTION__,$dsn);
			$result = false;
		}

		$this->RecordsAffected = new VARIANT();
		$this->open();
	} // eof constructor mdb()


	function open( ){
		if( strlen( $this->strRealPath ) > 0 ){
			$this->strConn =
				$this->strProvider.';'.
				$this->strDataSource.';'.
				$this->strMode.';'.
				$this->strPSI;

			$this->ADODB = new COM( 'ADODB.Connection' );
			if( $this->ADODB ){
				try {
					$this->ADODB->open( $this->strConn );
					$result = true;
				} 
				catch( Exception $exc){
					die($exc->__toString());
				}
			}
			else{
				echo '<br>mdb::open() ERROR with ADODB.Connection<br>'.$this->strConn;
				$result = false;
			}
		}

		$this->ok = $result;
		return $result;
	} // eof open()


	/**
	 * Execute SQL-Statement
	 * @param string $strSQL = sql statement
	 * @param boolean $getrecordcount = true when a record count is wanted
	 */
	function execute( $strSQL, $getrecordcount = false ){

		$this->RS = $this->ADODB->execute( $strSQL, &$this->RecordsAffected );
		echo 'Registros: '.$this->RS->fields->count().'<hr/>';
		
		//$this->RS->Open();
		if( $getrecordcount == true ){
			$this->RS->MoveFirst();
			$this->recordcount = 0;

			# brute force loop
			while( $this->RS->EOF == false ){
				$this->recordcount++;
				$this->RS->MoveNext();
			}
			$this->RS->MoveFirst();
		}
	} // eof execute()
	
	private function _recordsAffected( $affectedRows = null ){
		if( $affectedRows != null ) $this->RecordsAffected = $affectedRows;
		return $this->RecordsAffected;
	}

	function eof(){
		return $this->RS->EOF;
	} // eof eof()

	function moveNext( ){
		$this->RS->MoveNext();
	} // eof movenext()

	function moveFirst(){
		$this->RS->MoveFirst();
	} // eof movefirst()

	function close(){
		try{
			$this->RS->Close();
		}
		catch (Exception $exc){
			echo '<pre>Error: '.$exc->__toString().'</pre>';
		}
		$this->RS=null;
		@$this->ADODB->Close();
		$this->ADODB=null;
	} // eof close()

	function fieldValue( $fieldname ){
		return $this->RS->Fields[$fieldname]->value;
	} // eof fieldvalue()

	function fieldName( $fieldnumber ){
		return $this->RS->Fields[$fieldnumber]->name;
	} // eof fieldname()

	function fieldCount( ){
		return $this->RS->Fields->Count;
	} // eof fieldcount()

} // eoc mdb
?>