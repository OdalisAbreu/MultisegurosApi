<?
	// by LinksDominicana.com
	define("WS_DIR","ws6_3_8");
	define("WS_VER","WS.6.8");
	define("LOGS",1);
	define("EXEDTIME",22);
	define("LIMIT_REV",20000);
	
	// CODIGOS DE RESPUESTA
	$conf['Cod_resp'] = array(
		'00' => 'Transaccion Completada',
		'11' => 'No tiene balance disponible',
		'12' => 'Numero de telefono invalido',
		'13' => 'El monto es menor a la cantidad permitida',
		'14' => 'Usuario y/o Password incorrectos',
		'15' => 'Problemas de comunicacion con el webservice',
		'16' => 'Transaccion duplicada',
		'17' => 'Error en parametros',
		'18' => 'Usuario inactivo'
	); 
?>