<?php
/**
 * Reads and displays the information of a finished race
 */
require_once(dirname(__FILE__).'/inc/config.inc.php');
require_once(dirname(__FILE__).'/inc/common.php');
require_once(dirname(__FILE__).'/inc/dictionary.php');

/**
 * Conexion a la base..
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

// Numero de carrera
$race_no = '1';
if( !empty($_GET['race']) ) $race_no = $_GET['race'];

// selecciono la base en la que voy a trabajar..
mysql_select_db($db_data['db_name'],$dbh);

$sql = 'SELECT nombre,pista,estado,distancia,orden,puesto,difere,tiempo FROM carreras WHERE carrera = '.$race_no;
$sql.= ' AND fecha = ( SELECT max(fecha) FROM carreras ) AND puesto IN (1,2,3,4,5,6) ORDER BY puesto ASC';
$resource = mysql_query($sql,$dbh);
$results_data = array();
while( $reg = mysql_fetch_assoc($resource) ){
	array_push($results_data, $reg);
	$race_data[$reg['orden']] = $reg;
}

$winner_order         = $results_data[0]['orden'];
$carrera_nombre       = $results_data[0]['nombre'];
$carrera_pista        = $results_data[0]['pista'];
$carrera_pista_estado = $results_data[0]['estado'];
$carrera_distancia    = $results_data[0]['distancia'];
$carrera_tiempo	      = format_race_time($results_data[0]['tiempo']);

mysql_free_result($resource);

$sql = '';
$sql.= 'SELECT nombre,ordenes,importe,descripcio FROM dividendos ';
$sql.= 'WHERE carrera='.$race_no;
$sql.= ' AND fecha = (SELECT max(fecha) from carreras)';

$resource = mysql_query($sql,$dbh);
$results = array();
while ($record = mysql_fetch_assoc($resource)){
	array_push($results,$record);
}

mysql_free_result($resource);
mysql_close($dbh);

foreach( $results as $key => $result ){
	$bet_name = strtolower($result['nombre']);
	$bets2chop = array('ganador','segundo','tercero');
	if( in_array($bet_name, $bets2chop) )
	{
		$race_data[$result['ordenes']]['apuestas'][strtolower($result['nombre'])] = $result['importe'];
		unset($results[$key]);
	}
}

$otras_apuestas = array();
foreach( $results as $key => $result ){
	$new_key = str_replace(' ','_',$result['nombre']);
	$otras_apuestas[$new_key] = $result;
}
?>
<input type="hidden" name="race_no" id="race_no" value="<?php echo($race_no)?>" />
<div id="header" class="clearfix">
	<div id="header_Logo">
	<h2>CARRERA N&ordm;<?php echo($race_no.' :: '.$carrera_nombre)?></h2>
	</div>
	<div id="header_Title">
		<h1>Hip&oacute;dromo de San Isidro</h1>
	</div>
</div>
<?php
$output = 'Pista: ';
$output.= tr($carrera_pista);
$output.= ', '.tr('LANE_'.$carrera_pista_estado);
$output.= ', '.$carrera_distancia.' metros.';
if( $carrera_tiempo ) $output.= ' - Disputada en '.$carrera_tiempo;
?>
<div style="text-align:left;padding:1px 3px;">
	<p style="color:#ffffff;font-size:21px;margin:0px;padding:0px;">
	<?php echo($output)?>
	</p>
</div>
<div style="width:95%;margin:auto;margin-bottom:6px;">
	<table class="content" id="ERL">
		<tr>
			<th colspan="2" style="width:18%;">Marcador</th>
			<th>Diferencia</th>
			<th>Ganador</th>
			<th>Segundo</th>
			<th>Tercero</th>
		</tr>
		<?php $count = 1;?>
		<?php foreach( $race_data as $order => $data):?>
			<tr>
				<td class="mark"><?php echo(sprintf('%d&ordm;',$data['puesto']))?></td>
				<td><?php echo(sprintf('%d',$data['orden']))?></td>
				<td><?php echo( !empty($data['difere']) ? $data['difere']:'&nbsp;-&nbsp;');?></td>
				<td>
				<?php
				if( !empty($data['apuestas']['ganador']) ){
					echo(sprintf('%.2f',$data['apuestas']['ganador']));
				}
				else{ 
					echo('&nbsp;'); 
				} ?>
				</td>
				<td>
				<?php
				if( !empty($data['apuestas']['segundo']) ){
					echo(sprintf('%.2f',$data['apuestas']['segundo']));
				}
				else{ 
					if( !empty($data['apuestas']['ganador']) ) echo('&nbsp;-&nbsp;');
					else echo('&nbsp;'); 
				} ?>
				</td>
				<td>
				<?php
				if( !empty($data['apuestas']['tercero']) ){
					echo(sprintf('%.2f',$data['apuestas']['tercero']));
				}
				else{ 
					if( $count <= 3 ) echo('&nbsp;-&nbsp;');
					else echo('&nbsp;'); 
				} ?>
				</td>
			</tr>
			<?php $count++;?>
		<?php endforeach;?>
		
	</table>
</div>
<div style="width:95%;margin:auto;">
	<table class="content" id="ERR">
		<tr><th colspan="3">DIVIDENDOS</th></tr>
		<?php
		foreach( $results as $apuesta ){
			//if( $apuesta['importe'] ){
				$ordenes = explode(' ',$apuesta['ordenes']);
				for($i = 0; $i < count($ordenes); $i++)
					$ordenes[$i] = ltrim($ordenes[$i],' 0');
				$html = "\t\t\t".'<tr><td>'.$apuesta['nombre'].'</td>';
				$html.= '<td>'.implode(' - ',$ordenes).'</td>';
				$html.= '<td style="text-align:center;">';
				if( !empty($apuesta['importe']) ){
					$html.= sprintf('%.2f',$apuesta['importe']);
				} else $html.= strtoupper($apuesta['descripcio']);
				$html.= '</td>'."\n\t\t\t".'</tr>';
				echo $html;
			//}
		}
		?>
	</table>
</div>
