<?php
/*
 * Reabre todas las cajas (caja_cerrada = 2). Pensado para correr una vez por dia
 * como tarea programada del hosting.
 */
include __DIR__.'/configuration.php';

$conn = new mysqli($servername, $username, $password, $dbname, $dbport);
if ($conn->connect_error)
{
	http_response_code(500);
	exit('Falló la conexión');
}

$conn->query('UPDATE usuarios SET caja_cerrada = 2');
$conn->close();

echo 'Se reiniciaron todas las cajas diarias';
