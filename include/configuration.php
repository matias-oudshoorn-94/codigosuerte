<?php
/*
 * Datos de conexion para scripts sueltos (fuera de CodeIgniter).
 * Se leen de application/config/database.php para tener una sola fuente.
 */
defined('BASEPATH') OR define('BASEPATH', dirname(__DIR__).'/system/');
defined('ENVIRONMENT') OR define('ENVIRONMENT', getenv('CI_ENV') ?: 'production');

include dirname(__DIR__).'/application/config/database.php';

$servername = $db['default']['hostname'];
$username   = $db['default']['username'];
$password   = $db['default']['password'];
$dbname     = $db['default']['database'];
$dbport     = $db['default']['port'] !== '' ? (int) $db['default']['port'] : NULL;
