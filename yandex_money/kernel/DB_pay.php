<?php
/****************************************************************************************
	Конфигурация БД и системы вебмани
****************************************************************************************/

$table_ym_pay = 'yandex_money';
//Параметры БД
define('DB_YANDEX_MONEY_HOST', DB_HOST);
define('DB_YANDEX_MONEY_USER', DB_USER);
define('DB_YANDEX_MONEY_PASSWORD', DB_PASSWORD);
define('DB_YANDEX_MONEY_NAME', DB_NAME);
define('DB_YANDEX_MONEY_TIME_ZONE',DB_TIME_ZONE);
define('DB_YANDEX_MONEY_CHARSET', DB_CHARSET);

$MODULES['YANDEX_MONEY']['DB'] = new DataStorage(DB_YANDEX_MONEY_HOST, DB_YANDEX_MONEY_USER, DB_YANDEX_MONEY_PASSWORD, DB_YANDEX_MONEY_NAME, DB_YANDEX_MONEY_CHARSET, DB_YANDEX_MONEY_TIME_ZONE);

$yandex_money->db = &$MODULES['YANDEX_MONEY']['DB'];
?>