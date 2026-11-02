<?php
sleep(1);
include('../configuration.php');
include('../conexiones.php');
$conn = conMYSQL();
//include('dbcon.php');'".strtolower($username)."'
if ($_REQUEST) {
    $claves = $_REQUEST['clave'];
    $claveEncriptada = md5($claves);
    $query = "select * from clientes where clave='" . $claveEncriptada . "'";
    $results = mysql_query($query, $conn) or die('ok');

    if (mysql_num_rows(@$results) > 0) // not available
    {

    } else {
        ?>
        
        <script>alert('Clave actual Inválida');
            actual.focus();</script><?php
    }


} ?>