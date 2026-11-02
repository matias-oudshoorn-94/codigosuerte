<?php 
define('DEBUG', true);

if(DEBUG == true):
    ini_set('display_errors', 'On');
    error_reporting(E_ALL ^ E_DEPRECATED);
else:
    ini_set('display_errors', 'Off');
    error_reporting(0);
endif;

$path = $_SERVER['DOCUMENT_ROOT'].'/';
$live_site = 'http://codigosuerte.com.ar/';

$servername = "localhost";
$username = "c1890072_pdfadmi";
$password = "goMOsopi09";
$dbname = "c1890072_pdfadmi";