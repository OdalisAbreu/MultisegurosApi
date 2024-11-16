<?php
ini_set('display_errors', 1);
set_time_limit(0);
include("../../../PHPMailerAutoload.php");
include("../../../../../inc/conexion_inc.php");
include("../../../../../inc/nombres.func.php");
include("../../../../../inc/fechas.func.php");
Conectarse();

//--------------------Modifica las fechas para tener el rango del día----------------
$fechaw = fecha_despues('' . date('d/m/Y') . '', -1);
$edaw = explode('/', $fechaw);
$fechasdaw = $edaw[2] . "-" . $edaw[1] . "-" . $edaw[0];
$wFecha2 = "fecha >= '$fechasdaw 00:00:00' AND fecha <= '$fechasdaw 23:59:59' ";

$idProveedor = $_GET['id']; //---Captura el Id del proveedor  

if ($p['id_serv_adc'] > 101){
	$sq = mysql_query("SELECT * FROM seguro_trans_history where $wFecha2 and id_serv_adc > 101");
}else{
	$sq = mysql_query("SELECT * FROM seguro_trans_history where $wFecha2 and id_serv_adc = $idProveedor");
}
$numFilas = mysql_num_rows($sq);
if($numFilas > 0){
	enviarEmailHtml($idProveedor);
	exit;
}else{
	echo 'No hay reportes para enviar';
}

//$sq = mysql_query("SELECT * FROM multiseg_2.seguro_trans_history where fecha >= '2023-01-01 00:00:00' AND fecha <= '2023-01-08 23:59:59' and id_serv_adc > 0");


	
	/*if ($paw['id']) {
		enviarEmailHtml($p['id']);
	}*/


//-------------------------- Funcion para enviar Email-----------------------------------

function enviarEmailHtml($dist_id)
{

	//explode
	$fech = fecha_despues('' . date('d/m/Y') . '', -1);
	$ed = explode('/', $fech);
	$fechasd = $ed[2] . "-" . $ed[1] . "-" . $ed[0];
	$fecha1 = $ed[0] . "-" . $ed[1] . "-" . $ed[2];

/*	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'multiseguros.com.do';
	$mail->SMTPAuth = true;
	$mail->Username = 'operaciones@multiseguros.com.do';
	$mail->Password = '@x43RMcKh9@L';
	$mail->SMTPSecure = 'ssl';
	$mail->From = 'operaciones@multiseguros.com.do';
	$mail->FromName = 'MultiSeguros';
	$mail->Port = '465';
	$mail->SMTPDebug = true;
*/
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'mail.segurosexpress.com';
$mail->SMTPAuth = true;
$mail->Username = 'operaciones@segurosexpress.com';
$mail->Password = 'oCgYS@7yIaOO';
$mail->SMTPSecure = 'ssl';
$mail->From = 'operaciones@segurosexpress.com';
$mail->FromName = 'MultiSeguros';
$mail->Port = '465';
$mail->SMTPDebug = true;

	$query = mysql_query("SELECT * FROM suplidores WHERE id_seguro ='" . $dist_id . "' LIMIT 1");
	$row = mysql_fetch_array($query);

	$desg = explode(",", $row['email_finanzas']);
	$cant = count($desg);
	$cant = $cant - 1;
	if ($_GET['DEBUG']) {
		echo "DEBUG";
	} else {
		for ($i = 0; $i <= $cant; $i++) {
			$mail->AddAddress("" . $desg[$i] . "", "");
		}
	}
    //$mail->addAddress('odalisdabreu@gmail.com');
	$mail->addAddress('grullon.jose@gmail.com');
	$mail->AddBCC('odalisdabreu@gmail.com');

	/*if($dist_id=='1'){
		$carpeta = 'DOM';
	}else if($dist_id=='3'){
		$carpeta = 'GEN';
	}*/

	$archivo1 = '/Excel/SERVICIOS_OPCIONALES/MS_ID_'.$dist_id.'_RDSO_'.$fechasd.'.xls';
	//$archivo2 = "/excelFiles/$dist_id/MS_EM_$fechasd.xlsx";
	//echo "$archivo1,$archivo2";

	$archivo1 = realpath(__DIR__.'/../../../../') . $archivo1;
	//$archivo2 = realpath(__DIR__ . '/../../../') . $archivo2;

	/*if (!file_exists($archivo1) && !file_exists($archivo2)) {
		return;
	}*/

	$mail->AddAttachment($archivo1);
	//$mail->AddAttachment($archivo2);

	$mail->WordWrap = 50;
	$mail->Subject = 'Ventas de ' . NomAseg($dist_id) . ' del ' . $fecha1 . ' ';
	$mail->Body    = 'para ver el mensaje necesita HTML.';
	$mail->IsHTML(true);


	//$mail->SMTPDebug  = 2;
	$mail->MsgHTML(
		"Buenos d&iacute;as, el archivo de las ventas de los servicios esta anexado.
		<p>
		--------------------------------------------------------------------------------
<br /><br />
Este mensaje puede contener informaci&oacute;n privilegiada y confidencial. Dicha informaci&oacute;n es exclusivamente para el uso del individuo o entidad al cual es enviada. Si el lector de este mensaje no es el destinatario del mismo, queda formalmente notificado que cualquier divulgaci&oacute;n, distribuci&oacute;n, reproducci&oacute;n o copiado de esta comunicaci&oacute;n est&aacute; estrictamente prohibido. Si este es el caso, favor de eliminar el mensaje de su computadora e informar al emisor a trav&eacute;s de un mensaje de respuesta. Las opiniones expresadas en este mensaje son propias del autor y no necesariamente coinciden con las de MultiSeguros.<br />
<br />
<br />
Gracias.<br />
<br />
<br />
 MultiSeguros"
	);

	if (!$mail->send()) {
		echo "15/error enviando/15";
	} else {
		echo realpath(__FILE__) . "00/mensaje enviado/00";
	}
} 



