<?php
include('../configuration.php');
include('../conexiones.php');
$conn = conMYSQL();


$email = $_POST['email'];
$query = "select email from consumidor_final where email='" . $email . "'";
$results = mysql_query($query, $conn) or die('ok');

    if (mysql_num_rows(@$results) > 0) // Existe el DNI
    {
    	echo "false";

    } 
    else 
    {
    	echo "true";
    }
