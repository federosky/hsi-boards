<?php
define('ORDEN',"N&ordm;");
define('EJEMPLAR',"S.P.C.");
define('KILOSCAB',"Kg.");
define('KILOSREA',"Kg.");

// Tipo de pista
define('RCO','Arena con codo');
define('RCN','Arena con codo nuevo');
define('CRE','C&eacute;sped recta');
define('CCO','C&eacute;sped con codo');
define('CDI','C&eacute;sped diagonal');

function tr($text){
	if( !defined($text) ) return $text;
	return constant($text);
}
?>