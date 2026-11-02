<?php 

include("configuration.php");
include("funciones.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error) 
{
   die("Falló la conexión: " . $conn->connect_error);
}

$sql = "UPDATE usuarios SET caja_cerrada = 2";

$conn->query($sql);

echo "Se reiniciaron todas las cajas diarias";


?>