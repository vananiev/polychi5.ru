<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/
?>
<?php
	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "TICKET: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}

	// Подключаем БД
	require_once("DB_ticket.php"); 
	//если нет БД то создаем ее
	mysql_query("CREATE DATABASE IF NOT EXISTS ".DB_TICKET_NAME." CHARACTER SET ".DB_TICKET_CHARSET,$GLOBALS['msconnect_ticket']) or die(mysql_error());

//создаем тавлицу запросов
mysql_query("CREATE TABLE IF NOT EXISTS `$table_tickets` (
	id int(5) unsigned NOT NULL auto_increment,
	headline varchar(256),
	from_id int(5) signed,
	from_mail varchar(32),
	password varchar(32) NOT NULL default '',
	to_id int(5) signed,
	thematic enum ('MONEY','HOW','OTHER','HIDDEN'),
	reg_date timestamp NOT NULL default '0000-00-00 00:00:00',
	last_visit timestamp NOT NULL default '0000-00-00 00:00:00',
	status enum ('NEW','ANSWERED','OPENED','CLOSED') NOT NULL default 'NEW',
	PRIMARY KEY  (`ID`)
	)",$msconnect_ticket) or die(mysql_error());
// to_id == -1 для всех доступный тикет
	
//создаем тавлицу общения
mysql_query("CREATE TABLE IF NOT EXISTS  `$table_questions` (
	id int(5) unsigned NOT NULL auto_increment,
	ticket_id int(5) unsigned,
	from_id int(5) signed,
	to_id int(5) signed,
	text varchar(1024),
	file enum ('NO','YES') NOT NULL default 'NO',
	date timestamp NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`ID`)
	)",$msconnect_ticket) or die(mysql_error());
//при наличии файла, оно сохраняется как НОМЕР_ОТВЕТА.rar (принимаются лишь архивы .rar)
echo "Tickets DB Make ok<br>";

//------------------- Отзывы ----------------------------------------------------------------
mysql_query("INSERT INTO $table_tickets (headline,from_id,to_id,`thematic`,reg_date) VALUES('Отзывы',-1, 0, 'HOW',CURRENT_TIMESTAMP)", $msconnect_ticket) or die("Ошибка при создании тикета отзывов.<br>".mysql_error());
mysql_query("UPDATE $table_tickets SET id = 0 WHERE headline='Отзывы'", $msconnect_ticket);

echo "Tickets: Создан тикет для отзывов<br>";

//-------------------создаем действия -------------------------------------------------------	
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TKT_SUPPORT_ANS','Разрешает давать ответы на запросы пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TKT_SUPPORT_ANS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TKT_CNG_STATUS','Разрешает изменять статус Всех тикетов')", $msconnect_users) or die("Ошибка при регистрации действия TKT_CNG_STATUS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TKT_DEL_MODF_MSG','Разрешает удалять и изменять чужие сообщения')", $msconnect_users) or die("Ошибка при регистрации действия TKT_DEL_MODF_MSG.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TKT_DEL_OTHERS_TICKET','Разрешает удалять чужие тикеты')", $msconnect_users) or die("Ошибка при регистрации действия TKT_DEL_OTHERS_TICKET.<br>".mysql_error());

echo "Tickets: Actions Make ok<br>";

//создаем группы
mysql_query("INSERT IGNORE INTO $table_groups (name,action,description) VALUES('SUPPORT','','Служба поддержки')", $msconnect_users) or die("Ошибка при регистрации группы SUPPORT.<br>".mysql_error());
	
echo "Users: Groups Make ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	//на запись
	$dirs = array (
		'WRITE'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['TICKET']['PATH'], TICKET_FILE),
		'EXEC'=>array()
		);
	foreach($dirs['WRITE'] as $dir)
		{
		if(@mkdir($dir,FOLDER_WRITE)==false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_WRITE)==false)echo("Права (".decoct(FOLDER_WRITE).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
	foreach($dirs['EXEC'] as $dir)
		{
		if(@mkdir($dir,FOLDER_EXEC)==false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_EXEC)==false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
	$dir=MODULES_MEDIA.'/'.$INCLUDE_MODULES['TICKET']['PATH']; if(@chmod($dir,FOLDER_EXEC)==false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
echo "Ticket: папки созданы, права на данные папки выставлены<br>";

echo "Ticket Setup ok<br>";
?>