<?php
	/***************************************************************************

					Установка модуля

	**************************************************************************/

//если нет БД то создаем ее
$db->query("CREATE DATABASE IF NOT EXISTS ".DB_NAME." CHARACTER SET ".DB_CHARSET) or die($db->error);

// создаем таблицу блоков ссылок
$db->query("CREATE TABLE IF NOT EXISTS $table_linkblocks (
	  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	  `name` VARCHAR(64) NOT NULL DEFAULT '',
	  `MODULE` TEXT NOT NULL,
	  `FILE` TEXT NOT NULL,
	  `ARGS` TEXT NOT NULL,
	  `PAGE` TEXT NOT NULL,
	  `TEGS` TEXT NOT NULL,
	  `description` TEXT NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE(`name`)
	  )") or die($db->error);
echo "Engine: DB Make ok<br>";	

//создаем действия, если используется модель 'USERS'
if(isset($INCLUDE_MODULES['USERS']) || isset($SETUP_MODULES['USERS']))
	{
	if(isset($SETUP_MODULES['USERS'])) unset($SETUP_MODULES['USERS']);
	// вызываем установку модуля-пользователей
	require_once(SCRIPT_ROOT."/".$INCLUDE_MODULES['USERS']['PATH']."/kernel/setup.php");
	// второй раз уже не устанавливаем
	unset($SETUP_MODULES['USERS']);
	mysql_query("INSERT IGNORE INTO `$table_actions` (`name`,`description`) VALUES('EDIT_LINKBLOCKS','Разрешает работать с блоками ссылок')", $msconnect_users) or die("Ошибка при регистрации действия EDIT_LINKBLOCKS.<br>".mysql_error());
	echo "Engine: Actions Make ok<br>";
	}

echo "Engine: Setup ok<br>";
?>