<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/
?>
<?php
/*status =
GETS - get solver выбор решающего
NEW - добавлен не решен
WAIT - в процессе решения
SOLV - решен, но принимаются претензии к решению
REMK - (remake) переделать
OK - решен, претензии к решению не принимаются
NSOL - не решен, срок решения истек
LOCK - заблокирован за нарушения
*/
	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "TASK: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "TASK: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		return;
		}
	// подключаем БД заданий
	require_once("DB_task.php");
	
	//если нет БД то создаем ее
	mysql_query("CREATE DATABASE IF NOT EXISTS ".DB_TASK_NAME." CHARACTER SET ".DB_TASK_CHARSET ,$GLOBALS['msconnect_task']) or die(mysql_error());

//создаем тавлицу
mysql_query("CREATE TABLE IF NOT EXISTS  $table_task (
	id int(5) unsigned NOT NULL auto_increment,
	user int(5),
	solver int(5),
	select_solver varchar(512) NOT NULL default '',
	section varchar(255),
	subsection varchar(255),
	add_date timestamp NOT NULL default '0000-00-00 00:00:00',
	resolve_until timestamp NOT NULL default '0000-00-00 00:00:00',
	agree_date timestamp NOT NULL default '0000-00-00 00:00:00',
	solv_date timestamp NOT NULL default '0000-00-00 00:00:00',
	status varchar(4) NOT NULL default 'NEW',
	user_pay bool NOT NULL default 0,
	solver_get_pay bool NOT NULL default 0,
	price int(10) default 30,
	rating int(1) unsigned NOT NULL default 5,
	dialog_id int unsigned default NULL,
	PRIMARY KEY  (`ID`)
	)",$msconnect_task) or die(mysql_error());

// задаем число с которого  будет начинаться id
mysql_query("ALTER TABLE $table_task AUTO_INCREMENT=1235",$msconnect_task) or die(mysql_error());

echo "Task: DB Make ok<br>";

//-------------------создаем действия -------------------------------------------------------
$solver_access = ',';
$user_access = ',';
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_MK_OK','Разрешает выставлять заданию статус выполнен и выполнить оплату Автору')", $msconnect_users) or die("Ошибка при регистрации действия TSK_MK_OK.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_SHTRAV','Разрешает штрафовать Авторов за задания, выполненные не в срок')", $msconnect_users) or die("Ошибка при регистрации действия TSK_SHTRAV.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_SEE_OTHERS_TASK','Разрешает просматривать задания других пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TSK_SEE_OTHERS_TASK.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_DEL_OTHERS_TASK','Разрешает удалять задания других пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TSK_DEL_OTHERS_TASK.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_CNG_STATUS','Разрешает изменять статус заданий (своих и чужих)')", $msconnect_users) or die("Ошибка при регистрации действия TSK_CNG_STATUS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_SOLVING','Разрешает давать подавать заявки на решения и решать задания')", $msconnect_users) or die("Ошибка при регистрации действия TSK_SOLVING.<br>".mysql_error());
if(mysql_insert_id($msconnect_task)!=0) $solver_access .= mysql_insert_id($msconnect_users).",";
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_SOLVING_OTHERS','Разрешает решать Все задания, даже если не вы являетесь решающим')", $msconnect_users) or die("Ошибка при регистрации действия TSK_SOLVING_OTHERS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_RATING_OTHERS','Разрешает просматривать Все решения без платы и оценивать качество решения заданий, других пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TSK_RATING_OTHERS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_PRICING_OTHERS','Разрешает оценивать стоимость заданий, других пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TSK_PRICING_OTHERS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('TSK_SELECT_SOLVER_OTHERS','Разрешает выбирать решающего заданий, других пользователей')", $msconnect_users) or die("Ошибка при регистрации действия TSK_SELECT_SOLVER_OTHERS.<br>".mysql_error());

echo "Task: Actions Make ok<br>";

//-------------------Создаем группы ---------------------------------------------------------
mysql_query("INSERT IGNORE INTO $table_groups (name,action,description) VALUES('USER','".$user_access."','Студенты')", $msconnect_users) or die("Ошибка при регистрации группы USER.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_groups (name,action,description) VALUES('SOLVER','".$solver_access."','Авторы')", $msconnect_users) or die("Ошибка при регистрации группы SOLVER.<br>".mysql_error());

echo "Task: Groups Make ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'WRITE'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['TASK']['PATH'], TASK_ROOT, SOLVING_ROOT, OPENED_SOLVING_ROOT),
		'EXEC'=>array()
		);
	foreach($dirs['WRITE'] as $dir)
		{
		if(@mkdir($dir,FOLDER_WRITE)===false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_WRITE)===false)echo("Права (".decoct(FOLDER_WRITE).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
	foreach($dirs['EXEC'] as $dir)
		{
		if(@mkdir($dir,FOLDER_EXEC)===false)echo("Ошибка создания папки: $dir. Возможно она уже существует<br>");
		if(@chmod($dir,FOLDER_EXEC)===false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
		//if(@chown($dir,$OWN)==false) echo("Ошибка смены владельца на $OWN для папки: $dir<br>");;
		}
	$dir=MODULES_MEDIA.'/'.$INCLUDE_MODULES['TASK']['PATH']; if(@chmod($dir,FOLDER_EXEC)==false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
echo "Task: папки созданы, права на данные папки выставлены<br>";
	
echo "Task Setup ok<br>";
?>