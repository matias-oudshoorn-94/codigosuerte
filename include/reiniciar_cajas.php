<?php
/*
 * Reabre todas las cajas (caja_cerrada = 2). Pensado para correr una vez por dia
 * como tarea programada (cron) del hosting, a la medianoche de Argentina.
 *
 * Cada corrida queda anotada en application/logs/reiniciar_cajas.log, asi se
 * puede ver si el cron esta corriendo y si fallo. Termina con codigo 1 si falla,
 * para que el hosting lo marque como error.
 */
date_default_timezone_set('America/Argentina/Buenos_Aires');

$log = dirname(__DIR__).'/application/logs/reiniciar_cajas.log';
$anotar = function ($texto) use ($log) {
	@file_put_contents($log, date('Y-m-d H:i:s').' '.$texto.PHP_EOL, FILE_APPEND);
};
$terminar = function ($texto, $ok) use ($anotar) {
	$anotar(($ok ? 'OK ' : 'ERROR ').$texto);
	if (PHP_SAPI !== 'cli' && ! $ok)
	{
		// Por web no se muestran detalles de la base; quedan en el log
		http_response_code(500);
		$texto = 'Fallo al reiniciar las cajas (ver application/logs/reiniciar_cajas.log)';
	}
	echo $texto.PHP_EOL;
	exit($ok ? 0 : 1);
};

try
{
	require __DIR__.'/configuration.php';

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conn = @new mysqli($servername, $username, $password, $dbname, $dbport);
	$conn->query('UPDATE usuarios SET caja_cerrada = 2 WHERE caja_cerrada <> 2');
	$reabiertas = $conn->affected_rows;
	$conn->close();
}
catch (Throwable $e)
{
	$terminar('Fallo al reiniciar las cajas: '.$e->getMessage(), FALSE);
}

$terminar('Se reiniciaron todas las cajas diarias ('.$reabiertas.' estaban cerradas)', TRUE);
