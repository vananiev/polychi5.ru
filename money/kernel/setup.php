<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/
?>
<?php
	//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "MONEY: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}

	// Подключаем БД
	require_once("DB_money.php");
	
	//если нет БД то создаем ее
	mysql_query("CREATE DATABASE IF NOT EXISTS ".DB_MONEY_NAME." CHARACTER SET ".DB_MONEY_CHARSET ,$GLOBALS['msconnect_money']) or die(mysql_error());

	//создаем тавлицу
	mysql_query("CREATE TABLE IF NOT EXISTS $table_money (
		id int(11) unsigned NOT NULL auto_increment,
		ot int(11) default NULL ,
		komy int(11)  default NULL,
		give int(11) default NULL,
		get int(11) default NULL,
		commission int(11) default NULL,
		date timestamp NOT NULL default '0000-00-00 00:00:00',
		description varchar(256) default NULL,
		PRIMARY KEY  (`ID`)
		)",$msconnect_money) or die(mysql_error());
	echo "Money DB Make ok<br>";
	
//-------------------создаем действия -------------------------------------------------------	
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('MNY_SEE_SYS_BAL','Разрешает просмотр баланса системы')", $msconnect_users) or die("Ошибка при регистрации действия MNY_SEE_SYS_BAL.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('MNY_CNG_USR_BAL','Разрешает изменять баланс пользователя')", $msconnect_users) or die("Ошибка при регистрации действия MNY_CNG_USR_BAL.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('MNY_SYS_HISTORY','Разрешает просмотр истории счетов системы и пользователей')", $msconnect_users) or die("Ошибка при регистрации действия MNY_SYS_HISTORY.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('MNY_SYS_TRANSFER','Разрешает осуществление переводов средств между системой и пользователем')", $msconnect_users) or die("Ошибка при регистрации действия MNY_TRANSFER.<br>".mysql_error());
mysql_query("INSERT IGNORE INTO $table_actions (name,description) VALUES('MNY_OUT',			'Разрешает выводить средства из системы')", $msconnect_users) or die("Ошибка при регистрации действия MNY_OUT.<br>".mysql_error());

echo "Money: Actions Make ok<br>";

//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['MONEY']['PATH']),
		'WRITE'=>array()
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
	echo "Money: папки созданы, права на данные папки выставлены<br>";
	
	echo "Money Setup ok<br>";
?>