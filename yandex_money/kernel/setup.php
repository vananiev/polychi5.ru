<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/

//Проверка зависимостей
	if(!defined('MODULE_USERS'))
		{
		echo "YANDEX_MONEY: Для правильной работы необходима установка модуля ".MODULE_USERS."<br>";
		return;
		}
	if(!defined('MODULE_MONEY'))
		{
		echo "YANDEX_MONEY: Для правильной работы необходима установка модуля ".MODULE_MONEY."<br>";
		return;
		}

// подключаем БД yandex money
require_once("DB_pay.php");

//если нет БД то создаем ее
$yandex_money->db->query("CREATE DATABASE IF NOT EXISTS ".DB_YANDEX_MONEY_NAME." CHARACTER SET ".DB_YANDEX_MONEY_CHARSET) or die($yandex_money->db->error());

//создаем тавлицу
$yandex_money->db->query("CREATE TABLE IF NOT EXISTS {$table_ym_pay} ( 
	id int(10) unsigned NOT NULL auto_increment,
	id_pokypatelya int(4),
	data_platezha timestamp default CURRENT_TIMESTAMP,
	ot char(16),
	komy char(16),
	symma int(10),
	paymant_id varchar(32),
	status varchar(255),
	PRIMARY KEY  (`ID`)
	) CHARSET=utf8") or die($yandex_money->db->error()); 

//-------------------создаем действия -------------------------------------------------------	
$users->db->query("INSERT IGNORE INTO $table_actions (name,description) VALUES('YM_SEE_MON_IN','Разрешает просмотр ввода средств через Яндекс.Деньги')") or die("Ошибка при регистрации действия YM_SEE_MON_IN.<br>".$users->db->error());

echo "Yandex_Money: Actions Make ok<br>";
	
//-------------------Создаем необходимые папки, Выставляем права на папки и файлы сайта -----
	//$OWN="apache";
	$dirs = array (
		'EXEC'=>array(MODULES_MEDIA.'/'.$INCLUDE_MODULES['YANDEX_MONEY']['PATH']),
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
echo "Yandex_Money: папки созданы, права на данные папки выставлены<br>";
	
echo "Yandex_Money DB Make ok<br>";
echo "Yandex_Money Setup ok<br>";
?>