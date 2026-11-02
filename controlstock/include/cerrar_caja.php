<?php 

include("configuration.php");
include("funciones.php");


//print_r($_POST);

$fecha = date('Y-m-d');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error) 
{
   die("Falló la conexión: " . $conn->connect_error);
}

$sql = "INSERT INTO cajas_diarias (id_usuario, fecha, cantidad_ventas, total_efectivo, total_tarjeta, total_medio_electronico, total, impresiones_reales, impresiones_aprox, desperdicio) VALUES ('".$_POST['codigo_cliente']."', '".$fecha."' , '".$_POST['cantidad_ventas']."', '".$_POST['efectivo']."', '".$_POST['tarjeta']."', '".$_POST['electronico']."', '".$_POST['total']."', '".$_POST['impresiones_reales']."', '".$_POST['impresiones_aprox']."', '".$_POST['desperdicio']."') ";

//echo $sql;

$conn->query($sql);


$sql2 = "UPDATE usuarios SET caja_cerrada = 1 WHERE idusuario = '".$_POST['codigo_cliente']."'"; //Cierro la caja de hoy del usuario

//echo $sql;

$conn->query($sql2);

?>
    <script type="text/javascript">
		window.location="http://codigosuerte.com.ar/caja_cerrada";
	</script>
<?php

/*
if ($conn->query($sql) === TRUE)
{
    ?>
    <script type="text/javascript">
		window.location="http://codigosuerte.com.ar/caja_cerrada";
	</script>
    <?php
} 
else 
{
    echo "Hubo un error, por favor comunicarse con el administrador. Muchas gracias"
}



?>