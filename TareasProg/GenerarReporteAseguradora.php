<?php
ini_set('display_errors', 0);
set_time_limit(0);
include "../inc/conexion_inc.php";
include "../inc/fechas.func.php";
include "../inc/nombres.func.php";
Conectarse();

// --------------------------------------------
if ($_GET['fecha1']) {
	$fecha1 = $_GET['fecha1'];
} else {
	$fecha1 = fecha_despues('' . date('d/m/Y') . '', -1);
}
//$fecha1 = '30/09/2019';
// --------------------------------------------
if ($_GET['fecha2']) {
	$fecha2 = $_GET['fecha2'];
} else {
	$fecha2 = fecha_despues('' . date('d/m/Y') . '', -1);
}
// -------------------------------------------
//$fecha2 = '29/10/2019';

$fd1 = explode('/', $fecha1);
$fh1 = explode('/', $fecha2);
$fDesde = $fd1[2] . '-' . $fd1[1] . '-' . $fd1[0];
$fHasta = $fh1[2] . '-' . $fh1[1] . '-' . $fh1[0];

$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";

function Vehiculo($id)
{
	$query = mysql_query(
		"SELECT * FROM  seguro_vehiculo
	WHERE id='" .
			$id .
			"' LIMIT 1"
	);
	$row = mysql_fetch_array($query); 
	return $row['veh_tipo'] .
		"|" .
		$row['veh_marca'] .
		"|" .
		$row['veh_modelo'] .
		"|" .
		$row['veh_ano'] .
		"|" .
		$row['veh_matricula'] .
		"|" .
		$row['veh_chassis'];
}

function Clientes($id)
{
	$query = mysql_query(
		"SELECT * FROM  seguro_clientes
	WHERE id='" .
			$id .
			"' LIMIT 1"
	);
	$row = mysql_fetch_array($query);
	return $row['asegurado_nombres'] .
		"|" .
		$row['asegurado_apellidos'] .
		"|" .
		$row['asegurado_cedula'] .
		"|" .
		$row['asegurado_direccion'] .
		"|" .
		$row['ciudad'] .
		"|" .
		$row['asegurado_telefono1'];
}

function Marcas($id)
{
	$querym = mysql_query(
		"SELECT * FROM seguro_marcas WHERE ID='" . $id . "' LIMIT 1"
	);
	$rowm = mysql_fetch_array($querym);
	return $rowm['DESCRIPCION'];
}

function Modelos($id)
{
	$querymo = mysql_query(
		"SELECT id,descripcion FROM seguro_modelos WHERE id='" . $id . "' LIMIT 1"
	);
	$rowmo = mysql_fetch_array($querymo);
	return $rowmo['descripcion'];
}

function Telefono($id)
{
	$telefono = str_replace("-", "", $id);
	$in = $telefono;
	return substr($in, 0, 3) . "-" . substr($in, 3, 3) . "-" . substr($in, -4);
}

function Ciudad($id, $transId)
{
	$queryp1 = mysql_query("SELECT * FROM ciudad WHERE id='" . $id . "' LIMIT 1");
	$rowp1 = mysql_fetch_array($queryp1);

	$queryp2 = mysql_query(
		"SELECT * FROM municipio WHERE id='" . $rowp1['id_muni'] . "' LIMIT 1"
	);
	$rowp2 = mysql_fetch_array($queryp2);

	$queryp3 = mysql_query(
		"SELECT * FROM provincia WHERE id='" . $rowp2['id_prov'] . "' LIMIT 1"
	);
	$rowp3 = mysql_fetch_array($queryp3);

	$ciudad = $rowp3['descrip'];
	//Agregar Ciudad desde AgenciaVia
	if (empty($rowp3['descrip'])) {
		$r2id = mysql_query(
			"SELECT id,x_id FROM seguro_transacciones WHERE id='" . $transId . "' LIMIT 1"
		);
		while ($row2id = mysql_fetch_array($r2id)) {
			//array para buscar el nombre de la ciudad
			$agenciCode = substr($row2id['x_id'], 0, 6);
			$r2ciu = mysql_query(
				"SELECT num_agencia,ciudad FROM agencia_via WHERE num_agencia='" . $agenciCode . "' LIMIT 1"
			);
			while ($row2ciu = mysql_fetch_array($r2ciu)) {
				$ciudad = $row2ciu['ciudad'];
			}
		}
	}

	return $ciudad;
}

function Ventas($id)
{
	global $fDesde, $fHasta, $fecha1, $fecha2;

	$wFecha = "fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' AND ";

	$dist_id = $id;

	// --------------------- Index ID ------------------------ //
	$qIndex = mysql_query(
		"SELECT id_inicio FROM indexa WHERE fecha ='" . $fDesde . "' "
	);
	$Index = mysql_fetch_array($qIndex);
	if ($Index['id_inicio']) {
		$wIndexId = "(id > " . $Index['id_inicio'] . ") AND ";
	}
	// -------------------------------------------------------

	//PARA LOS REVERSOS
	$qR = mysql_query("SELECT id_trans FROM seguro_transacciones_reversos ");
	while ($rev = mysql_fetch_array($qR)) {
		$reversadas .= "[" . $rev['id_trans'] . "]";
	}

	$html .=
		'<table cellpadding="4" cellspacing="0">
  	
	<tr>
		<td colspan="21"> 
		
		
		
		<table width="100%" cellpadding="9" cellspacing="0">
	<tr>
    	<td colspan="8">
		
		<b style="font-size: 67px; color: #d9261c;">Multi</b><b style="font-size: 67px; color: #828282;">Seguros 
			</b>	
			</td>
    	
   
	  <td align="center" colspan="13">
		  <font style="font-size: 24px; color: #828282; font-weight: bold;">
		  	<b>REPORTE DIARIO DE VENTAS</b>
		  </font>
		  
		  <br>
		  <font style="font-size: 18px; color: #828282; font-weight: bold;">
		  	' .
		NomAseg($dist_id) .
		'
		  <font><br>
		  <font style="font-size: 14px; color: #828282; font-weight: bold;">
		  	<b>Desde:</b> ' .
		$fecha1 .
		' <b>Hasta:</b> ' .
		$fecha2 .
		'
			</font>
	  </td>
  </tr>
	
</table>


		</td>
	</tr>
	
	
   <tr style="background-color:#B1070A; color:#FFFFFF; font-size:14px;">
   		<td></td>
        <td>No. Poliza</td>
        <td>Nombres</td>
        <td>Apellidos</td>
        <td>C&eacute;dula</td>
        <td>Direcci&oacute;n</td>
        <td>Ciudad</td>
        <td>Tel&eacute;fono</td>
        <td>Tipo</td>
        <td>Marca</td>
        <td>Modelo</td>
        <td>A&ntilde;o</td>
        <td>Chassis</td>
        <td>Placa</td>
        <td>Fecha Emisi&oacute;n</td>
        <td>Inicio Vigencia</td>
        <td>Fin Vigencia</td>
        <td>DPA</td>
		<td>AP</td>
        <td>RC</td>
        <td>RC2</td>
        <td>FJ</td>
        <td>Prima</td>
   </tr> ';

	$quer1 = mysql_query(
		"SELECT * FROM seguro_transacciones WHERE $wFecha id_aseg='" .
			$dist_id .
			"' order by id ASC"
	);
	while ($u = mysql_fetch_array($quer1)) {
		if (!substr_count($reversadas, "[" . $u['id'] . "]") > 0) {
			$t++;
			$id = $u['id'];

			$RepMontoSeguro = RepMontoSeguro($u['id']);
			$veh = explode("|", Vehiculo($u['id_vehiculo']));
			$ServMonto = RepMontoServdos($u['id'], $u['serv_adc']);
			//$precio 	 		= RepMontoSeguro($u['id']) + $ServMonto;
			$precio = $RepMontoSeguro + $ServMonto;
			$Tprecio += $precio;
			$precio = formatDinero($precio);

			$tipo = explode("|", RepTipo($veh[0]));
			$Nombretipo = $tipo[0];

			/*$SerOpcioal =  explode("-", $u['serv_adc']);
		for ($i = 0; $i < count($SerOpcioal); $i++) {
			//echo "ID SERV A REVISAR".$SerOpcioal[$i]."<br>";
			if ($SerOpcioal[$i] > 0)
				$val 	=  explode("|", Validar($SerOpcioal[$i]));;
			$dpa_1 	= $val[0];
			$ap_1	= $val[1];
			$rc_1	= $val[2];
			$rc2_1	= $val[3];
			$fj_1 	= $val[4];
		}*/

			$dd = explode("|", VerVariable($u['serv_adc']));
			$dpa_1 = $dd[0];
			$ap_1 = $dd[1];
			$rc_1 = $dd[2];
			$rc2_1 = $dd[3];
			$fj_1 = $dd[4];

			if ($dpa_1 > 0) {
				$dpa = substr(formatDinero($dpa_1), 0, -3);
			} else {
				$dpa = substr(formatDinero($tipo[1]), 0, -3);
			}

			if ($ap_1 > 0) {
				$ap = substr(formatDinero($ap_1), 0, -3);
			} else {
				$ap = substr(formatDinero($tipo[2]), 0, -3);
			}

			if ($rc_1 > 0) {
				$rc = substr(formatDinero($rc_1), 0, -3);
			} else {
				$rc = substr(formatDinero($tipo[3]), 0, -3);
			}

			if ($rc2_1 > 0) {
				$rc2 = substr(formatDinero($rc2_1), 0, -3);
			} else {
				$rc2 = substr(formatDinero($tipo[4]), 0, -3);
			}

			if ($fj_1 > 0) {
				$fj = substr(formatDinero($fj_1), 0, -3);
			} else {
				$fj = substr(formatDinero($tipo[5]), 0, -3);
			}

			$marca = Marcas($veh[1]);
			$modelo = Modelos($veh[2]);
			$cliente = explode("|", Clientes($u['id_cliente']));
			$pref = GetPrefijo($u['id_aseg']);
			$idseg = str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
			$prefi = $pref . "-" . $idseg;

			$cedula = str_replace("-", "", $cliente[2]);
			$in = $cedula;
			$cedula =
				substr($in, 0, 3) . "-" . substr($in, 3, -1) . "-" . substr($in, -1);

			$html .=
				'<tr>
   		<td><b>' .
				$t .
				'</td>
        <td>' .
				$prefi .
				'</td>
        <td>' .
				$cliente[0] .
				'</td>
        <td>' .
				$cliente[1] .
				'</td>
        <td>' .
				$cedula .
				'</td>
        <td>' .
				$cliente[3] .
				'</td>
        <td>' .
				Ciudad($cliente[4], $id) .
				'</td>
        <td>' .
				Telefono($cliente[5]) .
				'</td>
        <td>' .
				$Nombretipo .
				'</td>
        <td>' .
				$marca .
				'</td>
        <td>' .
				$modelo .
				'</td>
        <td>' .
				$veh[3] .
				'</td>
        <td>' .
				$veh[5] .
				'</td>
        <td align="right">' .
				$veh[4] .
				'</td>
        <td align="right">' .
				$u['fecha'] .
				'</td>
        <td align="right">' .
				FechaReporte($u['fecha_inicio']) .
				'</td>
        <td align="right">' .
				FechaReporte($u['fecha_fin']) .
				'</td>
        <td>' .
				$dpa .
				'</td>
        <td>' .
				$ap .
				'</td>
        <td>' .
				$rc .
				'</td>
        <td>' .
				$rc2 .
				'</td>
		<td>' .
				$fj .
				'</td>
        <td align="right">' .
				$precio .
				'</td>
   </tr>';
		}
	}

	$html .=
		'<tr>
	<td colspan="17"></td>
	<td colspan="4"><h4>Total de primas</h4></td>
	<td><h4>' .
		formatDinero($Tprecio) .
		'</h4></td>
</tr>';
	$html .= '</table>';

	$carpeta = 'Excel/ASEGURADORA/' . $dist_id . '';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}

	$sfile = "Excel/ASEGURADORA/" . $dist_id . "/MS_RDV_$fDesde.xls"; // Ruta del archivo a generar

	$fp = fopen($sfile, "w");

	fwrite($fp, $html);
	fclose($fp);

	return $html;
}

$aseguradorasQuery = mysql_query("SELECT 
    id,
    id_dist,
    nombre,
    prefijo,
    id_suplid,
    logo_color,
    logo_mono,
    fecha,
    activo
FROM
    seguros
WHERE
    activo = 'si'");

$ventas = "";

while ($aseguradoraRow = mysql_fetch_array($aseguradorasQuery)) {
	$sqaw = mysql_query(
		"SELECT * FROM seguro_transacciones WHERE id_aseg='" .
			$aseguradoraRow['id'] .
			"' AND $wFecha2  order by id desc limit 1"
	);
	$paw = mysql_fetch_array($sqaw);

	if ($paw['id']) {
		echo "<br>Ventas:[" .
			$paw['id_aseg'] .
			"] " .
			Ventas($paw['id_aseg']) .
			"<br>";
	}
}
