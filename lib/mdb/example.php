<?php
#
# This is some wired example.
# You cannot use it out of the box.
#
# Use your own mdb filename and
# your own tablename and
# your own fieldnames.
#
# You really need a Windows Server and a mdb file on it to have it work!
# Big chance it does not work with Linux or Unix - but tell me if it does.
#
# This example opens the mdb once and then reads it twice.
# I know about 2 ways to get the values, so I show them all.
# Maybe there is a speed difference.
#
# Works on a Win 2000 server with PHP 4.3.4 and Microsoft-IIS/5.0
#

require_once(dirname(__FILE__).'/odbc.class.php');

$mdbFilePath = 'c:\Archivos de programa\hsi\CarrerasTest.mdb';

try{
	$dbh = new OdbcPDO('odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq='.$mdbFilePath.';Uid=Admin');
}
catch(PDOException $e)
{
	echo $e->getMessage();
}
$results = $dbh->execute('SELECT * FROM borrados WHERE NroCarrera = 1');
echo('<pre>'.print_r($results,1).'</pre>');

$dbh->execute('UPDATE borrados SET CodOper = 6');// WHERE NroCarrera IN (1,3,5)');
echo('<pre>'.print_r($dbh,1).'</pre>');

$results = $dbh->execute('SELECT * FROM borrados');
echo('<pre>'.print_r($results,1).'</pre>');

//$results = $dbh->execute('SELECT * FROM resultados');
//echo('<pre>'.print_r($results,1).'</pre>');
exit(0);

require_once('mdb.class.php');
require_once('results.class.php');

$mdbFilePath = dirname(__FILE__).'/../../tmp/mdb/Carreras.mdb';

$mdb = new Mdb( $mdbFilePath ); // your own mdb filename required
$results = new Results( $mdbFilePath );
//echo '<pre>'.print_r($mdb,1).'</pre>';
//echo '<pre>'.print_r($results,1).'</pre>';
$mdb->execute('select * from resultados'); // your own table in the mdb file

#
# first example: using fieldnames
#

while( !$mdb->eof() ){
	echo $mdb->fieldValue('PathCompArchInfo'); // using your own fields name
	echo ' = ';
	echo $mdb->fieldValue( 1 ); // using the fields fieldnumber
	echo '<br>';
	$mdb->moveNext();
}

echo '<br><hr>No se que va a imprimir aca..<br>';

#
# Going back to the first recordset
#
$mdb->moveFirst();

#
# This works, too: Make each Field an object. The values change
# when the data pointer advances with movenext().
#
$url = $mdb->RS->Fields(1);
$bez = $mdb->RS->Fields(2);
$kat = $mdb->RS->Fields(3);
$field_count = $mdb->fieldCount();
$fields = array();
for( $i = 0; $i < $field_count; $i++ ){
	array_push($fields, $mdb->fieldName($i));
}
echo 'Field names: '.implode('|',$fields);

$mdb->moveFirst();
while( !$mdb->eof() ){
	# works!
	$output = '';
	$output.= $bez->value.' = '.$url->value;
	echo $output.'<br>';
	$mdb->moveNext();
}

$mdb->close();

?>