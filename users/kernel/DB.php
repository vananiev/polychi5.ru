<?php
$table_users = 'users'; 	//имя таблицы
$table_groups = 'groups'; 	//имя таблицы
$table_actions = 'actions'; 	//имя таблицы
$table_linkblocks = 'linkblocks';	// блоки ссылок
$msconnect_users;
//Параметры БД
define('DB_USER_HOST', DB_HOST);
define('DB_USER_USER', DB_USER);
define('DB_USER_PASSWORD', DB_PASSWORD);
define('DB_USER_NAME', DB_NAME);
define('DB_USER_TIME_ZONE',DB_TIME_ZONE);
define('DB_USER_CHARSET', DB_CHARSET);
function select_db_user()
{
	//подключение к серверу БД
	$GLOBALS['msconnect_users'] = mysql_connect(DB_USER_HOST, DB_USER_USER, DB_USER_PASSWORD) or die(mysql_error());

	//Устанавливаем часовой пояс
	mysql_query("SET @@session.time_zone = '".DB_USER_TIME_ZONE."'",$GLOBALS['msconnect_users']) or die(mysql_error());

	//выбор БД
	mysql_select_db(DB_USER_NAME,$GLOBALS['msconnect_users']) or die(mysql_error());

	//установка набора символов сайта
	mysql_set_charset(SITE_CHARSET,$GLOBALS['msconnect_users']) or mysql_query("SET NAMES ".SITE_CHARSET,$GLOBALS['msconnect_users']);	//изменяет кодировку и для mysql_real_escape_string() в отличие от: mysql_query("SET NAMES ".SITE_CHARSET);
}
select_db_user();

$MODULES['USERS']['DB'] = new DataStorage(DB_USER_HOST, DB_USER_USER, DB_USER_PASSWORD, DB_USER_NAME, DB_USER_CHARSET, DB_USER_TIME_ZONE);
$users->db = &$MODULES['USERS']['DB'];
?>