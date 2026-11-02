<?php
require_once('../nusoap-0.9.5/lib/nusoap.php');

$endpoint = "http://www.makerservicios.com.ar:5556/registracion.asmx?wsdl";
$action = "RegistracionPSR/EstaLibreNombreUsuario";
$mynamespace = "RegistracionPSR";
$client = new nusoap_client($endpoint, 'wsdl');

//var_dump($_POST);
$param = array('usuario' => $_POST['usuario']);
$aux = $client->call('EstaLibreNombreUsuario', array('parameters' => $param), '', '', false, true);
$result = '';
// Check for a fault
if ($client->fault) $resut = '<h2>Fault</h2><pre></pre>';
else {
    // Check for errors
    $err = $client->getError();
    if ($err) $result = '<h2>Error</h2><pre>' . $err . '</pre>';
    else $result = $aux['EstaLibreNombreUsuarioResult'];
}
//var_dump($result);
if ($result == 'S')
    echo 'true';
else echo 'false';
