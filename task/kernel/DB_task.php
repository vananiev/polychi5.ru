<?php
$table_task='task';
$msconnect_task;
//Параметры БД
define('DB_TASK_HOST', DB_HOST);
define('DB_TASK_USER', DB_USER);
define('DB_TASK_PASSWORD', DB_PASSWORD);
define('DB_TASK_NAME', DB_NAME);
define('DB_TASK_TIME_ZONE',DB_TIME_ZONE);
define('DB_TASK_CHARSET', DB_CHARSET);
function select_db_task()
{
	//подключение к серверу БД
	$GLOBALS['msconnect_task'] = mysql_connect(DB_TASK_HOST, DB_TASK_USER, DB_TASK_PASSWORD) or die(mysql_error());

	//Устанавливаем часовой пояс
	mysql_query("SET @@session.time_zone = '".DB_TASK_TIME_ZONE."'",$GLOBALS['msconnect_task']) or die(mysql_error());

	//выбор БД
	mysql_select_db(DB_TASK_NAME,$GLOBALS['msconnect_task']) or die(mysql_error());
	
	//установка набора символов сайта
	mysql_set_charset(SITE_CHARSET,$GLOBALS['msconnect_task']) or mysql_query("SET NAMES ".SITE_CHARSET,$GLOBALS['msconnect_task']);	//изменяет кодировку и для mysql_real_escape_string() в отличие от: mysql_query("SET NAMES ".SITE_CHARSET);
	//SHOW CREATE TABLE tablename
	//show variables like '%char%
	//$res=mysql_query("show variables like '%char%'");
	//print_r(mysql_fetch_assoc($res));
}
select_db_task();

$MODULES['TASK']['DB'] = new DataStorage(DB_TASK_HOST, DB_TASK_USER, DB_TASK_PASSWORD, DB_TASK_NAME, DB_TASK_CHARSET, DB_TASK_TIME_ZONE);
$task->db = &$MODULES['TASK']['DB'];
?>