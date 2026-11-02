<?php
sleep(1);
include('dbcon.php');
if ($_REQUEST) {
    $username = $_REQUEST['cajas1'];
    $idpropietario = $_REQUEST['idpropietario'];
    $query = "select * from propietarios where pro_usuario = '" . strtolower($username) . "' and pro_usuario !=''";
    $results = mysql_query($query) or die('ok');

    if (mysql_num_rows(@$results) > 0) // not available
    {
        echo '<div id="Error">Usuario ya existente</div>';
    } else {
        echo '<div id="Success">Disponible</div>';
    }

} ?>
