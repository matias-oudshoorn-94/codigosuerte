<?php
include('../configuration.php');
include('../conexiones.php');
$conn = conMYSQL();


$dni = $_POST['dni'];
$query = "select dni from consumidor_final where dni='" . $dni . "'";
$results = mysql_query($query, $conn) or die('ok');

    if (mysql_num_rows(@$results) > 0) // Existe el DNI
    {
    	echo "false";

    } 
    else 
    {
    	echo "true";
    }
