<?php
/****************************************************************************************
	Конфигурация БД и системы вебмани
****************************************************************************************/

$table_pay;
$msconnect_pay;
//Параметры БД
define('DB_WEBMONEY_HOST', DB_HOST);
define('DB_WEBMONEY_USER', DB_USER);
define('DB_WEBMONEY_PASSWORD', DB_PASSWORD);
define('DB_WEBMONEY_NAME', DB_NAME);
define('DB_WEBMONEY_TIME_ZONE',DB_TIME_ZONE);
define('DB_WEBMONEY_CHARSET', DB_CHARSET);
function select_db_pay()
{
	$GLOBALS['table_pay'] ="webmoney";	//имя таблицы

	//подключение к серверу БД
	$GLOBALS['msconnect_pay'] = mysql_connect(DB_WEBMONEY_HOST, DB_WEBMONEY_USER, DB_WEBMONEY_PASSWORD) or die(mysql_error());

	//Устанавливаем часовой пояс
	mysql_query("SET @@session.time_zone = '".DB_WEBMONEY_TIME_ZONE."'",$GLOBALS['msconnect_pay']) or die(mysql_error());

	//выбор БД
	mysql_select_db(DB_WEBMONEY_NAME,$GLOBALS['msconnect_pay']) or die(mysql_error());

	//установка набора символов сайта
	mysql_set_charset(SITE_CHARSET,$GLOBALS['msconnect_pay']) or mysql_query("SET NAMES ".SITE_CHARSET,$GLOBALS['msconnect_pay']);
	//SHOW CREATE TABLE tablename
	//show variables like '%char%
	//$res=mysql_query("show variables like '%char%'");
	//print_r(mysql_fetch_assoc($res));
}
select_db_pay();

$MODULES['WEBMONEY']['DB'] = new DataStorage(DB_WEBMONEY_HOST, DB_WEBMONEY_USER, DB_WEBMONEY_PASSWORD, DB_WEBMONEY_NAME, DB_WEBMONEY_CHARSET, DB_WEBMONEY_TIME_ZONE);
$webmoney->db = &$MODULES['WEBMONEY']['DB'];
?>