<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/
?>
<?php
	// наименьший id пользователя
	define('USERID_START',1000);

	//БД пользователи
	require_once("DB.php");
	//если нет БД то создаем ее
	mysql_query("CREATE DATABASE IF NOT EXISTS ".DB_USER_NAME." CHARACTER SET ".DB_USER_CHARSET,$GLOBALS['msconnect_users']) or die(mysql_error());

//создаем тавлицу
mysql_query("CREATE TABLE IF NOT EXISTS $table_users (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	login varchar(256) NOT NULL DEFAULT '',
	name varchar(256) NOT NULL DEFAULT '',
	surname varchar(256) NOT NULL DEFAULT '',
	password varchar(32) NOT NULL DEFAULT '',
	city varchar (64) NOT NULL DEFAULT '',
	occupation varchar (128) NOT NULL DEFAULT '',
	age int(8),
	mail varchar(64) NOT NULL DEFAULT '',
  	phone char(12) NOT NULL DEFAULT '',
  	notification char(6) DEFAULT NULL,
	connect varchar(512) NOT NULL DEFAULT '',
	reg_date timestamp NOT NULL default '0000-00-00 00:00:00',
	last_visit timestamp NOT NULL default '0000-00-00 00:00:00',
	balance int(8) NOT NULL default 0,
	money_out_query int(10) NOT NULL default 0,
	koshelek char(16) NOT NULL DEFAULT '',
	rating decimal(2,1) NOT NULL default 0,
	groups text NOT NULL,
	authorize varchar(8096) DEFAULT NULL,
	time_zone varchar(16) NOT NULL DEFAULT 'Etc/GMT-4',
	photo varchar(256) DEFAULT NULL,
	dialog_id int unsigned default NULL,
	PRIMARY KEY  (`ID`),
	UNIQUE(login)
	)",$msconnect_users) or die(mysql_error());			//status int(2) unsigned NOT NULL default ".USER.",
// создаем таблицу групп
mysql_query("CREATE TABLE IF NOT EXISTS $table_groups (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	name varchar(32) NOT NULL DEFAULT '',
	action text NOT NULL ,
	menus text NOT NULL,
	description text NOT NULL ,
	PRIMARY KEY  (`ID`),
	UNIQUE(name)
	)",$msconnect_users) or die(mysql_error());
// создаем таблицу действий
mysql_query("CREATE TABLE IF NOT EXISTS $table_actions (
	id int(10) unsigned NOT NULL AUTO_INCREMENT,
	name varchar(32) NOT NULL DEFAULT '',
	description text NOT NULL,
	PRIMARY KEY  (`ID`),
	UNIQUE(`name`)
	)",$msconnect_users) or die(mysql_error());
echo "Users: DB Make ok<br>";

//------------------------------------------ задаем пользователя admin -----------------------------
$password = md5('admin');
$sql = mysql_query("SELECT id FROM $table_users WHERE login='admin'",$msconnect_users) or die(mysql_error());
// если админа нет
if (mysql_num_rows($sql) == 0)
	{
	mysql_query("INSERT INTO $table_users (login,password,reg_date,groups) VALUES('admin','{$password}',CURRENT_TIMESTAMP(),',ADMIN,')", $msconnect_users) or die("Ошибка при регистрации пользователя admin.<br>".mysql_error());
	mysql_query("UPDATE $table_users SET id = 0 WHERE login='admin'", $msconnect_users) or die("Ошибка при регистрации пользователя admin.<br>".mysql_error());
	echo "Пользователь admin создан с паролем: admin<br>";
	}
// задаем число с которого  будет начинаться id
mysql_query("ALTER TABLE $table_users AUTO_INCREMENT=".USERID_START,$msconnect_users) or die(mysql_error());

//создаем действия
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('CHANGE_ACCESS','Разрешает выставлять права группе')", $msconnect_users) or die("Ошибка при регистрации действия CHANGE_ACCESS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('ADD_DEL_GROUP','Разрешает создавать и удалять группы')", $msconnect_users) or die("Ошибка при регистрации действия ADD_GROUP.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('CNG_USER_GROUP','Разрешает изменять список групп пользователя')", $msconnect_users) or die("Ошибка при регистрации действия CNG_USER_GROUP.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('USR_SEE_ALL_INFO','Разрешает видеть полную информацию о пользователе')", $msconnect_users) or die("Ошибка при регистрации действия USR_SEE_ALL_INFO.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('USR_SEE_USR_LIST','Разрешает получать список пользователей')", $msconnect_users) or die("Ошибка при регистрации действия USR_SEE_USR_LIST.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO `$table_actions` (`name`,`description`) VALUES('EDIT_MENUS','Разрешает создавать, редактировать, удалять меню')", $msconnect_users) or die("Ошибка при регистрации действия EDIT_MENUS.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO `$table_actions` (`name`,`description`) VALUES('SUDO','Разрешает просматривыать страницы от лица другого пользователя')", $msconnect_users) or die("Ошибка при регистрации действия SUDO.<br>".mysql_error());


echo "Users: Actions Make ok<br>";

//создаем группы
mysql_query("INSERT IGNORE INTO $table_groups (name,action,description) VALUES('ADMIN','','Администратор')", $msconnect_users) or die("Ошибка при регистрации группы ADMIN.<br>".mysql_error());
mysql_query("UPDATE $table_groups SET id = 0 WHERE name='ADMIN'", $msconnect_users);// or die("Ошибка при регистрации группы ADMIN(2).<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_groups (name,action,description) VALUES('BANED','','Забаненные')", $msconnect_users) or die("Ошибка при регистрации группы BANED.<br>".mysql_error());

echo "Users: Groups Make ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	//на запись
	$dirs = array (
		'EXEC'=>array(),
		'WRITE'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['USERS']['PATH'], AVATAR_ROOT)
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
	$dir=MODULES_MEDIA.'/'.$INCLUDE_MODULES['USERS']['PATH']; if(@chmod($dir,FOLDER_EXEC)==false)echo("Права (".decoct(FOLDER_EXEC).") на папку не выставлены: $dir. Устанновите их вручную<br>");
echo "Users: папки созданы, права на данные папки выставлены<br>";

echo "Users Setup ok<br>";
?>
