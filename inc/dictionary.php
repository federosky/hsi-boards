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

// Estados de la pista
define('LANE_', 'Normal'); // default state
define('LANE_N', 'Normal');
define('LANE_P', 'Pesada');
define('LANE_H', 'H&uacute;meda');
define('LANE_F', 'Fangosa');

function tr($text){
	if( !defined($text) ) return $text;
	return constant($text);
}
