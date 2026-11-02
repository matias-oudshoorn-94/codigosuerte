<?php
//error_reporting(E_ALL); //Para que no salte el error 500 
//ini_set('display_errors', 'On');

include_once('OCA/oca.php');

$testoca = new Oca('20-32923598-0', '372747');

//print_r($_POST);

$result = $testoca->getHtmlDeEtiquetasPorOrdenOrNumeroEnvio($_POST['orden'],$_POST['envio']);

$array = json_decode(json_encode((array)simplexml_load_string($result)),true);

print_r($array[0]);


?>