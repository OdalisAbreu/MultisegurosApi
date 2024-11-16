<?
ini_set('display_errors', 1);
set_time_limit(0);
include("../../inc/conexion_inc.php");
Conectarse();
$days = $_GET['days'];
$daysPlusOne = $days + 1;
$query = "SELECT DISTINCT
                tran.id,
                tran.fecha,
                tran.id_cliente,
                cli.asegurado_nombres,
                cli.asegurado_apellidos,
                cli.asegurado_telefono1,
                seg.nombre aseguradora,
                CONCAT(seg.prefijo,
                        '-',
                        LPAD(tran.id_poliza, 6, '0')) poliza,
                tran.fecha_inicio,
                tran.fecha_fin,
                tran.vigencia_poliza,
                tran.id_vehiculo,
                tran.user_id,
                agen.razon_social agencia
            FROM
                seguro_transacciones tran
                    INNER JOIN
                seguro_clientes cli ON cli.id = tran.id_cliente
                    INNER JOIN
                seguros seg ON seg.id = tran.id_aseg
                    INNER JOIN
                agencia_via agen ON agen.num_agencia = SUBSTRING_INDEX(tran.x_id, '-', 1)
            WHERE
                tran.user_id = '20' AND tran.user_id != ''
                    AND tran.fecha_fin >= DATE_ADD(CURRENT_DATE(),
                    INTERVAL $days DAY)
                    AND tran.fecha_fin <= DATE_ADD(CURRENT_DATE(),
                    INTERVAL $daysPlusOne DAY)";

$queryExecutor = mysql_query($query);

if (!$queryExecutor) {
    echo mysql_error();
}

while ($dataReader = mysql_fetch_assoc($queryExecutor)) {
    $data[] = $dataReader;
}
header("Content-Type: application/json");
echo json_encode($data);
