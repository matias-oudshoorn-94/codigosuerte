<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Conexion a la base de datos
| -------------------------------------------------------------------
| En desarrollo local (CI_ENV=development) se pueden sobrescribir los
| datos con variables de entorno: DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME.
*/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'      => '',
	'hostname' => getenv('DB_HOST') ?: 'localhost',
	'port'     => getenv('DB_PORT') ?: '',
	'username' => getenv('DB_USER') ?: 'u245081402_codigosuerte',
	'password' => getenv('DB_PASS') ?: 'hlD@Dh5No^5O',
	'database' => getenv('DB_NAME') ?: 'u245081402_codigosuerte',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt'  => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => (ENVIRONMENT !== 'production'),
);
