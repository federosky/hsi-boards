<?php
/**
* ----------------------------------------------------------------
*			XBase
*			test.php
*
*  Developer        : Erwin Kooi
*  released at      : Nov 2005
*  last modified by : Erwin Kooi
*  date modified    : Jan 2005
*
*  Info? Mail to info@cyane.nl
*
* --------------------------------------------------------------
*
* Basic demonstration
* download the sample tables from:
* http://www.cyane.nl/phpxbase.zip
*
**/

/* load the required classes */
require_once "Column.class.php";
require_once "Record.class.php";
require_once "Table.class.php";

/* create a table object and open it */
//$table = new XBaseTable("test/bond.DBF");
$table = new XBaseTable("../../tmp/detalle.extended.dbf");
$table->open();

/* print some header info */
echo('<pre>');
echo("Version: ".$table->version."<br />");
echo("Is Fox Pro?: ".($table->foxpro?"yes":"no")."<br />");
echo("Modify Date: ".date("r",$table->modifyDate)."<br />");
echo("Record count: ".$table->recordCount."<br />");
echo("Header Length: ".$table->headerLength."<br />");
echo("Record ByteLength: ".$table->recordByteLength."<br />");
echo("In Transaction?: ".($table->inTransaction?"yes":"no")."<br />");
echo("Is encrypted? ".($table->encrypted?"yes":"no")."<br />");
echo("mdx Flag: ".ord($table->mdxFlag)."<br />");
echo("Language Code: ".ord($table->languageCode)."<br />");
echo('</pre>');

// Table dump
echo $table->toHTML();
echo('<hr/>');
/* close the table */
$table->close();
?>