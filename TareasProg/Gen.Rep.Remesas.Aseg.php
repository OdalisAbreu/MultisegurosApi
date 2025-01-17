<?php
ini_set('display_errors', 1);
set_time_limit(0);
include("../inc/conexion_inc.php");
include("../inc/fechas.func.php");
include("../inc/nombres.func.php");
/** Include PHPExcel */
require_once '../PHPExcel-1.8/Classes/PHPExcel.php';

Conectarse();

function Num($year, $aseg)
{
	$sqlr = mysql_query("SELECT id_aseg,year,num,id FROM remesas 
WHERE year ='" . $year . "' AND id_aseg ='" . $aseg . "' order by id DESC");
	if (!$sqlr) {
		echo mysql_error();
	}
	$prd = mysql_fetch_array($sqlr);

	$year = date("Y");
	if ($prd['id']) {
		return $prd['num'] + 1;
	} else {
		return "1";
	}
	/*if($year == $pr['year']){
return $pr['num'] + 1;
}else{
return $pr['num'] + 1;; 
}*/
}

function Num2($year, $aseg)
{
	$sqlr = mysql_query("SELECT id_aseg,year,num,id FROM remesas 
WHERE year ='" . $year . "' AND id_aseg ='" . $aseg . "' order by id DESC");
	if (!$sqlr) {
		echo mysql_error();
	}

	$pr = mysql_fetch_array($sqlr);

	$year = date("Y");

	if ($pr['id']) {
		return $pr['num'] + 1;
	} else {
		return "1";
	}

	/*if($year == $pr['year']){
return $pr['num'] + 1;
}else{
return $pr['num'] + 1; 
}*/
}

function IfExisteCorte($conf)
{
	$sql = mysql_query("SELECT id 
						FROM remesas
						WHERE
							fecha_desde >='" . $conf['desde'] . " 00:00:00' 
						AND 
							fecha_hasta <= '" . $conf['hasta'] . " 23:59:59'
						AND 
							id_aseg = '" . $conf['aseg'] . "' 
						AND year='" . $conf['year'] . "' ");
	if (!$sql) {
		echo mysql_error();
	}
	$p = mysql_fetch_array($sql);

	if ($p['id']) {
		return true;
	} else {
		return false;
	}
}

function Vehiculo($id)
{
	$query = mysql_query("SELECT id,veh_tipo FROM seguro_vehiculo
WHERE id='" . $id . "' LIMIT 1");
	if (!$query) {
		echo mysql_error();
	}
	$row = mysql_fetch_array($query);
	return $row['veh_tipo'];
}

function Clientes($id)
{
	$query = mysql_query("SELECT * 
							FROM seguro_clientes 
							WHERE id='" . $id . "' LIMIT 1");
	if (!$query) {
		echo mysql_error();
	}
	$row = mysql_fetch_array($query);
	return $row['asegurado_nombres'] . "|" . $row['asegurado_apellidos'] . "|" . $row['asegurado_cedula'];
}




function GetPrefijo2($id)
{
	$queryp = mysql_query("SELECT * FROM seguros WHERE id='" . $id . "' LIMIT 1");
	if (!$queryp) {
		echo mysql_error();
	}
	$rowp = mysql_fetch_array($queryp);
	return $rowp['prefijo'];
}

///-------------------------------------------------
function CedulaNew($id)
{
	$cedula = str_replace("-", "", $id);
	$in = $cedula;
	return substr($in, 0, 3) . "-" . substr($in, 3, -1) . "-" . substr($in, -1);
}

///-------------------------------------------------



function Fecha($id)
{
	$clear1 = explode(' ', $id);
	$fecha_vigente1 = explode('-', $clear1[0]);
	return $Vard = $fecha_vigente1[2] . ' - ' . $fecha_vigente1[1] . ' - ' . $fecha_vigente1[0];
}


function FechaHor($id)
{
	$clear1 = explode(' ', $id);
	$fecha_vigente1 = explode('-', $clear1[0]);
	$fecha_vigente2 = explode(':', $clear1[1]);
	$hora = $fecha_vigente2[0] . ":" . $fecha_vigente2[1];
	return $Vard = $fecha_vigente1[2] . ' - ' . $fecha_vigente1[1] . ' - ' . $fecha_vigente1[0] . " " . $hora;
}

function Ventas($id)
{
	$yeawr = date("Y");

	$dist_id = $id;

	// --------------------------------------------
	if ($_GET['fecha1']) {
		$fecha1 = $_GET['fecha1'];
	} else {
		$fecha1 = fecha_despues('' . date('d/m/Y') . '', -7);
	}
	// --------------------------------------------
	if ($_GET['fecha2']) {
		$fecha2 = $_GET['fecha2'];
	} else {
		$fecha2 = fecha_despues('' . date('d/m/Y') . '', -1);
	}
	// -------------------------------------------

	$fd1 = explode('/', $fecha1);
	$fh1 = explode('/', $fecha2);
	$fDesde = $fd1[2] . '-' . $fd1[1] . '-' . $fd1[0];
	$fHasta = $fh1[2] . '-' . $fh1[1] . '-' . $fh1[0];


	$wFecha = "fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' AND ";

	// --------------------- Index ID ------------------------ //
	$qIndex = mysql_query("SELECT id_inicio FROM indexa WHERE fecha ='" . $fDesde . "' ");
	if (!$qIndex) {
		echo mysql_error();
	}
	$Index = mysql_fetch_array($qIndex);
	if ($Index['id_inicio']) {
		$wIndexId = "(id > " . $Index['id_inicio'] . ") AND ";
	}
	// -------------------------------------------------------

	//PARA LOS REVERSOS
	$qRc = mysql_query("SELECT * FROM seguro_transacciones_reversos ");
	if (!$qRc) {
		echo mysql_error();
	}
	while ($revc = mysql_fetch_array($qRc)) {
		$reversadas .= "[" . $revc['id_trans'] . "]";
	}
	$fecha_ = explode('/', $fecha1);
	$a = str_pad(Num2($yeawr, $_GET['id_aseg']), 4, "0", STR_PAD_LEFT);


	$html = '<table cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="11"> 
						<table width="100%" cellpadding="9" cellspacing="0">
							<tr>
								<td colspan="4">
									<b style="font-size: 35px; color: #d9261c;">Multi</b><b style="font-size: 35px; color: #828282;">Seguros</b>
								</td>
								<td align="center" colspan="4">
									<font style="font-size: 19px; color: #828282; font-weight: bold;">
										<b>REPORTE DE REMESA</b>
									</font>
									<br>
									<font style="font-size: 16px; color: #828282; font-weight: bold;">'
		. NomAseg($dist_id) .
		'<font>
									<br>
									<font style="font-size: 14px; color: #828282; font-weight: bold;">
										<b>Desde:</b> ' . $fecha1 . ' <b>Hasta:</b> ' . $fecha2 . '
									</font>
								</td>
								<td colspan="3" align="right">
									Remesa No. ' . Sigla($_GET['id_aseg']) . '-' . date("Y") . '-' . $a . '
									<br><b>Fecha de Impresi&oacute;n:</b><br>
									' . Fecha(date("Y-m-d")) . '
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
				<td>Fecha Emisi&oacute;n</td>
				<td>Inicio Vigencia</td>
				<td>Fin Vigencia</td>
				<td>Prima</td>
			<!--	<td>Comisi&oacute;n</td> -->
				<td>Total a Remesar</td>
			</tr> ';

	$CostoServ = 0;
	$MontoServ = 0;
	$CostoSeguro = 0;
	$MontoSeguro = 0;
	$Total_Costos = 0;
	$Total_Montos = 0;
	$quer1 = mysql_query("SELECT * FROM seguro_transacciones WHERE $wFecha id_aseg='" . $dist_id . "'order by id ASC");
	if (!$quer1) {
		echo mysql_error();
	}
	//echo "<b>CONSULTA:</b>SELECT * FROM seguro_transacciones WHERE $wFecha id_aseg='".$dist_id."' order by id DESC<br>";
	while ($u = mysql_fetch_array($quer1)) {
		//echo "id ".$u['id']."<br>";

		if ((substr_count($reversadas, "[" . $u['id'] . "]") > 0)) {
		} else {

			$t++;


			//DATOS DEL VEHICULO
			$tipo_vehiculo = Vehiculo($u['id_vehiculo']);

			$MontoServ = RepMontoServdos($u['id'], $u['serv_adc']);
			$CostoServ = RepMontoServCosto($u['id'], $u['serv_adc']);

			$CostoSeguro = CostoSeguroRemes($u['id']); // saco los 193.97 del seguro del motor
			$MontoSeguro = PrecioSeguroRemes($u['id']); // saco el monto del seguro vendido 375

			$Total_Costos = $CostoServ + $CostoSeguro; // SUMA DE LOS COSTOS ES TOTAL A REMESAR
			$Total_Montos = $MontoServ + $MontoSeguro; // SUMA DE LOS VENTA DEL SEGURO Y EL SERVICOI ES LA PRIMA
			$Comision = $Total_Montos - $Total_Costos;

			$Tremesar += $Total_Costos;
			$Tprima += $Total_Montos;
			$Tcomision += $Comision;



			$cliente = explode("|", Clientes($u['id_cliente']));
			$pref = GetPrefijo2($u['id_aseg']);
			$idseg = str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
			$prefi = $pref . "-" . $idseg;

			$html .= '<tr>
						<td>' . $t . '</td>
						<td>' . $prefi . '</td>
						<td>' . $cliente[0] . '</td>
						<td>' . $cliente[1] . '</td>
						<td>' . CedulaNew($cliente[2]) . '</td>
						<td>' . FechaHor($u['fecha']) . '</td>
						<td>' . Fecha($u['fecha_inicio']) . '</td>
						<td>' . Fecha($u['fecha_fin']) . '</td>
						<td>' . formatDinero($Total_Montos) . '</td>
				<!--		<td>' . formatDinero($Comision) . '</td> -->
						<td>' . formatDinero($Total_Costos) . '</td>
					</tr>';
		}
	}

	$html .= '<tr style="font-size:14px; font-weight:bold">
				<td colspan="8" align="right"></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr style="font-size:14px; font-weight:bold">
				<td colspan="9" align="right"> Total Primas:</td>
				<td>$' . formatDinero($Tprima) . '</td>
				<td></td>
			</tr>
			<tr style="font-size:14px; font-weight:bold">
				<td colspan="9" align="right"> Total Comision:</td>
				<td>$' . formatDinero($Tcomision) . '</td>
				<td></td>
			</tr>
			<tr style="font-size:14px; font-weight:bold">
				<td colspan="9" align="right"> Total Remesas:</td>
				<td>$' . formatDinero($Tremesar) . '</td>
				<td></td>
			</tr>
		</table>';

	$carpeta = 'Excel/ASEGURADORA/REMESAS/' . $dist_id . '';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}

	//$sfile= "Archivos/CLIENTES/Transacciones_$fDesde.xls"; // Ruta del archivo a generar 
	$sfile = "Excel/ASEGURADORA/REMESAS/" . $dist_id . "/MS_RDR_$fDesde.xls"; // Ruta del archivo a generar 

	$fp = fopen($sfile, "w");

	fwrite($fp, $html);
	fclose($fp);
	// try {

	// 	$inputFileType = 'Excel5';
	// 	$inputFileName = "Excel/ASEGURADORA/REMESAS/" . $dist_id . "/MS_RDR_$fDesde.xls";
	// 	$outputFileType = 'Excel2007';
	// 	$outputFileName = "Excel/ASEGURADORA/REMESAS/" . $dist_id . "/MS_RDR_$fDesde.xlsx";

	// 	$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
	// 	$objPHPExcel = $objPHPExcelReader->load($inputFileName);

	// 	$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $outputFileType);
	// 	$objPHPExcel = $objPHPExcelWriter->save($outputFileName);
	// 	//code...
	// } catch (Exception $e) {
	// 	echo $e->getMessage();
	// }
	echo $html;

	//echo "Remesa: ".$Tremesar."<br>";

	if ($Tremesar > 0) {
		// CONSULTANDO SI YA EXISTE EL RESUMEN:

		// ...


		//echo "hay comision";
		//$year = date("Y");
		//$year = "2018";
		//echo "Year: ".$year."<br>";
		//$yearcc = date("Y");

		$year = date("Y");
		//echo "fecha desde: ".$fDesde." 00:00:00<br>";
		//echo "fecha desde: ".$fHasta." 23:59:59<br>";
		//echo "Fecha Gen: ".date("Y-m-d H:i:s")."<br>";
		//echo "Aseguradora: ".$dist_id."<br>";
		//echo "Monto: ".$tRemesa."<br>";

		if (!IfExisteCorte($c = array('desde' => $fDesde, 'hasta' => $fHasta, 'aseg' => $dist_id, 'year' => $year))) {
			//$year = "2018";

			$num = Num($year, $dist_id);
			mysql_query("INSERT INTO remesas (id_dist,id_aseg,year,num,monto,fecha_desde,fecha_hasta,fecha_gen,pago,tipo_serv) 
			VALUES
			('6','" . $dist_id . "','" . $year . "','" . $num . "','" . $Tremesar . "','" . $fDesde . " 00:00:00" . "','" . $fHasta . " 23:59:59" . "','" . date("Y-m-d H:i:s") . "','n','seg') ");

			echo "00/GUARDANDO...";
		} else {
			echo "15/HAY REGISTROS PARA ESTE DIA";
		}
	}
}
try {

	$sqaw = mysql_query("SELECT * FROM seguro_transacciones WHERE id_aseg='" . $_GET['id_aseg'] . "' order by id desc limit 1");
	if (!$sqaw) {
		echo mysql_error();
	}
	$paw = mysql_fetch_array($sqaw);

	if ($paw['id']) {
		Ventas($paw['id_aseg']);
	}
	//code...
} catch (\Throwable $th) {
	echo $th->getMessage();
}
