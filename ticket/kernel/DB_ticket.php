<?php
$table_tickets = 'tickets'; 	//имя таблицы запросов
$table_questions = 'questions'; 	//имя таблицы вопросов
//$msconnect_ticket;
define('DB_TICKET_HOST', DB_HOST);
define('DB_TICKET_USER', DB_USER);
define('DB_TICKET_PASSWORD', DB_PASSWORD);
define('DB_TICKET_NAME', DB_NAME);
define('DB_TICKET_TIME_ZONE',DB_TIME_ZONE);
define('DB_TICKET_CHARSET', DB_CHARSET);
/*function select_db_ticket()
{
	//подключение к серверу БД
	$GLOBALS['msconnect_ticket'] = mysql_connect(DB_TICKET_HOST, DB_TICKET_USER, DB_TICKET_PASSWORD) or die(mysql_error());

	//Устанавливаем часовой пояс
	mysql_query("SET @@session.time_zone = '".DB_TICKET_TIME_ZONE."'",$GLOBALS['msconnect_ticket']) or die(mysql_error());

	//выбор БД
	mysql_select_db(DB_TICKET_NAME,$GLOBALS['msconnect_ticket']) or die(mysql_error());

	//установка набора символов сайта
	mysql_set_charset(SITE_CHARSET,$GLOBALS['msconnect_ticket']) or mysql_query("SET NAMES ".SITE_CHARSET,$GLOBALS['msconnect_ticket']);	//изменяет кодировку и для mysql_real_escape_string() в отличие от: mysql_query("SET NAMES ".SITE_CHARSET);
}
select_db_ticket();
*/
$MODULES['TICKET']['DB'] = new DataStorage(DB_TICKET_HOST, DB_TICKET_USER, DB_TICKET_PASSWORD, DB_TICKET_NAME, DB_TICKET_CHARSET, DB_TICKET_TIME_ZONE);
$ticket->db = &$MODULES['TICKET']['DB'];
?>