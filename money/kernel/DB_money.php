<?php
$table_money= 'money'; 	//имя таблицы
$msconnect_money;
//Параметры БД
define('DB_MONEY_HOST', DB_HOST);
define('DB_MONEY_USER', DB_USER);
define('DB_MONEY_PASSWORD', DB_PASSWORD);
define('DB_MONEY_NAME', DB_NAME);
define('DB_MONEY_TIME_ZONE',DB_TIME_ZONE);
define('DB_MONEY_CHARSET', DB_CHARSET);
function select_db_money()
{
	//подключение к серверу БД
	$GLOBALS['msconnect_money'] = mysql_connect(DB_MONEY_HOST, DB_MONEY_USER, DB_MONEY_PASSWORD) or die(mysql_error());

	//Устанавливаем часовой пояс
	mysql_query("SET @@session.time_zone = '".DB_MONEY_TIME_ZONE."'",$GLOBALS['msconnect_money']) or die(mysql_error());

	//выбор БД
	mysql_select_db(DB_MONEY_NAME,$GLOBALS['msconnect_money']) or die(mysql_error());

	//установка набора символов сайта
	mysql_set_charset(SITE_CHARSET,$GLOBALS['msconnect_money']) or mysql_query("SET NAMES ".SITE_CHARSET,$GLOBALS['msconnect_money']);	//изменяет кодировку и для mysql_real_escape_string() в отличие от: mysql_query("SET NAMES ".SITE_CHARSET);
}
select_db_money();

$MODULES['MODEY']['DB'] =  new DataStorage(DB_MONEY_HOST, DB_MONEY_USER, DB_MONEY_PASSWORD, DB_MONEY_NAME, DB_MONEY_CHARSET, DB_MONEY_TIME_ZONE);
$money->db = &$MODULES['MODEY']['DB'];
?>