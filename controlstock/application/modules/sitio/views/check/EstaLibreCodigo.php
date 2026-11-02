<?php
include('../configuration.php');
include('../conexiones.php');
$conn = conMYSQL();


$codigo = $_POST['codigo_numerico_sin_ceros'];
$query = "select numero_viaje from facturacion where numero_viaje='" . $codigo . "'";
$results = mysql_query($query, $conn) or die('ok');

    if (mysql_num_rows(@$results) > 0) // Existe el codigo
    {
    	echo "false";

    } 
    else 
    {
    	echo "true";
    }
