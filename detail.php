<?php
/**
 *
 */
require_once(dirname(__FILE__).'/inc/config.inc.php');

/**
 * DB Connection
 */
$db_connected = false;
if( $dbh = mysql_connect($db_data['host'],$db_data['username'],$db_data['password']) ){
	$db_connected = true;
}
else{
	$db_connected = false;
	$error_str = 'Mysql error: Unable to connect. Verify data.';
	exit($error_str);
}

$db_fields = array('carrera','nombre','tipo','pista','distancia','orden',
'forfait','puesto','kiloscab','kilos','tratamient','kilosrea','categojoc','herraje','ejemplar','jockey');

$sql = '';
$sql.= 'SELECT '.implode(',',$db_fields);
$sql.= ' FROM carreras WHERE fecha = (SELECT max(fecha) FROM carreras ) AND carrera='.$carrera;
$sql.= ' ORDER BY orden ASC;';
$resource = mysql_query($sql);

$data_source_running    = array();
$data_source_notrunning = array();

while ( $record = mysql_fetch_assoc($resource) ){
	if( $record['orden'] ){
		$record['spc_status'] = 'running';
		if( preg_match('/[A-Z]{2}/', $record['puesto']) )
			$record['spc_status'] = 'not_running';
		array_push( $data_source_running, $record);
	}
}

$data_source = array_merge($data_source_running,$data_source_notrunning);

$carrera_nombre       = $data_source[0]['nombre'];
$carrera_pista        = $data_source[0]['pista'];
$carrera_pista_estado = $data_source[0]['estado'];
$carrera_distancia    = $data_source[0]['distancia'];

// Cierro conexion..
//dbase_close($db);
mysql_free_result($resource);
mysql_close($dbh);
?>
<div id="header" class="clearfix">
	<div id="header_Logo">
	<h2>CARRERA N&ordm;<?php echo($carrera.' :: '.$carrera_nombre)?></h2>
	</div>
	<div id="header_Title">
		<h1>Hip&oacute;dromo de San Isidro</h1>
	</div>
</div>
<div style="text-align:left;padding:1px 3px;">
	<p style="color:#ffffff;font-size:21px;margin:0px;padding:0px;">PISTA: <?php echo(tr($carrera_pista).', '.$carrera_distancia)?> metros</p>
</div>
<div id="grid_box">
	<div class="grid_header" class="clearfix">
		<div class="order"><?php echo(tr('ORDEN'))?></div>
		<div class="spc"><?php echo(tr('EJEMPLAR'))?></div>
		<div class="kg_spc"><?php echo(tr('KILOSCAB'))?></div>
		<div class="herraje"><?php echo(tr('Herr.'))?></div>
		<div class="jockey"><?php echo(tr('JOCKEY'))?></div>
		<div class="kg_jockey"><?php echo(tr('KILOSREA'))?></div>
	</div>

	<?php
	foreach( $data_source as $k => $data ){
		$htmlout = '';
		$tratado = '';
		$htmlout.= "\t".'<div class="grid_line clearfix '.$data['spc_status'].'">'."\n";
		$htmlout.= "\t\t".'<div class="order">'.ltrim($data['orden'],"0").'</div>'."\n"; // orden

		if( trim($data['tratamient']) != '' ) $tratado.= '<span class="tratado">&nbsp;(T)</span>';
		$htmlout.= "\t\t".'<div class="spc"><span style="float:left;">'.text_wrap($data['ejemplar'],15).'</span>'.$tratado.'</div>'."\n"; // ejemplar

		if( $data['puesto'] && $data['spc_status'] == 'not_running' ){
			$htmlout.= "\t\t".'<div class="retired">(RETIRADO:'.$data['puesto'].')</div>'."\n";
			$htmlout.= "\t".'</div>'."\n";
			echo $htmlout;
			continue;
		}

		$htmlout.= "\t\t".'<div class="kg_spc">'.$data['kiloscab'].'</div>'."\n"; // kiloscab
		$htmlout.= "\t\t".'<div class="herraje">'.$data['herraje'].'</div>'."\n"; // herrajes
		$jockey_cat_class = 'jockey';
		if( array_key_exists('categojoc',$data) && is_numeric($data['categojoc']) ){
			$jockey_cat_class.= $data['categojoc'];
		}
		$htmlout.= "\t\t".'<div class="'.$jockey_cat_class.'">'.jockey_name_wrap($data['jockey']).'</div>'."\n"; // jockey
		$htmlout.= "\t\t".'<div class="kg_jockey">'.jockey_weight_wrap($data['kilosrea']).'</div>'."\n"; // kilosrea
		$htmlout.= "\t".'</div>'."\n";
		echo $htmlout;
	} ?>

</div>
