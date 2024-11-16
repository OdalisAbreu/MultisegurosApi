<?php
ini_set('display_errors', 0);
set_time_limit(0);
include "../inc/conexion_inc.php";
include "../inc/fechas.func.php";
include "../inc/nombres.func.php";
Conectarse();

// --------------------------------------------
$fecha1 = fecha_despues('' . date('d/m/Y') . '', -1);
//$fecha1 = '30/09/2019';
// --------------------------------------------
$fecha2 = fecha_despues('' . date('d/m/Y') . '', -1);
// -------------------------------------------
//$fecha2 = '29/10/2019';

$fd1 = explode('/', $fecha1);
$fh1 = explode('/', $fecha2);
$fDesde = $fd1[2] . '-' . $fd1[1] . '-' . $fd1[0];
$fHasta = $fh1[2] . '-' . $fh1[1] . '-' . $fh1[0];
$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";

//$wFecha2 = "fecha >= '2023-01-01 00:00:00' AND fecha < '2023-01-09 23:59:59'";


$qR = mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE $wFecha2");
while ($rev = mysql_fetch_array($qR)) {
	$reversadas .= "[" . $rev['id_trans'] . "]";
}

$id		= $_GET['id'];

$esc1 = mysql_query("SELECT * from suplidores WHERE activo ='si' AND reporte ='si' 
	AND id_seguro ='" . $id . "' LIMIT 1");
$resc1 = mysql_fetch_array($esc1);

if ($resc1['tipo'] == 'seg') {
	$aseg = "AND id_serv_adc > '101' ";
} else if ($resc1['tipo'] == 'serv') {
	$aseg = "AND id_serv_adc = '" . $id . "' ";
}



if ($_GET['serv'] == 't') {
	$serv = "";
} else {
	$serv = "AND id_serv_adc = '" . $_GET['serv'] . "' ";
	$nombreServicioOpc = ServAdicHistory($_GET['serv']);
}

$ValServ = ValServ($_GET['serv']);

if ($id != 't') {

	$nombre = TipoSuplidor($id);

	$clase = "0";
	$columna = "10";
	if ($ValServ == '2') {
		$colspan = "5";
	} else {
		$colspan = "4";
	}

	$colspan2 = "4";
	//$calt = "17";
	$calt = "7";
} else {
	$nombre = "TODOS LOS SUPLIDORES";
	$columna = "10";
	$clase = "0";
	if ($ValServ == '2') {
		$colspan = "6";
	} else {
		$colspan = "5";
	}
	$colspan2 = "5";
	$calt = "7";
}

/*
function CiudadRep($id)
{
	$query = mysql_query("SELECT * FROM  seguro_clientes WHERE id='" . $id . "' LIMIT 1");
	$row = mysql_fetch_array($query);

	$queryp1 = mysql_query("SELECT * FROM  ciudad WHERE id='" . $row['ciudad'] . "' LIMIT 1");
	$rowp1 = mysql_fetch_array($queryp1);

	$queryp2 = mysql_query("SELECT * FROM  municipio WHERE id='" . $rowp1['id_muni'] . "' LIMIT 1");
	$rowp2 = mysql_fetch_array($queryp2);

	$queryp3 = mysql_query("SELECT * FROM   provincia WHERE id='" . $rowp2['id_prov'] . "' LIMIT 1");
	$rowp3 = mysql_fetch_array($queryp3);

	return $rowp3['descrip'];
}
*/

function ServOpc($idserv)
{
	$qRz = mysql_query("SELECT * FROM `servicios` WHERE `id` = '" . $idserv . "' LIMIT 1");
	$revz = mysql_fetch_array($qRz);
	return $revz['mod_pref'] . "|" . $revz['cambiar'] . "|" . $revz['dpa'] . "|" . $revz['rc'] . "|" . $revz['rc2'] . "|" . $revz['ap'] . "|" . $revz['fj'] . "";

	//s|n|0|0|0|0|0
	//n|s|0|0|0|0|1000000
}

function DatosTrans($id)
{
  $query = mysql_query(
    "
	SELECT * FROM  seguro_transacciones
	WHERE id = '" .
      $id .
      "' LIMIT 1"
  );
  $row = mysql_fetch_array($query);
  return $row['x_id'] .
    "|" .
    $row['id_poliza'] .
    "|" .
    $row['id_vehiculo'] .
    "|" .
    $row['vigencia_poliza'] .
    "|" .
    $row['id_cliente'] .
    "|" .
    $row['fecha_inicio'] .
    "|" .
    $row['fecha_fin'];
}

$html = '
<table >
	<tr>
      <td colspan="' . $columna . '" >&nbsp;</td>
    </tr>
    
	<tr>
    <td colspan="' . $colspan . '">
		<b style="font-size: 50px; color: #d9261c;">Multi</b><b style="font-size: 50px; color: #828282 !important;">Seguros</b>	
	</td>
      <td colspan="' . $colspan2 . '"  align="center">
      <b style="font-size:23px">REPORTE DE REMESA DE SERVICIO OPCIONALES </b><br>
	  <b style="font-size:18px">' . $nombre . '<br>
      ' . $nombreServicioOpc . '</b>
      <br><b>Desde:</b>&nbsp;&nbsp;' . $fecha1 . '&nbsp;&nbsp;&nbsp;<b>Hasta:</b>&nbsp;&nbsp;' . $fecha2 . '
      </td>
    </tr>
    
  <tr style="font-size:16px; color:#FFFFFF; font-weight:bold">
    <td style="background-color:#B1070A;">#</td>
   
    <td style="background-color:#B1070A;">No.Poliza</td>
    ';

if ($ValServ == '2') {
	$html .= '
    <td style="background-color:#B1070A;">No.Poliza 2</td>
    ';
}
if ($id == 't') {
	$html .= '  
    <td style="background-color:#B1070A;">Aseguradora</td>
  ';
}

$html .= '  
        <td style="background-color:#B1070A;">Nombres</td>
        <td style="background-color:#B1070A;">Apellidos</td>
        <td style="background-color:#B1070A;">C&eacute;dula</td>
        <td style="background-color:#B1070A;">Fecha Emisi&oacute;n</td>
        <td style="background-color:#B1070A;">Inicio Vigencia</td>
        <td style="background-color:#B1070A;">Fin Vigencia</td>
		<td style="background-color:#B1070A;">Total a Remesar</td>
   </tr> 
                     
                      
  ';

$query = mysql_query("
		SELECT * FROM seguro_trans_history 
		WHERE $wFecha2 $aseg AND tipo='serv' AND tipo = 'serv' order by id ASC");
	if (mysql_affected_rows() == 0){
		echo 'No hay registros para este reporte';
		exit;
	}
while ($row = mysql_fetch_array($query)) {

	if ((substr_count($reversadas, "[" . $row['id_trans'] . "]") > 0)) {
	} else {

		$fh1		= explode(' ', $row['fecha']);
		$i++;

		$validar 	= ServOpc($row['id_aseg'], $_GET['serv']);
		$val		= explode('|', $validar);

		/*if($val[0]=='s'){
		 $pref = $val[1];
	  }else{
		$pref = GetPrefijo($row['id_aseg']);  
	  }*/

		$poliza 			= DatosTrans($row['id_trans']);
		$PolVal 			= explode('|', $poliza);
		$x_id 				= $PolVal[0];
		$num_poliza 		= $PolVal[1];
		$id_vehiculo 		= $PolVal[2];
		$vigencia_poliza 	= $PolVal[3];
		$id_cliente 		= $PolVal[4];
		$fecha_inicio 		= $PolVal[5];
		$fecha_fin 		= $PolVal[6];

		$Cliente 	= explode("|", Cliente($id_cliente));
		$Client 	= str_replace("|", "", $Cliente[0]);

		$idseg = str_pad($num_poliza, 6, "0", STR_PAD_LEFT);
		$prefi1 = GetPrefijo($row['id_aseg']) . "-" . $idseg;
		$prefi2 = $val[1] . "-" . $idseg;
		$TipoVeh = explode('/', Tipo($id_vehiculo));

		$TCosto += $row['costo'];

		$html .= '<tr style="font-size:12px; text-align:left">
    <td>' . $i . '</td>
    <td>' . $prefi1 . '</td>';

		if ($ValServ == '2') {
			$html .= '<td>' . $prefi2 . '</td>
    ';
		}

		if ($id == 't') {
			$html .= '<td style=" ' . $clase . '">' . NombreSeguroS($row['id_aseg']) . '</td>';
		}
		$html .= '
    <td>' . $Client . '</td>
    <td>' . $Cliente[1] . '</td>
    <td>' . Cedula($id_cliente) . '</td>
    <td align="center" style="width:150px">' . FechaReporte($row['fecha']) . '</td> 
    <td align="center" style="width:150px">' . FechaReporte($fecha_inicio) . '</td>
     <td align="center" style="width:150px">' . FechaReporte($fecha_fin) . '</td>
    <td align="right">' . formatDinero($row['costo']) . '</td>
</tr>';
	}
}
$html .= ' 
  
<tr>
	<td colspan="' . $calt . '"></td>
	<td  align="right"><h4>Total de primas &nbsp;</h4></td>
	<td><h4>' . formatDinero($TCosto) . '</h4></td>
</tr> 
 
</table>
';


///--------------------------------------------------------------------Guardar el archivo--------------------------------------------------////
$carpeta = 'Excel/SERVICIOS_OPCIONALES/' . $dist_id . '';
if (!file_exists($carpeta)) {
	mkdir($carpeta, 0777, true);
}

$sfile = "Excel/SERVICIOS_OPCIONALES/MS_ID_".$id."_RDSO_$fDesde.xls"; // Ruta del archivo a generar

$fp = fopen($sfile, "w");

fwrite($fp, $html);
fclose($fp);



//------------------------------- Funciones ----------------------------------------------

function ValServ($id)
{
	$query = mysql_query(
		"SELECT * FROM servicios WHERE id ='" . $id . "' LIMIT 1"
	);
	$row = mysql_fetch_array($query);

	if ($row['mod_pref'] == 's') {
		return 2;
	} else {
		return 1;
	}
}

function TipoSuplidor($id)
{
	$r51 = mysql_query(
		"SELECT id, nombre, tipo, id_seguro FROM suplidores WHERE id_seguro = '" .
			$id .
			"' LIMIT 1"
	);
	$row51 = mysql_fetch_array($r51);

	if ($row51['tipo'] == "serv") {
		$nombre = ServAdicHistory($row51['id_seguro']);
	} else {
		$nombre = NombreSeguroS($row51['id_seguro']);
	}
	return $nombre;
}
