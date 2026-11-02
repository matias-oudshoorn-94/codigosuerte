<?php
//define('DB_SERVER', 'localhost');
define('DB_SERVER_USERNAME', 'root');
//define('DB_SERVER_USERNAME', 'axislabd_asg');
define('DB_SERVER_PASSWORD', '');
//define('DB_SERVER_PASSWORD', 'HTO7v&slk4Jv');
define('DB_DATABASE', 'asgmanagement');
//define('DB_DATABASE', 'axislabd_asg');

$conexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE, $conexion);
?>
