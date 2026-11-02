<?php

include("configuration.php");
include("funciones.php");

// Rechazar llamadas sin datos (acceso directo por URL, bots, recargas):
// antes generaban una caja diaria vacía.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['codigo_cliente']) || (int)$_POST['codigo_cliente'] <= 0)
{
    http_response_code(400);
    exit('Solicitud inválida');
}

// Devuelve el valor numérico del POST, o NULL si viene vacío o no es un número.
function valor_numerico($campo)
{
    if (isset($_POST[$campo]) && is_numeric($_POST[$campo]))
    {
        return $_POST[$campo];
    }
    return null;
}

$id_usuario         = (int)$_POST['codigo_cliente'];
$fecha              = date('Y-m-d');
$cantidad_ventas    = valor_numerico('cantidad_ventas');
$efectivo           = valor_numerico('efectivo');
$tarjeta            = valor_numerico('tarjeta');
$electronico        = valor_numerico('electronico');
$transferencia      = valor_numerico('transferencia');
$total              = valor_numerico('total');
$impresiones_reales = valor_numerico('impresiones_reales');
$impresiones_aprox  = valor_numerico('impresiones_aprox');
$desperdicio        = valor_numerico('desperdicio');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error)
{
   die("Falló la conexión: " . $conn->connect_error);
}

// Las ventas ya crean la caja del día (shopping_model::send).
// Si existe, se completa esa fila en lugar de crear otra.
$id_caja = null;
$stmt = $conn->prepare("SELECT id FROM cajas_diarias WHERE id_usuario = ? AND fecha = ? ORDER BY id ASC LIMIT 1");
$stmt->bind_param('is', $id_usuario, $fecha);
$stmt->execute();
$stmt->bind_result($id_encontrado);
if ($stmt->fetch())
{
    $id_caja = $id_encontrado;
}
$stmt->close();

if ($id_caja !== null)
{
    $stmt = $conn->prepare("UPDATE cajas_diarias SET cantidad_ventas = ?, total_efectivo = ?, total_tarjeta = ?, total_medio_electronico = ?, total_transferencia = ?, total = ?, impresiones_reales = ?, impresiones_aprox = ?, desperdicio = ? WHERE id = ?");
    $stmt->bind_param('sssssssssi', $cantidad_ventas, $efectivo, $tarjeta, $electronico, $transferencia, $total, $impresiones_reales, $impresiones_aprox, $desperdicio, $id_caja);
}
else
{
    // ON DUPLICATE KEY evita duplicados si el índice único (id_usuario, fecha) está creado.
    $stmt = $conn->prepare("INSERT INTO cajas_diarias (id_usuario, fecha, cantidad_ventas, total_efectivo, total_tarjeta, total_medio_electronico, total_transferencia, total, impresiones_reales, impresiones_aprox, desperdicio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE cantidad_ventas = VALUES(cantidad_ventas), total_efectivo = VALUES(total_efectivo), total_tarjeta = VALUES(total_tarjeta), total_medio_electronico = VALUES(total_medio_electronico), total_transferencia = VALUES(total_transferencia), total = VALUES(total), impresiones_reales = VALUES(impresiones_reales), impresiones_aprox = VALUES(impresiones_aprox), desperdicio = VALUES(desperdicio)");
    $stmt->bind_param('issssssssss', $id_usuario, $fecha, $cantidad_ventas, $efectivo, $tarjeta, $electronico, $transferencia, $total, $impresiones_reales, $impresiones_aprox, $desperdicio);
}

if (!$stmt->execute())
{
    echo "Hubo un error al cerrar la caja, por favor comunicarse con el administrador. Muchas gracias";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

//Cierro la caja de hoy del usuario
$stmt = $conn->prepare("UPDATE usuarios SET caja_cerrada = 1 WHERE idusuario = ?");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$stmt->close();

$conn->close();

?>
    <script type="text/javascript">
		window.location="http://codigosuerte.com.ar/caja_cerrada";
	</script>
