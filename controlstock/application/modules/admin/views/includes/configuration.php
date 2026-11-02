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
$live_site = 'http://pdfcolor.com.ar/demo/';

$servername = "localhost";
$username = "c1381514_pdf";
$password = "60naMOtegu";
$dbname = "c1381514_pdf";