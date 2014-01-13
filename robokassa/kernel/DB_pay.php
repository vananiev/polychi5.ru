<?php
/****************************************************************************************
	Конфигурация БД и системы robokassa
****************************************************************************************/

$table_robokassa = 'robokassa';

//Параметры БД
define('DB_ROBOKASSA_HOST', DB_HOST);
define('DB_ROBOKASSA_USER', DB_USER);
define('DB_ROBOKASSA_PASSWORD', DB_PASSWORD);
define('DB_ROBOKASSA_NAME', DB_NAME);
define('DB_ROBOKASSA_TIME_ZONE',DB_TIME_ZONE);
define('DB_ROBOKASSA_CHARSET', DB_CHARSET);


$MODULES['ROBOKASSA']['DB'] = new DataStorage(DB_ROBOKASSA_HOST, DB_ROBOKASSA_USER, DB_ROBOKASSA_PASSWORD, DB_ROBOKASSA_NAME, DB_ROBOKASSA_CHARSET, DB_ROBOKASSA_TIME_ZONE);
$ROBOKASSA->db = &$MODULES['ROBOKASSA']['DB'];
?>
