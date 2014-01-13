<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/

//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "ROBOKASSA: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "ROBOKASSA: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		return;
		}

// подключаем БД
require_once("DB_pay.php");

//если нет БД то создаем ее
$ROBOKASSA->db->query("CREATE DATABASE IF NOT EXISTS ".DB_ROBOKASSA_NAME." CHARACTER SET ".DB_ROBOKASSA_CHARSET) or die($ROBOKASSA->db->error());

//создаем тавлицу
$ROBOKASSA->db->query("CREATE TABLE IF NOT EXISTS {$table_robokassa} ( 
	id int(10) unsigned NOT NULL auto_increment,
	id_pokypatelya int(4),
	data_platezha timestamp default CURRENT_TIMESTAMP,
	symma int(10),
	status char(10),
	PRIMARY KEY  (`ID`)
	) CHARSET=utf8") or die($ROBOKASSA->db->error()); 

//-------------------создаем действия -------------------------------------------------------	
$users->db->query("INSERT IGNORE INTO $table_actions (name,description) VALUES('RB_SEE_MON_IN','Разрешает просмотр ввода средств через Robokassa')") or die("Ошибка при регистрации действия RB_SEE_MON_IN.<br>".$users->db->error());

echo "ROBOKASSA: Actions Make ok<br>";
	
//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['ROBOKASSA']['PATH']),
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
echo "ROBOKASSA: папки созданы, права на данные папки выставлены<br>";
	
echo "ROBOKASSA DB Make ok<br>";
echo "ROBOKASSA Setup ok<br>";
?>
