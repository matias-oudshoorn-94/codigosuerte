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

$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "u245081402_codigosuerte";
$password = getenv('DB_PASS') ?: "hlD@Dh5No^5O";
$dbname = getenv('DB_NAME') ?: "u245081402_codigosuerte";
if (getenv('DB_PORT')) { ini_set('mysqli.default_port', getenv('DB_PORT')); }