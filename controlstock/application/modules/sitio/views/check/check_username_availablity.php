<?php
sleep(1);
include('../configuration.php');
include('../conexiones.php');
$conn = conMYSQL();
//include('dbcon.php');'".strtolower($username)."'
if ($_REQUEST) {
    $mail = $_REQUEST['mail'];
    $query = "select * from clientes where mail = '" . strtolower($mail) . "'";
    $results = mysql_query($query, $conn) or die('ok');

    if (mysql_num_rows(@$results) > 0) // not available
    {
        echo '<div id="Error">Email existente en otro usuario</div>';
    }


} ?>
